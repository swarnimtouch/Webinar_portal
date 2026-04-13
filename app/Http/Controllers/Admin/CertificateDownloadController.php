<?php

namespace App\Http\Controllers\Admin;

use App\Models\CertificateDownload;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CertificateDownloadController
{
    public function index()
    {
        return view('admin.certificate_download.index', [
            'title'      => __('Certificate Downloads'),
            'breadcrumb' => breadcrumb([
                __('Certificate Downloads') => route('admin.certificate-download')
            ])
        ]);
    }

    public function delete($id)
    {
        try {

            $download = CertificateDownload::findOrFail($id);

            // 🔥 Delete stored file
            if ($download->file_path &&
                Storage::disk('public')->exists($download->file_path)) {

                Storage::disk('public')->delete($download->file_path);
            }

            // Delete DB record
            $download->delete();

            return response()->json([
                'success' => true,
                'message' => __('Record deleted successfully')
            ]);

        } catch (\Exception $e) {

            return response()->json([
                'success' => false,
                'message' => __('Failed to delete record')
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
                    'message' => __('No records selected')
                ], 400);
            }

            $downloads = CertificateDownload::whereIn('id', $ids)->get();

            foreach ($downloads as $download) {

                if ($download->file_path &&
                    Storage::disk('public')->exists($download->file_path)) {

                    Storage::disk('public')->delete($download->file_path);
                }
            }

            CertificateDownload::whereIn('id', $ids)->delete();

            return response()->json([
                'success' => true,
                'message' => __('Records deleted successfully')
            ]);

        } catch (\Exception $e) {

            return response()->json([
                'success' => false,
                'message' => __('Failed to delete records')
            ], 500);
        }
    }

    public function datatable(Request $request)
    {
        $query = CertificateDownload::with(['certificate', 'user']);

        /* ===== SEARCH ===== */
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->whereHas('user', fn($u) => $u->where('name', 'like', "%{$search}%"))
                    ->orWhereHas('certificate', fn($c) => $c->where('name', 'like', "%{$search}%"));
            });
        }

        $recordsTotal    = CertificateDownload::count();
        $recordsFiltered = $query->count();

        /* ===== ORDER ===== */
        if ($request->has('order')) {
            $columns = $request->columns;
            foreach ($request->order as $order) {
                $columnName = $columns[$order['column']]['data'];
                $direction  = $order['dir'];

                if (in_array($columnName, ['id', 'created_at'])) {
                    $query->orderBy($columnName, $direction);
                }
            }
        } else {
            $query->orderBy('id', 'desc');
        }

        /* ===== PAGINATION ===== */
        $downloads = $query
            ->skip($request->start)
            ->take($request->length)
            ->get();

        /* ===== RESPONSE DATA ===== */
        $data = $downloads->map(function ($download) {
            return [
                'id'               => $download->id,
                'certificate_id'   => $download->certificate_id,
                'certificate_name' => $download->certificate?->name ?? '-',
                'user_id'          => $download->user_id,
                'user_name'        => $download->user?->name ?? '-',
                'user_email'       => $download->user?->email ?? '-',
                'file_path'               => $download->file_path,
                'downloaded_at'    => $download->created_at
                    ? $download->created_at->format('d M, Y H:i')
                    : '-',
            ];
        });

        return response()->json([
            'draw'            => intval($request->draw),
            'recordsTotal'    => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data'            => $data,
        ]);
    }
}
