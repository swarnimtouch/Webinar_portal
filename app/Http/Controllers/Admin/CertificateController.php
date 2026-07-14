<?php

namespace App\Http\Controllers\Admin;

use App\Models\Events;
use Illuminate\Http\Request;
use App\Models\Certificate;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class CertificateController
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $certificate = Certificate::with('event')->get()->unique('event_id')->values();
        return view('admin.certificate.index', [
            'certificate' => $certificate,
            'title' => __('Certificate'),
            'breadcrumb' => breadcrumb([
                __('Certificate') => route('admin.certificate')
            ])
        ]);
    }

    /**
     * Show the form for creating/editing a resource.
     */
    public function addEditForm($id = null)
    {
        $certificate = $id ? Certificate::findOrFail($id) : new Certificate();

        $events = Events::get();

        return view('admin.certificate.add_edit', [
            'certificate' => $certificate,
            'title' => __('Certificate'),
            'events' => $events,
            'breadcrumb' => breadcrumb([__('Certificate') => route('admin.certificate'), ($id ? 'Edit' : 'Add' . ' Certificate') => '']),

        ]);
    }

    /**
     * Store or update certificate
     */
    public function save(Request $request, $id = null)
    {
        $certificate = $id ? Certificate::findOrFail($id) : new Certificate();

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|min:2|max:255',
            'background_image' => $certificate->exists
                ? 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120'
                : 'required|image|mimes:jpg,jpeg,png,webp|max:5120',
            'event_id' => 'nullable|exists:events,id',
            'font_file' => 'nullable|file',
            'font_size' => 'required|integer|min:1|max:300',
            'font_color' => 'required|string|max:20',
            'is_bold' => 'nullable|boolean',
            'start_x' => 'required|integer|min:0',
            'end_x' => 'required|integer|min:0',
            'y' => 'required|integer|min:0',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        if ($request->hasFile('background_image')) {
            if ($certificate->exists && $certificate->background_image) {
                Storage::disk('public')->delete($certificate->background_image);
            }
            $bgImage = $request->file('background_image');
            $originalName = $bgImage->getClientOriginalName();
            $certificate->background_image = $bgImage->storeAs('certificates/backgrounds', $originalName, 'public');
        }

        if ($request->hasFile('font_file')) {
            if ($certificate->exists && $certificate->font_file) {
                Storage::disk('public')->delete($certificate->font_file);
            }
            $fontFile = $request->file('font_file');
            $originalName = $fontFile->getClientOriginalName();
            $certificate->font_file = $fontFile->storeAs('certificates/fonts', $originalName, 'public');
        }

        $certificate->event_id = $request->event_id;
        $certificate->name = $request->name;
        $certificate->font_size = $request->font_size;
        $certificate->font_color = $request->font_color;
        $certificate->is_bold = $request->has('is_bold') ? 1 : 0;
        $certificate->start_x = $request->start_x;
        $certificate->end_x = $request->end_x;
        $certificate->y = $request->y;
        $certificate->status = 'active';
        $certificate->save();


        return redirect()->route('admin.certificate')->with('success', 'Certificate Saved Successfully');
    }

    /**
     * Delete single certificate
     */
    public function delete($id)
    {
        try {
            $certificate = Certificate::findOrFail($id);

            if ($certificate->background_image) {
                Storage::disk('public')->delete($certificate->background_image);
            }

            $certificate->delete();

            return response()->json([
                'success' => true,
                'message' => __('Certificate deleted successfully')
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => __('Error deleting Certificate')
            ], 500);
        }
    }

    /**
     * Delete multiple certificates
     */
    public function deleteMultiple(Request $request)
    {
        try {
            $ids = $request->ids;

            if (empty($ids)) {
                return response()->json([
                    'success' => false,
                    'message' => __('No Certificates selected')
                ], 400);
            }

            Certificate::whereIn('id', $ids)->each(function ($certificate) {
                if ($certificate->background_image) {
                    Storage::disk('public')->delete($certificate->background_image);
                }
            });

            Certificate::whereIn('id', $ids)->delete();

            return response()->json([
                'success' => true,
                'message' => __('Certificates deleted successfully')
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => __('Error deleting Certificates')
            ], 500);
        }
    }

    /**
     * Toggle certificate status
     */
    public function toggleStatus($id)
    {
        try {
            $certificate = Certificate::findOrFail($id);
            $certificate->status = $certificate->status === 'active' ? 'inactive' : 'active';
            $certificate->save();

            return response()->json([
                'success' => true,
                'message' => __('status updated successfully'),
                'status' => $certificate->status
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => __('Failed to update status')
            ], 500);
        }
    }

    /**
     * DataTable for certificates
     */
    public function datatable(Request $request)
    {
        $user = auth()->user();
        $query = Certificate::with('event');
        if ($user->type === 'sub_admin') {
            $query->where('event_id', $user->event_id);
        }
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('font_file', 'like', "%{$search}%")
                    ->orWhere('font_color', 'like', "%{$search}%")
                    ->orwherehas('event', function ($q2) use ($search) {
                        $q2->where('name', 'like', "%{$search}%");
                    });
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('event')) {
            $query->where('event_id', $request->event);
        }
        $recordsTotal = Certificate::count();
        $recordsFiltered = $query->count();

        if ($request->has('order')) {
            $columns = $request->columns;
            foreach ($request->order as $order) {
                $columnName = $columns[$order['column']]['data'];
                $direction = $order['dir'];

                if (in_array($columnName, ['name', 'font_size', 'status', 'created_at'])) {
                    $query->orderBy($columnName, $direction);
                }
            }
        } else {
            $query->orderBy('id', 'desc');
        }

        $certificates = $query
            ->skip($request->start)
            ->take($request->length)
            ->get();

        $data = $certificates->map(function ($certificate) {
            return [
                'id' => $certificate->id,
                'name' => $certificate->name,
                'event' => $certificate->event->name ?? 'N/A',
                'background_image' => $certificate->background_image
                    ? asset('storage/' . $certificate->background_image)
                    : null,
                'font_file' => $certificate->font_file,
                'font_size' => $certificate->font_size,
                'font_color' => $certificate->font_color,
                'is_bold' => $certificate->is_bold,
                'start_x' => $certificate->start_x,
                'end_x' => $certificate->end_x,
                'y' => $certificate->y,
                'status' => $certificate->status,
                'created_at' => $certificate->created_at
                    ? $certificate->created_at->format('d M, Y')
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
