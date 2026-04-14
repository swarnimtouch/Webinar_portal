<?php

namespace App\Http\Controllers\Admin;

use App\Models\Poll;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class PollController
{
    /**
     * Display poll listing page
     */
    public function index()
    {
        return view('admin.poll.index', [
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

        $title = $poll->exists ? __('Edit Poll') : __('Add Poll');

        return view('admin.poll.add_edit', [
            'poll' => $poll,
            'title' => $title,
            'breadcrumb' => breadcrumb([
                __('Poll') => route('admin.poll'),
                $title => ''
            ])
        ]);
    }

    /**
     * Store or update poll
     */
    public function save(Request $request, $id = null)
    {
        $poll = $id ? Poll::findOrFail($id) : new Poll();

        $validator = Validator::make($request->all(), [
            'question' => 'required|string|min:5|max:500',
            'answers' => 'required|array|min:2|max:10',
            'answers.*' => 'required|string|min:1|max:255',
            'status' => 'required|in:active,inactive',
            'is_hidden' => 'nullable|boolean'
        ], [
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

        $answers = array_values(array_filter($request->answers, function ($answer) {
            return !empty(trim($answer));
        }));

        if (count($answers) < 2) {
            return redirect()
                ->back()
                ->withErrors(['answers' => 'Please provide at least 2 answers'])
                ->withInput();
        }

        $poll->question = $request->question;
        $poll->answers = json_encode($answers);
        $poll->status = $request->status;
        $poll->is_hidden = $request->has('is_hidden') ? 1 : 0;
        $poll->save();

        $message = $poll->wasRecentlyCreated
            ? __('Poll created successfully')
            : __('Poll updated successfully');

        return redirect()
            ->route('admin.poll')
            ->with('success', $message);
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
                'message' => __('Failed to delete poll')
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
                'message' => __('Failed to delete polls')
            ], 500);
        }
    }

    /**
     * Toggle poll status
     */
    public function toggleStatus($id)
    {
        try {
            $poll = Poll::findOrFail($id);
            $poll->status = $poll->status === 'active' ? 'inactive' : 'active';
            $poll->save();

            return response()->json([
                'success' => true,
                'message' => __('Poll status updated successfully'),
                'status' => $poll->status
            ]);
        } catch (\Exception $e) {
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
        $query = Poll::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('question', 'like', "%{$search}%")
                    ->orWhere('answers', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
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

            $answers = json_decode($poll->answers, true) ?? [];

            return [
                'id' => $poll->id,
                'question' => $poll->question,
                'answers' => $answers,
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
