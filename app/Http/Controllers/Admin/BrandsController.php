<?php

namespace App\Http\Controllers\Admin;

use App\Models\Brands;
use App\Models\Events;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BrandsController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    public function index()
    {
        $brand = Brands::with('event')->get()->unique('event_id')->values();
        return view('admin.brands.index', [
            'brand' => $brand,
            'title' => __('Brands'),
            'breadcrumb' => breadcrumb([
                __('Brands') => route('admin.brand')
            ])
        ]);

    }

    public function addEditForm($id = null)
    {
        $brand = $id ? Brands::findOrFail($id) : new Brands();
        $events = Events::get();
        $response = [
            'brand' => $brand,
            'events' => $events,
            'title' => __('Brand'),
            'breadcrumb' => breadcrumb([__('Brands') => route('admin.brand'), ($id ? 'Edit' : 'Add' . ' Brand') => '']),
        ];
        return view('admin.brands.add_edit', $response);
    }

    public function save(Request $request, $id = null)
    {
        $brand = $id ? Brands::findOrFail($id) : new Brands();
        $isUpdate = $brand->exists;

        $rules = [
            'title' => 'required|string|max:255',
            'event_id' => 'nullable|exists:events,id',
        ];

        if (!$isUpdate) {
            $rules['filename'] = 'required|file|mimes:jpg,jpeg,png,gif,webp,mp4,mov,avi,webm|max:20480';
        } else {
            $rules['filename'] = 'nullable|file|mimes:jpg,jpeg,png,gif,webp,mp4,mov,avi,webm|max:20480';
        }

        $request->validate($rules);

        $brand->title = $request->title;
        $brand->event_id = $request->event_id;
        $brand->status = $brand->status ?? 'active';

        if ($request->hasFile('filename')) {

            if ($brand->filename && Storage::disk('public')->exists('brands/' . $brand->filename)) {
                Storage::disk('public')->delete('brands/' . $brand->filename);
            }
            $file = $request->file('filename');
            $mimeType = $file->getMimeType();
            $type = str_starts_with($mimeType, 'image/') ? 'image' : 'video';
            $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->storeAs('brands', $filename, 'public');
            $brand->filename = $filename;
            $brand->type = $type;
        }
        $brand->save();
        return redirect()->route('admin.brand')
            ->with('success', 'Brand Saved successfully');
    }

    public function delete($id)
    {
        try {
            $brands = Brands::findOrFail($id);
            if (Storage::exists('public/brands/' . $brands->filename)) {
                Storage::delete('public/brands/' . $brands->filename);
            }
            $brands->delete();
            return response()->json(['success' => true, 'message' => 'Brand deleted successfully']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error deleting Brand'], 500);
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
                return response()->json(['success' => false, 'message' => 'No brands selected'], 400);
            }
            $brands = Brands::whereIn('id', $ids)->get();
            foreach ($brands as $brand) {
                if (Storage::exists('public/brands/' . $brand->filename)) {
                    Storage::delete('public/brands/' . $brand->filename);
                }
                $brand->delete();
            }
            return response()->json(['success' => true, 'message' => 'Brands deleted successfully']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error deleting Brands'], 500);
        }
    }

    public function toggleStatus(Request $request, $id)
    {
        try {
            $brands = Brands::findOrFail($id);
            $brands->status = $request->input('status');
            $brands->save();
            return response()->json([
                'success' => true,
                'message' => 'Status updated successfully',
                'status' => $brands->status
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating status'
            ], 500);
        }
    }

    public function datatable(Request $request)
    {
        $user = auth()->user();
        $query = Brands::with('event');
        if ($user->type === 'sub_admin') {
            $query->where('event_id', $user->event_id);
        }
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhereHas('event', function ($q2) use ($search) {
                        $q2->where('name', 'like', "%{$search}%");
                    });
            });
        }
        if ($request->filled('type') && !empty($request->type)) {
            $type = $request->type;
            $query->where('type', $type);
        }
        if ($request->filled('status') && !empty($request->status)) {
            $status = $request->status;
            $query->where('status', $status);
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
                    'title' => 'title',
                    'type' => 'type',
                    default => 'id'
                };
                $query->orderBy($dbColumn, $direction);
            }
        } else {
            $query->orderBy('id', 'desc');
        }
        $length = $request->input('length', 10);
        $start = $request->input('start', 0);
        $brands = $query->skip($start)->take($length)->get();
        $data = $brands->map(function ($brand) {
            return [
                'id' => $brand->id,
                'title' => $brand->title,
                'event' => $brand->event->name ?? 'N/A',
                'media_url' => $brand->media_url,
                'created_at' => $brand->created_at->format('d M Y'),
                'status' => $brand->status,
                'actions' => '',
            ];
        });
        return response()->json([
            'draw' => $request->input('draw', 1),
            'recordsTotal' => $total,
            'recordsFiltered' => $total,
            'data' => $data
        ]);
    }
}
