<?php

namespace App\Http\Controllers\Admin;

use App\Models\Banner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class BannerController extends Controller
{
    public function index()
    {
        return view('admin.banners.index', ['title' => __('Banners'), 'breadcrumb' => breadcrumb([__('Banners') => route('admin.banners')])]);
    }

    public function addEditForm($id = null)
    {
        $banner = $id ? Banner::findOrFail($id) : new Banner();

        $response = [
            'banner' => $banner,
            'title' => __('Banner'),
            'breadcrumb' => breadcrumb([__('Banners') => route('admin.banners'), ($id ? 'Edit' : 'Add' . ' Banner') => '']),
        ];
        return view('admin.banners.add_edit', $response);
    }


    public function save(Request $request, $id = null)
    {
        $banner = $id ? Banner::findOrFail($id) : new Banner();
        $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'type' => [
                'required',
                Rule::in(['image', 'video']),
            ],
            'image_file' => [
                Rule::requiredIf(fn() => !$id && $request->type === 'image'),
                Rule::when($request->type === 'image', [
                    'nullable',
                    'file',
                    'mimes:jpg,jpeg,png,webp',
                    'max:5120',
                ]),
            ],
            'video_file' => [
                Rule::requiredIf(fn() => !$id && $request->type === 'video'),
                Rule::when($request->type === 'video', [
                    'nullable',
                    'file',
                    'mimes:mp4,webm,mov',
                    'max:20480',
                ]),
            ],
        ]);

        $banner->title = $request->title ?? null;
        $banner->type = $request->type ?? 'image';

        if ($request->type === 'image' && $request->hasFile('image_file')) {

            $file = $request->file('image_file');
            $name = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->storeAs('banners', $name, 'public');
            $banner->filename = $name;
        }

        if ($request->type === 'video' && $request->hasFile('video_file')) {
            $file = $request->file('video_file');
            $name = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->storeAs('banners', $name, 'public');
            $banner->filename = $name;
        }

        $banner->save();

        return redirect()->route('admin.banners')
            ->with('success', 'Banner Saved Successfully');
    }


    public function delete($id)
    {
        try {
            $banner = Banner::findOrFail($id);
            if (Storage::exists('public/banners/' . $banner->filename)) {
                Storage::delete('public/banners/' . $banner->filename);
            }
            $banner->delete();

            return response()->json(['success' => true, 'message' => 'Banner deleted successfully']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error deleting banner'], 500);
        }
    }


    public function deleteMultiple(Request $request)
    {
        try {
            $ids = $request->input('ids', []);

            if (empty($ids)) {
                return response()->json(['success' => false, 'message' => 'No banners selected'], 400);
            }

            $banners = Banner::whereIn('id', $ids)->get();

            foreach ($banners as $banner) {
                // Delete the file from storage
                if (Storage::exists('public/banners/' . $banner->filename)) {
                    Storage::delete('public/banners/' . $banner->filename);
                }

                // Delete the database record
                $banner->delete();
            }

            return response()->json(['success' => true, 'message' => 'Banners deleted successfully']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error deleting banners'], 500);
        }
    }

    public function toggleStatus(Request $request, $id)
    {
        try {
            $banner = Banner::findOrFail($id);

            $banner->status = $request->input('status');
            $banner->save();

            return response()->json([
                'success' => true,
                'message' => 'Status updated successfully',
                'status' => $banner->status
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
        $query = Banner::query();

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('type', 'like', "%{$search}%");
            });
        }
        if ($request->has('type') && !empty($request->type)) {
            $type = $request->type;
            $query->where('type', $type);
        }

        if ($request->has('status') && !empty($request->status)) {
            $status = $request->status;
            $query->where('status', $status);
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
        $banners = $query->skip($start)->take($length)->get();

        $data = $banners->map(function ($banner) {
            return [
                'id' => $banner->id,
                'title' => $banner->title,
                'media_url' => $banner->media_url,
                'created_at' => $banner->created_at->format('d M Y'),
                'status' => $banner->status,
                'type' => $banner->type,
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
