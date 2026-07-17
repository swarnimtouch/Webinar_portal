<?php

namespace App\Http\Controllers\Admin;

use App\Events\CommentUpdated;
use App\Models\Comment;
use App\Models\DynamicFields;
use App\Models\Events;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Log;

class CommentsController extends Controller
{
    private function fields($user)
    {
        $columns = Schema::getColumnListing('users');
        $fields = DynamicFields::active()->where('field_name', '!=', 'password')
            ->when($user->type === 'sub_admin', fn ($q) => $q->where('event_id', $user->event_id))
            ->orderBy('index_no')->get()->unique('field_name')
            ->filter(fn ($field) => in_array($field->field_name, $columns, true))->values();

        $nameField = $fields->first(fn ($field) => in_array($field->field_name, ['first_name', 'last_name', 'name'], true));
        if (!$nameField) {
            return $fields;
        }

        $nameField->field_name = 'full_name';
        $nameField->label = 'Name';

        return $fields
            ->reject(fn ($field) => $field !== $nameField && in_array($field->field_name, ['first_name', 'last_name', 'name'], true))
            ->values();
    }

    private function fieldValue(?object $user, string $fieldName): string
    {
        if (!$user) return 'N/A';
        if ($fieldName === 'full_name') {
            return trim(($user->first_name ?? '') . ' ' . ($user->last_name ?? '')) ?: ($user->name ?: 'N/A');
        }
        return $user->{$fieldName} ?? 'N/A';
    }

    private function query(Request $request)
    {
        $user = auth()->user();
        return Comment::with(['user', 'event'])->withCount('votes')
            ->when($user->type === 'sub_admin', fn ($q) => $q->where('event_id', $user->event_id))
            ->when($request->filled('event'), fn ($q) => $q->where('event_id', $request->integer('event')))
            ->when($request->filled('search'), function ($q) use ($request) {
                $search = $request->string('search')->value();
                $q->where(fn ($inner) => $inner->where('comment', 'like', "%{$search}%")
                    ->orWhereHas('user', fn ($uq) => $uq->where('name', 'like', "%{$search}%")
                        ->orWhere('first_name', 'like', "%{$search}%")
                        ->orWhere('last_name', 'like', "%{$search}%")
                        ->orWhereRaw("CONCAT_WS(' ', first_name, last_name) LIKE ?", ["%{$search}%"])
                        ->orWhere('email', 'like', "%{$search}%")));
            });
    }

    public function index(Request $request)
    {
        $user = auth()->user();
        abort_if($user->type === 'sub_admin' && !$user->event?->enable_comments, 403);
        return view('admin.comments.index', [
            'title' => 'Comments', 'breadcrumb' => breadcrumb(['Comments' => route('admin.comments.index')]),
            'comments' => $this->query($request)->latest()->paginate(25)->withQueryString(),
            'fields' => $this->fields($user),
            'events' => $user->type === 'admin' ? Events::orderBy('name')->get() : collect(),
        ]);
    }

    public function delete(Comment $comment)
    {
        $user = auth()->user();
        abort_if($user->type === 'sub_admin' && $comment->event_id !== $user->event_id, 403);
        $comment->delete();
        return back()->with('success', 'Comment deleted successfully.');
    }

    public function deleteMultiple(Request $request)
    {
        $ids = $request->validate(['ids' => ['required', 'array'], 'ids.*' => ['integer']])['ids'];
        $user = auth()->user();
        Comment::whereIn('id', $ids)
            ->when($user->type === 'sub_admin', fn ($q) => $q->where('event_id', $user->event_id))
            ->delete();
        return $request->expectsJson()
            ? response()->json(['success' => true, 'message' => 'Selected comments deleted successfully.'])
            : back()->with('success', 'Selected comments deleted successfully.');
    }

    public function toggleStatus(Request $request, Comment $comment)
    {
        $user = auth()->user();
        abort_if($user->type === 'sub_admin' && $comment->event_id !== $user->event_id, 403);
        $validated = $request->validate(['is_approved' => ['required', 'boolean']]);

        $comment->is_approved = (bool) $validated['is_approved'];
        $comment->save();
        $comment->load(['user', 'event'])->loadCount('votes');

        $payload = [
            'id' => $comment->id,
            'user_name' => $this->fieldValue($comment->user, 'full_name'),
            'comment' => $comment->comment,
            'votes_count' => (int) $comment->votes_count,
            'voted_by_me' => false,
            'created_at' => $comment->created_at?->toIso8601String(),
            'time_ago' => $comment->created_at?->diffForHumans() ?? '',
        ];
        try {
            broadcast(new CommentUpdated($comment->event->slug, $comment->is_approved ? 'approved' : 'hidden', $payload));
        } catch (\Throwable $exception) {
            Log::warning('Comment status saved but realtime broadcast failed.', ['comment_id' => $comment->id]);
        }

        return response()->json([
            'success' => true,
            'message' => $comment->is_approved ? 'Comment published successfully.' : 'Comment unpublished successfully.',
        ]);
    }

    public function datatable(Request $request)
    {
        $user = auth()->user();
        abort_if($user->type === 'sub_admin' && !$user->event?->enable_comments, 403);
        $fields = $this->fields($user);
        $base = Comment::query()->when($user->type === 'sub_admin', fn ($q) => $q->where('event_id', $user->event_id));
        $recordsTotal = (clone $base)->count();
        $query = $this->query($request);
        $recordsFiltered = (clone $query)->count();

        $sortColumn = $request->input('columns.' . $request->input('order.0.column') . '.data');
        $direction = $request->input('order.0.dir') === 'asc' ? 'asc' : 'desc';
        $query->orderBy(in_array($sortColumn, ['created_at', 'comment'], true) ? $sortColumn : 'id', $direction);

        $comments = $query->skip($request->integer('start'))->take(max(1, $request->integer('length', 10)))->get();
        $data = $comments->map(function (Comment $comment) use ($fields, $user) {
            $row = ['id' => $comment->id];
            if ($user->type === 'admin') $row['event'] = $comment->event?->name ?? 'N/A';
            foreach ($fields as $field) $row[$field->field_name] = $this->fieldValue($comment->user, $field->field_name);
            $row['comment'] = $comment->comment;
            $row['votes_count'] = (int) $comment->votes_count;
            $row['is_approved'] = $comment->is_approved;
            $row['created_at'] = $comment->created_at?->format('d M Y H:i') ?? '-';
            return $row;
        });

        return response()->json([
            'draw' => $request->integer('draw'), 'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsFiltered, 'data' => $data,
        ]);
    }

    public function export(Request $request)
    {
        $user = auth()->user(); $fields = $this->fields($user); $rows = [];
        $headers = $user->type === 'admin' ? ['Event'] : [];
        $headers = array_merge($headers, $fields->pluck('label')->all(), ['Comment', 'Upvotes', 'Publish', 'Date']);
        $esc = fn ($value) => '"' . str_replace('"', '""', (string) ($value ?? '')) . '"';
        $rows[] = implode(',', array_map($esc, $headers));
        foreach ($this->query($request)->latest()->get() as $comment) {
            $row = $user->type === 'admin' ? [$comment->event?->name] : [];
            foreach ($fields as $field) $row[] = $this->fieldValue($comment->user, $field->field_name);
            $row[] = $comment->comment;
            $row[] = (int) $comment->votes_count;
            $row[] = $comment->is_approved ? 'Published' : 'Unpublished';
            $row[] = $comment->created_at?->format('Y-m-d H:i:s');
            $rows[] = implode(',', array_map($esc, $row));
        }
        return response("\xEF\xBB\xBF" . implode("\n", $rows), 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="comments_export_' . now()->format('Y-m-d') . '.csv"',
        ]);
    }
}
