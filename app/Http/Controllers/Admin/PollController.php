<?php

namespace App\Http\Controllers\Admin;

use App\Events\PollBroadcast;
use App\Models\Events;
use App\Models\Poll;
use App\Models\PollAnswer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class PollController
{
    /**
     * Display poll listing page
     */
    public function index()
    {
        $poll = Poll::with('event')->get()->unique('event_id')->values();
        return view('admin.poll.index', [
            'poll' => $poll,
            'title' => __('Poll'),
            'breadcrumb' => breadcrumb([
                __('Poll') => route('admin.poll')
            ])
        ]);
    }

    /**
     * Show add/edit form
     */
    public function addEditForm($id = null)
    {
        $poll = $id ? Poll::findOrFail($id) : new Poll();

        $events = Events::get();


        return view('admin.poll.add_edit', [
            'poll' => $poll,
            'events' => $events,
            'title' => __('Poll'),
            'breadcrumb' => breadcrumb([__('Poll') => route('admin.poll'), ($id ? 'Edit' : 'Add' . ' Poll') => '']),
        ]);
    }

    /**
     * Store or update poll
     */
    public function save(Request $request, $id = null)
    {
        $poll = $id ? Poll::findOrFail($id) : new Poll();
        $interactionType = $request->input('interaction_type', 'single_choice');

        $rules = [
            'event_id' => 'nullable|exists:events,id',
            'question' => 'required|string|min:5|max:500',
            'interaction_type' => 'required|in:single_choice,multiple_choice,text,rating',
            'rating_max' => 'nullable|required_if:interaction_type,rating|integer|min:3|max:10',
            'is_hidden' => 'nullable|boolean'
        ];
        if (in_array($interactionType, ['single_choice', 'multiple_choice'], true)) {
            $rules['answers'] = 'required|array|min:2|max:10';
            $rules['answers.*'] = 'required|string|min:1|max:255';
        } else {
            $rules['answers'] = 'nullable|array';
        }

        $validator = Validator::make($request->all(), $rules, [
            'answers.required' => 'Please provide at least 2 answers',
            'answers.min' => 'Minimum 2 answers are required',
            'answers.max' => 'Maximum 10 answers are allowed',
            'answers.*.required' => 'Answer field cannot be empty',
        ]);

        if ($validator->fails()) {
            return redirect()
                ->back()
                ->withErrors($validator)
                ->withInput();
        }

        $answers = array_values(array_filter($request->input('answers', []), function ($answer) {
            return !empty(trim($answer));
        }));

        if (in_array($interactionType, ['single_choice', 'multiple_choice'], true) && count($answers) < 2) {
            return redirect()
                ->back()
                ->withErrors(['answers' => 'Please provide at least 2 answers'])
                ->withInput();
        }
        $poll->event_id = $request->event_id;
        $poll->question = $request->question;
        $poll->interaction_type = $interactionType;
        $poll->rating_max = $interactionType === 'rating' ? $request->integer('rating_max', 5) : 5;
        $poll->answers = in_array($interactionType, ['single_choice', 'multiple_choice'], true) ? $answers : [];
        $poll->is_hidden = $request->has('is_hidden') ? 1 : 0;
        if ($poll->save()) {
            $poll->poll_answers()->delete();
            if (in_array($interactionType, ['single_choice', 'multiple_choice'], true) && !empty($answers)) {
                $formattedAnswers = collect($answers)->map(function ($answer) {
                    return ['answer' => $answer];
                })->toArray();
                $poll->poll_answers()->createMany($formattedAnswers);
            }
            if ($poll->is_hidden == 0) {
                broadcast(new PollBroadcast($poll, $poll->event->slug))->toOthers();
            }
        }
        return redirect()
            ->route('admin.poll')
            ->with('success', 'Poll Saved Successfully');
    }

    /**
     * Delete single poll
     */
    public function delete($id)
    {
        try {
            $poll = Poll::findOrFail($id);
            $poll->delete();

            return response()->json([
                'success' => true,
                'message' => __('Poll deleted successfully')
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => __('Error deleting poll')
            ], 500);
        }
    }

    /**
     * Delete multiple polls
     */
    public function deleteMultiple(Request $request)
    {
        try {
            $ids = $request->ids;

            if (empty($ids)) {
                return response()->json([
                    'success' => false,
                    'message' => __('No polls selected')
                ], 400);
            }

            Poll::whereIn('id', $ids)->delete();

            return response()->json([
                'success' => true,
                'message' => __('Polls deleted successfully')
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => __('Error deleting polls')
            ], 500);
        }
    }

    /**
     * Toggle poll status
     */
    public function toggleStatus(Request $request, $id)
    {
        try {
            $request->validate(['status' => ['required', 'in:active,inactive']]);

            $poll = Poll::with('event')->findOrFail($id);
            $poll->status = $request->input('status');
            $poll->save();

            if ($poll->event) {
                try {
                    $poll->load('poll_answers');
                    broadcast(new PollBroadcast($poll, $poll->event->slug));
                } catch (\Throwable $broadcastException) {
                    Log::error('Poll status was saved but realtime broadcast failed.', [
                        'poll_id' => $poll->id,
                        'event_slug' => $poll->event->slug,
                        'exception' => $broadcastException,
                    ]);
                }
            }

            return response()->json([
                'success' => true,
                'message' => __('Status updated successfully'),
                'status' => $poll->status
            ]);
        } catch (\Throwable $e) {
            Log::error('Failed to update poll status.', [
                'poll_id' => $id,
                'exception' => $e,
            ]);

            return response()->json([
                'success' => false,
                'message' => __('Failed to update status')
            ], 500);
        }
    }

    /**
     * DataTable for polls
     */
    public function datatable(Request $request)
    {
        $user = auth()->user();
        $query = Poll::with(['event', 'poll_answers']);
        if ($user->type === 'sub_admin') {
            $query->where('event_id', $user->event_id);
        }
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('question', 'like', "%{$search}%")
                    ->orWhere('answers', 'like', "%{$search}%")
                    ->orWherehas('event', function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%");
                    });
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('event')) {
            $query->where('event_id', $request->event);
        }
        $recordsTotal = Poll::count();
        $recordsFiltered = $query->count();

        if ($request->has('order')) {
            $columns = $request->columns;
            foreach ($request->order as $order) {
                $columnName = $columns[$order['column']]['data'];
                $direction = $order['dir'];

                if (in_array($columnName, ['question', 'status', 'created_at'])) {
                    $query->orderBy($columnName, $direction);
                }
            }
        } else {
            $query->orderBy('id', 'desc');
        }

        $polls = $query
            ->skip($request->start)
            ->take($request->length)
            ->get();

        $data = $polls->map(function ($poll) {
            return [
                'id' => $poll->id,
                'event' => $poll->event->name ?? 'N/A',
                'question' => $poll->question,
                'interaction_type' => $poll->interaction_type,
                'rating_max' => $poll->rating_max,
                'answers' => $poll->poll_answers->pluck('answer')->toArray(),
                'status' => $poll->status,
                'created_at' => $poll->created_at->format('d M, Y'),
            ];
        });

        return response()->json([
            'draw' => intval($request->draw),
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data' => $data,
        ]);
    }


}
