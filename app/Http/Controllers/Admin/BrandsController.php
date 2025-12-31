<?php

namespace App\Http\Controllers\Admin;

use App\Models\Brands;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BrandsController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    public function index()
    {
        $brands = Brands::latest()->get();

        $title = 'Brands';
        $breadcrumbs = [
            'Brands' => ''
        ];

        return view('admin.brands.index', compact(
            'brands',
            'title',
            'breadcrumbs'
        ));
    }

    public function create($id = null)
    {
        $brand = $id ? Brands::findOrFail($id) : new Brands();


        if ($id) {
            // Edit
            $title = 'Brands';
            $breadcrumbs = [
                'Brands' => route('brand'),
                'Edit Brands' => ''
            ];
        } else {
            // Create
            $title = 'Brands';
            $breadcrumbs = [
                'Brands' => route('brand'),
                'Brand' => ''
            ];
        }

        return view('admin.brands.add_edit', compact(
            'brand',
            'title',
            'breadcrumbs'
        ));
    }

    public function store(Request $request, $id = null)
    {
        $brand = $id ? Brands::findOrFail($id) : new Brands();
        $isUpdate = $brand->exists;

        // Validation rules - file required only on create or if not exists
        $rules = [
            'title' => 'required|string|max:255',
        ];

        // File validation - required in create mode, optional in edit mode
        if (!$isUpdate) {
            $rules['filename'] = 'required|file|mimes:jpg,jpeg,png,gif,webp,mp4,mov,avi,webm|max:20480';
        } else {
            $rules['filename'] = 'nullable|file|mimes:jpg,jpeg,png,gif,webp,mp4,mov,avi,webm|max:20480';
        }

        $request->validate($rules);

        $brand->title = $request->title;
        $brand->status = $brand->status ?? 'active';

        // Handle file upload
        if ($request->hasFile('filename')) {
            // Delete old file if exists
            if ($brand->filename && Storage::disk('public')->exists('brands/' . $brand->filename)) {
                Storage::disk('public')->delete('brands/' . $brand->filename);
            }

            $file = $request->file('filename');

            // Auto-detect type
            $mimeType = $file->getMimeType();
            $type = str_starts_with($mimeType, 'image/') ? 'image' : 'video';

            $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->storeAs('brands', $filename, 'public');

            $brand->filename = $filename;
            $brand->type = $type;
        }

        $brand->save();

        return redirect()->route('brand')
            ->with('success', $isUpdate ? 'Brand updated successfully' : 'Brand created successfully');
    }

    public function delete($id)
    {
        try {
            $brands = Brands::findOrFail($id);

            // Delete the file from storage
            if (Storage::exists('public/brands/' . $brands->filename)) {
                Storage::delete('public/brands/' . $brands->filename);
            }

            // Delete the database record
            $brands->delete();

            return response()->json(['success' => true, 'message' => 'Brands deleted successfully']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error deleting Brands'], 500);
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
                // Delete the file from storage
                if (Storage::exists('public/brands/' . $brand->filename)) {
                    Storage::delete('public/brands/' . $brand->filename);
                }

                // Delete the database record
                $brand->delete();
            }

            return response()->json(['success' => true, 'message' => 'Brands deleted successfully']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error deleting Bramds'], 500);
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
}
