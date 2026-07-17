<?php

namespace App\Http\Controllers\Admin;

use App\Models\Brands;
use App\Models\Feedback;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class FeedbackController
{
    private function ensureEnabledForSubAdmin(): void
    {
        $user = auth()->user();
        abort_if($user->type === 'sub_admin' && !$user->event?->enable_feedback, 403);
    }

    public function index()
    {
        $this->ensureEnabledForSubAdmin();
        $feedback = Feedback::with('event')->get()->unique('event_id')->values();
        return view('admin.feedback.index', [
            'feedback' => $feedback,
            'title' => __('FeedBack'),
            'breadcrumb' => breadcrumb([
                __('FeedBack') => route('admin.feedback.index')
            ])
        ]);

    }

    public function delete($id)
    {
        $this->ensureEnabledForSubAdmin();
        $user = auth()->user();
        $feedback = Feedback::query()
            ->when($user->type === 'sub_admin', fn ($query) => $query->where('event_id', $user->event_id))
            ->findOrFail($id);
        try {
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
        $this->ensureEnabledForSubAdmin();
        try {
            $ids = $request->input('ids', []);

            if (empty($ids)) {
                return response()->json(['success' => false, 'message' => 'No FeedBack selected'], 400);
            }

            $feedBack = Feedback::whereIn('id', $ids)
                ->when(auth()->user()->type === 'sub_admin', fn ($query) => $query->where('event_id', auth()->user()->event_id))
                ->get();

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
        $this->ensureEnabledForSubAdmin();
        $user = auth()->user();

        $query = Feedback::with(['user', 'event']);
        if ($user->type === 'sub_admin') {
            $query->where('event_id', $user->event_id);
        }
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            })->orWhere('comment', 'like', "%{$search}%");
        }
        if ($request->filled('event')) {
            $query->where('event_id', $request->event);
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
        $start = $request->input('start', 0);

        $feedbacks = $query->skip($start)->take($length)->get();

        $data = $feedbacks->map(function ($feedback) {
            return [
                'id' => $feedback->id,
                'user_name' => optional($feedback->user)->name ?? 'N/A',
                'user_email' => optional($feedback->user)->email ?? 'N/A',
                'event' => $feedback->event->name ?? 'N/A',
                'rating' => $feedback->rating,
                'comment' => $feedback->comment ?? 'N/A',
                'created_at' => $feedback->created_at->format('d M Y'),
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

    public function export(Request $request)
    {
        $this->ensureEnabledForSubAdmin();
        $user = auth()->user();
        $isAdmin = $user->type === 'admin';
        $search = $request->get('search');

        $query = Feedback::with(['user', 'event'])
            ->when(!$isAdmin, function ($q) use ($user) {
                $q->where('event_id', $user->event_id);
            })
            ->when($search, function ($q) use ($search) {
                $q->whereHas('user', function ($uq) use ($search) {
                    $uq->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                })->orWhere('comment', 'like', "%{$search}%");
            })
            ->orderBy('id', 'desc')
            ->get();

        $headers = $isAdmin
            ? ['Event', 'Name', 'Email', 'Rating', 'Comment', 'Date']
            : ['Name', 'Email', 'Rating', 'Comment', 'Date'];

        $rows = [];
        $rows[] = implode(',', $headers);

        $esc = fn($val) => '"' . str_replace('"', '""', $val) . '"';

        foreach ($query as $feedback) {
            $eventName = optional($feedback->event)->name ?? 'N/A';
            $userName = optional($feedback->user)->name ?? 'N/A';
            $userEmail = optional($feedback->user)->email ?? 'N/A';
            $rating = $feedback->rating ?? 'N/A';
            $comment = $feedback->comment ?? 'N/A';
            $date = $feedback->created_at?->format('d M Y') ?? 'N/A';

            $row = $isAdmin
                ? [$esc($eventName), $esc($userName), $esc($userEmail), $esc($rating), $esc($comment), $esc($date)]
                : [$esc($userName), $esc($userEmail), $esc($rating), $esc($comment), $esc($date)];

            $rows[] = implode(',', $row);
        }

        $csv = implode("\n", $rows);
        $filename = 'feedbacks_export_' . now()->format('Y-m-d') . '.csv';

        return response($csv, 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }
}
