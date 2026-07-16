<?php

namespace App\Http\Controllers\Admin;

use App\Models\CertificateLogs;
use App\Models\Events;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Support\EventStorage;

class CertificateLogController
{
    public function index()
    {
        $user = auth()->user();

        $eventIds = CertificateLogs::with('certificate')
            ->get()
            ->pluck('certificate.event_id')
            ->filter()
            ->unique()
            ->values();

        $download = Events::whereIn('id', $eventIds)->orderBy('name')->get();
        if ($user->type === 'sub_admin') {
            $download = $download->where('id', $user->event_id)->values();
        }

        return view('admin.certificate_log.index', [
            'title' => __('Certificate Log'),
            'download' => $download,

            'breadcrumb' => breadcrumb([
                __('Certificate Log') => route('admin.certificate_log')
            ])
        ]);
    }

    public function delete($id)
    {
        try {

            $download = CertificateLogs::findOrFail($id);

            EventStorage::delete($download->file_path);

            $download->delete();

            return response()->json([
                'success' => true,
                'message' => __('Certificate Log deleted successfully')
            ]);

        } catch (\Exception $e) {

            return response()->json([
                'success' => false,
                'message' => __('Error deleting Certificate Log')
            ], 500);
        }
    }

    public function deleteMultiple(Request $request)
    {
        try {

            $ids = $request->ids;

            if (empty($ids)) {
                return response()->json([
                    'success' => false,
                    'message' => __('No Certificate Log selected')
                ], 400);
            }

            $downloads = CertificateLogs::whereIn('id', $ids)->get();

            foreach ($downloads as $download) {

                EventStorage::delete($download->file_path);
            }

            CertificateLogs::whereIn('id', $ids)->delete();

            return response()->json([
                'success' => true,
                'message' => __('Certificate Log deleted successfully')
            ]);

        } catch (\Exception $e) {

            return response()->json([
                'success' => false,
                'message' => __('Error deleting Certificate Log')
            ], 500);
        }
    }

    public function datatable(Request $request)
    {
        $user = auth()->user();
        $query = CertificateLogs::latestPerUserCertificate()
            ->with(['certificate', 'user']);
        if ($user->type === 'sub_admin') {
            $query->whereHas('certificate', function ($q) use ($user) {
                $q->where('event_id', $user->event_id);
            });
        }
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->whereHas('user', fn($u) => $u->where('name', 'like', "%{$search}%"))
                    ->orWhereHas('certificate', fn($c) => $c->where('name', 'like', "%{$search}%"));
            });
        }
        if ($request->filled('event')) {
            $query->whereHas('certificate', function ($q) use ($request) {
                $q->where('event_id', $request->event);
            });
        }

        $totalQuery = CertificateLogs::latestPerUserCertificate();
        if ($user->type === 'sub_admin') {
            $totalQuery->whereHas('certificate', function ($q) use ($user) {
                $q->where('event_id', $user->event_id);
            });
        }
        $recordsTotal = $totalQuery->count();
        $recordsFiltered = $query->count();

        if ($request->has('order')) {
            $columns = $request->columns;
            foreach ($request->order as $order) {
                $columnName = $columns[$order['column']]['data'];
                $direction = $order['dir'];

                if (in_array($columnName, ['id', 'created_at'])) {
                    $query->orderBy($columnName, $direction);
                }
            }
        } else {
            $query->orderBy('id', 'desc');
        }

        $downloads = $query
            ->skip($request->start)
            ->take($request->length)
            ->get();

        $data = $downloads->map(function ($download) {
            return [
                'id' => $download->id,
                'certificate_id' => $download->certificate_id,
                'event' => $download->certificate?->event->name ?? 'N/A',
                'certificate_name' => $download->certificate?->name ?? 'N/A',
                'user_id' => $download->user_id,
                'user_name' => $download->user?->name ?? 'N/A',
                'user_email' => $download->user?->email ?? 'N/A',
                'file_path' => $download->file_path,
                'file_url' => EventStorage::downloadUrl($download->file_path),
                'downloaded_at' => $download->created_at
                    ? $download->created_at->format('d M, Y H:i')
                    : '-',
            ];
        });

        return response()->json([
            'draw' => intval($request->draw),
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data' => $data,
        ]);
    }

    public function export(Request $request)
    {
        $user = auth()->user();
        $isAdmin = $user->type === 'admin';
        $search = $request->get('search');

        $query = CertificateLogs::query()
            ->latestPerUserCertificate()
            ->with([
                'user',
                'certificate.event',

            ])
            ->when(!$isAdmin, function ($q) use ($user) {
                $q->whereHas('certificate', function ($cq) use ($user) {
                    $cq->where('event_id', $user->event_id);
                });
            })
            ->when($search, function ($q) use ($search) {
                $q->whereHas('user', function ($uq) use ($search) {
                    $uq->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                })->orWhereHas('certificate', function ($cq) use ($search) {
                    $cq->where('name', 'like', "%{$search}%");
                });
            })
            ->orderByDesc('created_at')
            ->get();

        $rows = [];
        $headers = $isAdmin
            ? ['Event', 'User', 'Email', 'Certificate', 'File', 'Downloaded At']
            : ['User', 'Email', 'Certificate', 'File', 'Downloaded At'];

        $rows[] = implode(',', $headers);

        $esc = fn($val) => '"' . str_replace('"', '""', $val) . '"';

        foreach ($query as $log) {
            $filePath = EventStorage::downloadUrl($log->file_path) ?? 'No File';
            $eventName = optional($log->certificate?->event)->name ?? 'N/A';

            $userName = optional($log->user)->name ?? 'N/A';
            $userEmail = optional($log->user)->email ?? 'N/A';
            $certName = optional($log->certificate)->name ?? 'N/A';
            $downloadedAt = $log->created_at?->format('Y-m-d H:i:s') ?? 'N/A';

            $row = $isAdmin
                ? [$esc($eventName), $esc($userName), $esc($userEmail), $esc($certName), $esc($filePath), $esc($downloadedAt)]
                : [$esc($userName), $esc($userEmail), $esc($certName), $esc($filePath), $esc($downloadedAt)];

            $rows[] = implode(',', $row);
        }

        $csv = implode("\n", $rows);
        $filename = 'certificate_downloads_export_' . now()->format('Y-m-d') . '.csv';

        return response($csv, 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }
}
