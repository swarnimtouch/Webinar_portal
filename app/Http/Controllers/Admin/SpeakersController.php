<?php

namespace App\Http\Controllers\Admin;

use App\Models\Banner;
use App\Models\Events;
use App\Models\Speakers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SpeakersController extends Controller
{
    /**
     * Display listing of speakers.
     */
    public function index()
    {
        return view('admin.speakers.index', ['title' => __('Speakers'), 'breadcrumb' => breadcrumb([__('Speakers') => route('admin.speakers')])]);
    }

    /**
     * Add / Edit speaker (same breadcrumb)
     */
    public function addEditForm($id = null)
    {
        $speaker = $id ? Speakers::findOrFail($id) : null;
        $events = Events::get();
        $response = [
            'speaker' => $speaker,
            'events' => $events,
            'title' => __('Speakers'),
            'breadcrumb' => breadcrumb([__('Speakers') => route('admin.speakers'), ($id ? 'Edit' : 'Add' . ' Speakers') => '']),
        ];
        return view('admin.speakers.add_edit', $response);
    }


    /**
     * Store/Update speaker.
     * Single method handles both create and update.
     */
    public function save(Request $request, $id = null)
    {
        $speaker = $id ? Speakers::findOrFail($id) : new Speakers();
        $validated = $request->validate([
            'event_id' => 'nullable|exists:events,id',
            'name' => 'required|string|max:255',
            'line1' => 'required|string|max:255',
            'filename' => $id
                ? 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120'
                : 'required|image|mimes:jpg,jpeg,png,webp|max:5120',
            'status' => 'required|in:active,inactive'
        ]);
        $speaker->event_id = $request->event_id;
        $speaker->name = $request->name;
        $speaker->line1 = $request->line1;
        $speaker->line2 = $request->line2;
        $speaker->line3 = $request->line3;
        $speaker->status = $request->status ?? 'active';
        if ($request->hasFile('filename')) {
            if ($speaker->filename && Storage::disk('public')->exists('speakers/' . $speaker->filename)) {
                Storage::disk('public')->delete('speakers/' . $speaker->filename);
            }
            $file = $request->file('filename');
            $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->storeAs('speakers', $filename, 'public');
            $speaker->filename = $filename;
        } elseif ($request->input('image_removed') == '1' && $speaker->exists) {
            if ($speaker->filename && Storage::disk('public')->exists('speakers/' . $speaker->filename)) {
                Storage::disk('public')->delete('speakers/' . $speaker->filename);
            }
            $speaker->filename = null;
        }
        $speaker->save();

        return redirect()->route('admin.speakers')->with('success', 'Speaker Saved successfully');
    }

    /**
     * Remove the specified speaker.
     */
    public function delete($id)
    {
        try {
            $speaker = Speakers::findOrFail($id);
            if (Storage::exists('public/speakers/' . $speaker->filename)) {
                Storage::delete('public/speakers/' . $speaker->filename);
            }
            $speaker->delete();

            return response()->json(['success' => true, 'message' => 'Speaker deleted successfully']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error deleting speaker'], 500);
        }
    }

    /**
     * Delete multiple speakers.
     */
    public function deleteMultiple(Request $request)
    {
        $ids = $request->input('ids', []);

        if (empty($ids)) {
            return response()->json(['success' => false, 'message' => 'No speakers selected']);
        }

        $speakers = Speakers::whereIn('id', $ids)->get();

        foreach ($speakers as $speaker) {
            if ($speaker->filename && Storage::disk('public')->exists('speakers/' . $speaker->filename)) {
                Storage::disk('public')->delete('speakers/' . $speaker->filename);
            }
        }
        Speakers::whereIn('id', $ids)->delete();
        return response()->json(['success' => true, 'message' => 'Speakers deleted successfully!']);
    }

    /**
     * Toggle speaker status.
     */
    public function toggleStatus($id)
    {
        $speaker = Speakers::findOrFail($id);
        $speaker->status = $speaker->status === 'active' ? 'inactive' : 'active';
        $speaker->save();

        return response()->json([
            'success' => true,
            'status' => $speaker->status,
            'message' => 'Status updated successfully!'
        ]);
    }

    public function datatable(Request $request)
    {
        $user = auth()->user();

        $query = Speakers::with('event');

        if ($user->type === 'sub_admin') {
            $query->where('event_id', $user->event_id);
        }

        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('line1', 'like', "%{$search}%")
                    ->orWhere('line2', 'like', "%{$search}%")
                    ->orWhere('line3', 'like', "%{$search}%")
                    ->orWhereHas('event', function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%");
                    });
            });
        }

        $total = $query->count();

        if ($request->has('order')) {
            $columns = $request->columns;

            foreach ($request->order as $order) {
                $columnIndex = $order['column'];
                $columnName = $columns[$columnIndex]['data'];
                $direction = $order['dir'];

                $dbColumn = match ($columnName) {
                    'name' => 'name',
                    default => 'id'
                };

                $query->orderBy($dbColumn, $direction);
            }
        } else {
            $query->orderBy('id', 'desc');
        }

        $length = $request->input('length', 10);
        $start = $request->input('start', 0);

        $speakers = $query->skip($start)->take($length)->get();

        $data = $speakers->map(function ($speaker) {
            return [
                'id' => $speaker->id,
                'event' => $speaker->event->name ?? 'N/A',
                'name' => $speaker->name,
                'media_url' => $speaker->media_url,
                'line1' => $speaker->line1,
                'line2' => $speaker->line2 ?? 'N/A',
                'line3' => $speaker->line3 ?? 'N/A',
                'created_at' => $speaker->created_at->format('d M Y'),
                'status' => $speaker->status,
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
