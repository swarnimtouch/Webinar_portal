<?php

namespace App\Http\Controllers\Admin;

use App\Models\Banner;
use App\Models\Events;
use App\Support\EventStorage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class BannerController extends Controller
{
    public function index()
    {
        $banner = Banner::with('event')->get()->unique('event_id')->values();
        return view('admin.banners.index', ['banner' => $banner, 'title' => __('Banners'), 'breadcrumb' => breadcrumb([__('Banners') => route('admin.banners')])]);
    }

    public function addEditForm($id = null)
    {
        $banner = $id ? Banner::findOrFail($id) : new Banner();
        $events = Events::get();

        $response = [
            'banner' => $banner,
            'events' => $events,
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
            'event_id' => 'required|exists:events,id',

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
        $banner->event_id = $request->event_id;
        $event = Events::findOrFail($request->integer('event_id'));

        if ($request->type === 'image' && $request->hasFile('image_file')) {

            $file = $request->file('image_file');
            $name = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            EventStorage::delete($banner->filename, 'banners/' . $banner->filename);
            $banner->filename = EventStorage::store($file, $event, 'banners', $name);
        }

        if ($request->type === 'video' && $request->hasFile('video_file')) {
            $file = $request->file('video_file');
            $name = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            EventStorage::delete($banner->filename, 'banners/' . $banner->filename);
            $banner->filename = EventStorage::store($file, $event, 'banners', $name);
        }

        $banner->save();

        return redirect()->route('admin.banners')
            ->with('success', 'Banner Saved Successfully');
    }


    public function delete($id)
    {
        try {
            $banner = Banner::findOrFail($id);
            EventStorage::delete($banner->filename, 'banners/' . $banner->filename);
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
                EventStorage::delete($banner->filename, 'banners/' . $banner->filename);
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
        $user = auth()->user();

        $query = Banner::with('event');

        if ($user->type === 'sub_admin') {
            $query->where('event_id', $user->event_id);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('type', 'like', "%{$search}%")
                    ->orWhereHas('event', function ($q2) use ($search) {
                        $q2->where('name', 'like', "%{$search}%");
                    });
            });
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('event')) {
            $query->where('event_id', $request->event);
        }

        $total = $query->count();

        if ($request->has('order')) {
            $columns = $request->columns;
            foreach ($request->order as $order) {
                $dbColumn = match ($columns[$order['column']]['data']) {
                    'title' => 'title',
                    'type' => 'type',
                    default => 'id'
                };
                $query->orderBy($dbColumn, $order['dir']);
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
                'event' => optional($banner->event)->name ?? 'N/A',
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
