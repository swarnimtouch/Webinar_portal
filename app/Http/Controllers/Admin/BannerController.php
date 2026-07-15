<?php

namespace App\Http\Controllers\Admin;

use App\Models\Banner;
use App\Models\Events;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;

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
        $authUser = auth()->user();
        $eventId = $authUser->type === 'sub_admin' ? $authUser->event_id : $request->event_id;
        Validator::make(['event_id' => $eventId], ['event_id' => ['required', 'exists:events,id']])->validate();

        $existing = $id ? Banner::findOrFail($id) : null;
        if ($existing && $authUser->type === 'sub_admin' && (int) $existing->event_id !== (int) $authUser->event_id) {
            abort(403);
        }

        $items = $request->input('banners', []);
        if (!is_array($items) || count($items) < 1) {
            return back()->withErrors(['banners' => 'Add at least one banner.'])->withInput();
        }
        if ($id) {
            $items = [reset($items)];
        }

        foreach (array_values($items) as $index => $item) {
            $imageFile = $request->file("banners.$index.image_file");
            $videoFile = $request->file("banners.$index.video_file");
            $type = $item['type'] ?? null;
            $videoSource = $item['video_source'] ?? 'upload';
            $keepsExistingImage = $existing && $existing->type === 'image' && $existing->filename;
            $keepsExistingVideoFile = $existing && $existing->type === 'video' && $existing->filename;
            $keepsExistingVideoUrl = $existing && $existing->type === 'video' && $existing->video_url;

            Validator::make(array_merge($item, [
                'image_file' => $imageFile,
                'video_file' => $videoFile,
            ]), [
                'title' => ['required', 'string', 'max:255'],
                'type' => ['required', Rule::in(['image', 'video'])],
                'video_source' => ['nullable', Rule::in(['upload', 'url'])],
                'image_file' => [Rule::requiredIf($type === 'image' && !$keepsExistingImage), 'nullable', 'file', 'mimes:jpg,jpeg,png,webp,gif', 'max:5120'],
                'video_file' => [Rule::requiredIf($type === 'video' && $videoSource === 'upload' && !$keepsExistingVideoFile), 'nullable', 'file', 'mimes:mp4,webm,mov', 'max:20480'],
                'video_url' => [Rule::requiredIf($type === 'video' && $videoSource === 'url' && !$keepsExistingVideoUrl), 'nullable', 'url', 'max:2048'],
            ], [], [
                'title' => 'banner '.($index + 1).' title',
                'image_file' => 'banner '.($index + 1).' image',
                'video_file' => 'banner '.($index + 1).' video',
                'video_url' => 'banner '.($index + 1).' video URL',
            ])->validate();

            $banner = $existing ?: new Banner();
            $oldFilename = $banner->filename;
            $banner->event_id = $eventId;
            $banner->title = $item['title'];
            $banner->type = $type;

            if ($type === 'image') {
                $banner->video_url = null;
                if ($imageFile) {
                    $banner->filename = $this->storeBannerFile($imageFile);
                }
            } elseif ($videoSource === 'url') {
                $banner->video_url = $item['video_url'] ?? $banner->video_url;
                $banner->filename = null;
            } else {
                $banner->video_url = null;
                if ($videoFile) {
                    $banner->filename = $this->storeBannerFile($videoFile);
                }
            }

            $banner->save();
            if ($oldFilename && $oldFilename !== $banner->filename) {
                Storage::disk('public')->delete('banners/'.$oldFilename);
            }
        }

        return redirect()->route('admin.banners')
            ->with('success', $id ? 'Banner updated successfully' : count($items).' banner(s) added successfully');
    }

    private function storeBannerFile($file): string
    {
        $name = time().'_'.uniqid().'.'.$file->getClientOriginalExtension();
        $file->storeAs('banners', $name, 'public');
        return $name;
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
                if (Storage::exists('public/banners/' . $banner->filename)) {
                    Storage::delete('public/banners/' . $banner->filename);
                }
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
