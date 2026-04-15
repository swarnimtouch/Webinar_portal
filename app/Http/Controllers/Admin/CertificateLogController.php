<?php

namespace App\Http\Controllers\Admin;

use App\Models\CertificateLogs;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CertificateLogController
{
    public function index()
    {
        return view('admin.certificate_log.index', [
            'title' => __('Certificate Log'),
            'breadcrumb' => breadcrumb([
                __('Certificate Log') => route('admin.certificate-log')
            ])
        ]);
    }

    public function delete($id)
    {
        try {

            $download = CertificateLogs::findOrFail($id);

            if ($download->file_path &&
                Storage::disk('public')->exists($download->file_path)) {

                Storage::disk('public')->delete($download->file_path);
            }

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

                if ($download->file_path &&
                    Storage::disk('public')->exists($download->file_path)) {

                    Storage::disk('public')->delete($download->file_path);
                }
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
        $query = CertificateLogs::with(['certificate', 'user']);
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->whereHas('user', fn($u) => $u->where('name', 'like', "%{$search}%"))
                    ->orWhereHas('certificate', fn($c) => $c->where('name', 'like', "%{$search}%"));
            });
        }

        $recordsTotal = CertificateLogs::count();
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
                'certificate_name' => $download->certificate?->name ?? '-',
                'user_id' => $download->user_id,
                'user_name' => $download->user?->name ?? '-',
                'user_email' => $download->user?->email ?? '-',
                'file_path' => $download->file_path,
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
}
