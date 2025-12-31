<?php

namespace App\Http\Controllers\Admin;

use App\Models\Banner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BannerController extends Controller
{
    public function index()
    {
        $banners = Banner::latest()->get();

        $title = 'Banner';
        $breadcrumbs = [
            'Banners' => ''
        ];

        return view('admin.banners.index', compact(
            'banners',
            'title',
            'breadcrumbs'
        ));
    }

    public function create($id = null)
    {
        $banner = $id ? Banner::findOrFail($id) : new Banner();

        if ($id) {
            // Edit
            $title = 'Banner';
            $breadcrumbs = [
                'Banners' => route('banners'),
                'Edit Banner' => ''
            ];
        } else {
            // Create
            $title = 'Banner';
            $breadcrumbs = [
                'Banners' => route('banners'),
                'Banner' => ''
            ];
        }

        return view('admin.banners.add_edit', compact(
            'banner',
            'title',
            'breadcrumbs'
        ));
    }


    public function store(Request $request, $id = null)
    {
        $banner = $id ? Banner::findOrFail($id) : new Banner();
        $isUpdate = $banner->exists;

        // Validation rules - file required only on create
        $rules = [
            'title' => 'required|string|max:255',
            'type' => 'required|in:image,video',
        ];

        // Add file validation based on type and mode
        if ($request->type === 'image') {
            $rules['image_file'] = $isUpdate ? 'nullable|file|mimes:jpg,jpeg,png,webp|max:5120'
                : 'required|file|mimes:jpg,jpeg,png,webp|max:5120';
        } else if ($request->type === 'video') {
            $rules['video_file'] = $isUpdate ? 'nullable|file|mimes:mp4,webm,mov|max:20480'
                : 'required|file|mimes:mp4,webm,mov|max:20480';
        }

        $request->validate($rules);

        $banner->title  = $request->title;
        $banner->type   = $request->type;
        $banner->status = $banner->status ?? 'active';

        // FILE HANDLE - Image
        if ($request->type === 'image' && $request->hasFile('image_file')) {

            $file = $request->file('image_file');
            $name = time().'_'.uniqid().'.'.$file->getClientOriginalExtension();
            $file->storeAs('banners', $name, 'public');
            $banner->filename = $name;
        }

        // FILE HANDLE - Video
        if ($request->type === 'video' && $request->hasFile('video_file')) {

            $file = $request->file('video_file');
            $name = time().'_'.uniqid().'.'.$file->getClientOriginalExtension();
            $file->storeAs('banners', $name, 'public');
            $banner->filename = $name;
        }

        $banner->save();

        return redirect()->route('banners')
            ->with('success', $isUpdate ? 'Banner updated successfully' : 'Banner created successfully');
    }




    public function show($id)
    {
        $banner = Banner::findOrFail($id);
        return view('admin.banners.show', compact('banner'));
    }


    public function delete($id)
    {
        try {
            $banner = Banner::findOrFail($id);

            // Delete the file from storage
            if (Storage::exists('public/banners/' . $banner->filename)) {
                Storage::delete('public/banners/' . $banner->filename);
            }

            // Delete the database record
            $banner->delete();

            return response()->json(['success' => true, 'message' => 'Banner deleted successfully']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error deleting banner'], 500);
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

}
