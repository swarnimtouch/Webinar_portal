<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Models\UserQuizAnswer;

class UserQuizResult
{
    public function index()
    {
        return view('admin.user_quiz_result.index', [
            'title' => __('User Quiz Result'),
            'breadcrumb' => breadcrumb([
                __('User Quiz Result') => route('admin.user_quiz_result')
            ])
        ]);
    }

    public function delete($id)
    {
        try {
            $userQuizAnswer = UserQuizAnswer::findOrFail($id);
            $userQuizAnswer->delete();

            return response()->json(['success' => true, 'message' => 'UserQuizAnswer deleted successfully']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error deleting UserQuizAnswer'], 500);
        }
    }

    public function deleteMultiple(Request $request)
    {
        try {
            $ids = $request->input('ids', []);

            if (empty($ids)) {
                return response()->json(['success' => false, 'message' => 'No UserQuizAnswer selected'], 400);
            }

            UserQuizAnswer::whereIn('id', $ids)->delete();

            return response()->json(['success' => true, 'message' => 'UserQuizAnswer deleted successfully']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error deleting UserQuizAnswer'], 500);
        }
    }

    public function datatable(Request $request)
    {
        $user = auth()->user();

        $query = UserQuizAnswer::with('user', 'poll');
        if ($user->type === 'sub_admin') {
            $query->whereHas('poll', function ($q) use ($user) {
                $q->where('event_id', $user->event_id);
            });
        }
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->whereHas('user', function ($userQuery) use ($search) {
                    $userQuery->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                })
                    ->orWhereHas('poll', function ($pollQuery) use ($search) {
                        $pollQuery->where('question', 'like', "%{$search}%");
                    })
                    ->orWhere('answer', 'like', "%{$search}%");
            });
        }

        $total = $query->count();

        if ($request->has('order')) {
            $columns = $request->columns;
            foreach ($request->order as $order) {
                $columnIndex = $order['column'];
                $columnName = $columns[$columnIndex]['data'];
                $direction = $order['dir'];

                $dbColumn = match ($columnName) {
                    'answer' => 'answer',
                    'created_at' => 'created_at',
                    default => 'id',
                };

                $query->orderBy($dbColumn, $direction);
            }
        } else {
            $query->orderBy('id', 'desc');
        }

        $length = $request->input('length', 10);
        $start = $request->input('start', 0);

        $userQuizAnswers = $query->skip($start)->take($length)->get();

        $data = $userQuizAnswers->map(function ($userQuizAnswer) {
            return [
                'id' => $userQuizAnswer->id,
                'event' => optional($userQuizAnswer->user)->event->name ?? 'N/A',
                'user_name' => optional($userQuizAnswer->user)->name ?? 'N/A',
                'user_email' => optional($userQuizAnswer->user)->email ?? 'N/A',
                'question' => optional($userQuizAnswer->poll)->question ?? 'N/A',
                'answer' => $userQuizAnswer->answer ?? 'N/A',
                'created_at' => $userQuizAnswer->created_at->format('d M Y'),
                'actions' => '',
            ];
        });

        return response()->json([
            'draw' => (int)$request->input('draw'),
            'recordsTotal' => $total,
            'recordsFiltered' => $total,
            'data' => $data,
        ]);
    }
}
