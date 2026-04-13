<?php

namespace App\Http\Controllers\Admin;

use App\Models\Brands;
use App\Models\Feedback;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class FeedbackController
{
    //
    public function index()
    {

        return view('admin.feedback.index', [
            'title' => __('FeedBack'),
            'breadcrumb' => breadcrumb([
                __('FeedBack') => route('admin.feedback.index')
            ])
        ]);

    }
    public function delete($id)
    {
        try {
            $feedback = Feedback::findOrFail($id);


            $feedback->delete();

            return response()->json(['success' => true, 'message' => 'FeedBack deleted successfully']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error deleting FeedBack'], 500);
        }
    }

    /**
     * Delete multiple banners
     */
    public function deleteMultiple(Request $request)
    {
        try {
            $ids = $request->input('ids', []);

            if (empty($ids)) {
                return response()->json(['success' => false, 'message' => 'No FeedBack selected'], 400);
            }

            $feedBack = Feedback::whereIn('id', $ids)->get();

            foreach ($feedBack as $feedBacks) {


                $feedBacks->delete();
            }

            return response()->json(['success' => true, 'message' => 'FeedBack deleted successfully']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error deleting FeedBack'], 500);
        }
    }
    public function datatable(Request $request)
    {
        $query = Feedback::with('user');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            })->orWhere('comment', 'like', "%{$search}%");
        }

        $total = $query->count();

        if ($request->has('order')) {
            $columns = $request->columns;
            foreach ($request->order as $order) {
                $columnIndex = $order['column'];
                $columnName = $columns[$columnIndex]['data'];
                $direction = $order['dir'];

                $dbColumn = match ($columnName) {
                    'rating' => 'rating',
                    'created_at' => 'created_at',
                    default => 'id',
                };

                $query->orderBy($dbColumn, $direction);
            }
        } else {
            $query->orderBy('id', 'desc');
        }

        $length = $request->input('length', 10);
        $start  = $request->input('start', 0);

        $feedbacks = $query->skip($start)->take($length)->get();

        $data = $feedbacks->map(function ($feedback) {
            return [
                'id' => $feedback->id,
                'user_name' => optional($feedback->user)->name ?? 'N/A',
                'user_email' => optional($feedback->user)->email ?? 'N/A',
                'rating' => $feedback->rating,
                'comment' => $feedback->comment ?? '-',
                'created_at' => $feedback->created_at->format('d M Y'),
                'actions' => '',
            ];
        });

        return response()->json([
            'draw' => (int) $request->input('draw'),
            'recordsTotal' => $total,
            'recordsFiltered' => $total,
            'data' => $data,
        ]);
    }

}
