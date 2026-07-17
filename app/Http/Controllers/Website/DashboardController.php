<?php

namespace App\Http\Controllers\Website;

use App\Models\Certificate;
use App\Models\Feedback;
use App\Models\Poll;
use App\Models\UserAttendance;
use App\Models\UserPollAnswer;
use App\Models\Comment;
use App\Models\CommentVote;
use App\Events\CommentUpdated;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Support\EventStorage;

class DashboardController
{
    private $event = null;

    public function __construct()
    {
        $this->event = app('event');
    }

    public function dashboard()
    {
        $polls = Poll::activeVisibleLatest()->CurrentEvent()->get();
        $activeCertificate = Certificate::CurrentEvent()->Active()->first();
        $feedback = Feedback::where('user_id', Auth::guard('web')->id())
            ->where('event_id', $this->event->id ?? null)
            ->first();
        $resources = $this->event->resources()->orderBy('slot')->get();

        return view('website.dashboard', [
            'polls' => $polls,
            'active_certificate' => $activeCertificate,
            'feedback' => $feedback,
            'resources' => $resources,
            'is_log_attendance' => $this->event->is_log_attendance ?? false,
            'enable_live_chat' => (bool) $this->event->enable_live_chat,
            'enable_comments' => (bool) $this->event->enable_comments,
            'enable_polls' => (bool) $this->event->enable_polls,
            'enable_feedback' => (bool) $this->event->enable_feedback,
            'title' => __('Dashboard'),
        ]);
    }

    public function downloadResource(Request $request)
    {
        $resourceId = $request->route('resourceId');
        $resource = $this->event->resources()->findOrFail($resourceId);

        abort_unless(EventStorage::exists($resource->file_path), 404);

        return response(EventStorage::contents($resource->file_path), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="' . addslashes($resource->original_name) . '"',
        ]);
    }

    public function attendanceJoin(Request $request)
    {
        try {

            if (!$this->event->is_log_attendance) {
                return response()->json(['success' => false], 400);
            }

            if (!$this->checkAttendanceConditions()) {
                return response()->json(['success' => false, 'message' => 'Outside event window'], 400);
            }

            $userId = Auth::guard('web')->id();
            $now = Carbon::now();

            $attendance = DB::transaction(function () use ($userId, $now) {
                // Lock the user row so simultaneous heartbeat requests cannot
                // add the same seconds twice or create duplicate attendance.
                \App\Models\User::whereKey($userId)->lockForUpdate()->firstOrFail();

                $attendance = UserAttendance::firstOrCreate(
                    ['user_id' => $userId],
                    ['joined_at' => $now, 'last_ping_at' => null, 'session_time' => 0]
                );

                if ($attendance->last_ping_at) {
                    $elapsed = Carbon::parse($attendance->last_ping_at)->diffInSeconds($now);

                    // Heartbeats run every 30 seconds. A larger gap means the
                    // previous browser session disappeared without a leave ping.
                    if ($elapsed > 0 && $elapsed <= 90) {
                        $attendance->session_time += $elapsed;
                    }
                }

                $attendance->last_ping_at = $now;
                $attendance->save();

                return $attendance;
            });

            return response()->json([
                'success' => true,
                'session_time' => $attendance->session_time,
            ]);

        } catch (\Exception $e) {
            Log::error('Attendance Join: ' . $e->getMessage());
            return response()->json(['success' => false], 500);
        }
    }

    public function attendanceLeave(Request $request)
    {
        try {
            $userId = Auth::guard('web')->id();
            $now = Carbon::now();

            DB::transaction(function () use ($userId, $now) {
                \App\Models\User::whereKey($userId)->lockForUpdate()->first();
                $attendance = UserAttendance::where('user_id', $userId)->first();

                if (!$attendance || !$attendance->last_ping_at) return;

                $elapsed = Carbon::parse($attendance->last_ping_at)->diffInSeconds($now);
                if ($elapsed > 0 && $elapsed <= 90) {
                    $attendance->session_time += $elapsed;
                }

                // Null marks the session closed, so time away is never counted
                // when this user comes back to the event later.
                $attendance->last_ping_at = null;
                $attendance->save();
            });

            return response()->json(['success' => true]);

        } catch (\Exception $e) {
            Log::error('Attendance Leave: ' . $e->getMessage());
            return response()->json(['success' => false], 500);
        }
    }

    public function feedbackSave(Request $request)
    {
        abort_unless($this->event->enable_feedback, 403);
        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string',
        ]);

        $feedback = Feedback::firstOrNew([
            'user_id' => auth()->guard('web')->id(),
            'event_id' => $this->event->id ?? null,
        ]);
        $feedback->rating = $request->rating;
        $feedback->comment = $request->filled('comment') ? $request->comment : null;
        $feedback->save();

        return response()->json([
            'status' => true,
            'message' => 'Feedback saved successfully',
            'feedback' => [
                'rating' => $feedback->rating,
                'comment' => $feedback->comment,
            ],
        ]);
    }

    public function commentSave(Request $request)
    {
        abort_unless($this->event->enable_comments, 403);
        $validated = $request->validate(['comment' => ['required', 'string', 'max:2000']]);
        $comment = Comment::create([
            'event_id' => $this->event->id,
            'user_id' => Auth::guard('web')->id(),
            'comment' => $validated['comment'],
            'is_approved' => false,
        ]);
        return response()->json([
            'status' => true,
            'message' => 'Comment submitted and is waiting for approval.',
            'comment' => $comment,
        ]);
    }

    public function comments()
    {
        abort_unless($this->event->enable_comments, 403);
        $userId = Auth::guard('web')->id();
        $comments = Comment::query()
            ->where('event_id', $this->event->id)
            ->where(function ($query) use ($userId) {
                $query->where('is_approved', true)
                    ->orWhere(fn ($own) => $own->where('user_id', $userId)->where('is_approved', false));
            })
            ->with('user:id,name,first_name,last_name')
            ->withCount('votes')
            ->withExists(['votes as voted_by_me' => fn ($query) => $query->where('user_id', $userId)])
            ->orderByDesc('votes_count')
            ->orderByDesc('created_at')
            ->get()
            ->map(fn (Comment $comment) => $this->commentPayload($comment));

        return response()->json(['comments' => $comments]);
    }

    public function voteComment()
    {
        $comment = Comment::findOrFail(request()->route('comment'));
        abort_unless($this->event->enable_comments, 403);
        abort_unless($comment->event_id === $this->event->id && $comment->is_approved, 404);

        CommentVote::firstOrCreate([
            'comment_id' => $comment->id,
            'user_id' => Auth::guard('web')->id(),
        ]);

        $comment->load('user:id,name,first_name,last_name')->loadCount('votes');
        $payload = $this->commentPayload($comment, true);
        try {
            broadcast(new CommentUpdated($this->event->slug, 'upvoted', $payload))->toOthers();
        } catch (\Throwable $exception) {
            Log::warning('Comment upvote saved but realtime broadcast failed.', ['comment_id' => $comment->id]);
        }

        return response()->json(['status' => true, 'comment' => $payload]);
    }

    private function commentPayload(Comment $comment, ?bool $votedByMe = null): array
    {
        $name = trim(($comment->user?->first_name ?? '') . ' ' . ($comment->user?->last_name ?? ''))
            ?: ($comment->user?->name ?: 'User');

        return [
            'id' => $comment->id,
            'user_name' => $name,
            'comment' => $comment->comment,
            'is_approved' => (bool) $comment->is_approved,
            'votes_count' => (int) ($comment->votes_count ?? 0),
            'voted_by_me' => $votedByMe ?? (bool) ($comment->voted_by_me ?? false),
            'created_at' => $comment->created_at?->toIso8601String(),
            'time_ago' => $comment->created_at?->diffForHumans() ?? '',
        ];
    }

    public function getPoll()
    {
        abort_unless($this->event->enable_polls, 403);
        $poll = Poll::where('status', 'active')
            ->CurrentEvent()
            ->withCount('votes')
            ->with(['poll_answers' => function ($query) {
                $query->withCount('user_voted');
            }])
            ->where('is_hidden', 0)
            ->latest()
            ->first();
        if (!$poll) {
            return response()->json(['poll' => null]);
        }

        $vote = UserPollAnswer::where('poll_id', $poll->id)
            ->where('user_id', Auth::guard('web')->id())
            ->first();

        if ($poll->interaction_type === 'multiple_choice') {
            $selections = $poll->votes()
                ->pluck('answer')
                ->flatMap(fn ($answer) => array_map('trim', explode(', ', (string) $answer)))
                ->countBy();

            $poll->poll_answers->each(function ($option) use ($selections) {
                $option->setAttribute('user_voted_count', (int) $selections->get($option->answer, 0));
            });
        }

        return response()->json(['poll' => $poll, 'voted' => $vote]);
    }

    public function submitPoll(Request $request)
    {
        abort_unless($this->event->enable_polls, 403);
        $poll = Poll::where('event_id', $this->event->id)
            ->where('status', 'active')->where('is_hidden', 0)
            ->findOrFail($request->integer('poll_id'));

        $rules = ['answer' => ['required', 'string', 'max:2000']];
        if ($poll->interaction_type === 'single_choice') {
            $rules['answer_id'] = ['required', 'integer'];
        } elseif ($poll->interaction_type === 'multiple_choice') {
            $rules['answer_ids'] = ['required', 'array', 'min:1'];
            $rules['answer_ids.*'] = ['required', 'integer', 'distinct'];
        } else {
            $rules['answer_id'] = ['nullable'];
        }
        $validated = $request->validate($rules);

        if ($poll->interaction_type === 'single_choice') {
            $option = $poll->poll_answers()->findOrFail($validated['answer_id']);
            $validated['answer'] = $option->answer;
        } elseif ($poll->interaction_type === 'multiple_choice') {
            $options = $poll->poll_answers()->whereIn('id', $validated['answer_ids'])->get();
            abort_unless($options->count() === count($validated['answer_ids']), 422);
            $validated['answer'] = $options->pluck('answer')->implode(', ');
        } elseif ($poll->interaction_type === 'rating') {
            $rating = filter_var($validated['answer'], FILTER_VALIDATE_INT);
            abort_unless($rating !== false && $rating >= 1 && $rating <= $poll->rating_max, 422);
            $validated['answer'] = (string) $rating;
        }

        $alreadyVoted = UserPollAnswer::where('poll_id', $request->poll_id)
            ->where('user_id', Auth::guard('web')->id())
            ->exists();

        if ($alreadyVoted) {
            return response()->json(['status' => false, 'message' => 'You have already voted'], 409);
        }

        UserPollAnswer::create([
            'poll_id' => $request->poll_id,
            'user_id' => Auth::guard('web')->id(),
            'answer' => $validated['answer'],
            'answer_id' => $poll->interaction_type === 'single_choice' ? $validated['answer_id'] : null,
        ]);

        return response()->json(['status' => true, 'message' => 'Vote submitted successfully']);
    }

    private function checkAttendanceConditions(): bool
    {
        $now = Carbon::now('Asia/Kolkata');

        $from = $this->event->active_user_from ? Carbon::parse($this->event->active_user_from, 'Asia/Kolkata') : null;
        $to = $this->event->active_user_to ? Carbon::parse($this->event->active_user_to, 'Asia/Kolkata') : null;

        if ($from && $now->lt($from)) return false;
        if ($to && $now->gt($to)) return false;

        return true;
    }
}
