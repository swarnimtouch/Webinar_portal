<?php

namespace App\Http\Controllers\Admin;

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
        $speakers = Speakers::latest()->get();

        $title = 'Speakers';
        $breadcrumbs = [
            'Speakers' => ''
        ];

        return view('admin.speakers.index', compact(
            'speakers',
            'title',
            'breadcrumbs'
        ));
    }

    /**
     * Add / Edit speaker (same breadcrumb)
     */
    public function create($id = null)
    {
        $speaker = $id ? Speakers::findOrFail($id) : null;

        $title = 'Speakers';
        $breadcrumbs = [
            'Speakers' => route('speakers')
        ];
        if ($id) {
            // Edit
            $title = 'Speakers';
            $breadcrumbs = [
                'Speakers' => route('speakers'),
                'Edit Speakers' => ''
            ];
        } else {
            // Create
            $title = 'Speakers';
            $breadcrumbs = [
                'Speakers' => route('speakers'),
                'Speakers' => ''
            ];
        }
        return view('admin.speakers.add_edit', compact(
            'speaker',
            'title',
            'breadcrumbs'
        ));
    }


    /**
     * Store/Update speaker.
     * Single method handles both create and update.
     */
    public function store(Request $request, $id = null)
    {
        $speaker = $id ? Speakers::findOrFail($id) : null;

        // Custom validation rule for image
        $imageRule = 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120';

        // If creating new speaker, image is required
        if (!$speaker) {
            $imageRule = 'required|image|mimes:jpeg,png,jpg,gif|max:5120';
        }
        // If editing and image was removed and no new image uploaded, make it required
        elseif ($request->input('image_removed') == '1' && !$request->hasFile('filename')) {
            $imageRule = 'required|image|mimes:jpeg,png,jpg,gif|max:5120';
        }
        // If editing speaker has no existing image, image is required
        elseif ($speaker && !$speaker->filename && !$request->hasFile('filename')) {
            $imageRule = 'required|image|mimes:jpeg,png,jpg,gif|max:5120';
        }

        $validated = $request->validate([
            'name'     => 'required|string|max:255',
            'line1'    => 'required|string|max:255',
            'filename' => $imageRule,
            'status'   => 'required|in:active,inactive'
        ]);

        // If updating, use existing speaker, otherwise create new instance
        if (!$speaker) {
            $speaker = new Speakers();
        }

        // Update basic fields
        $speaker->name = $request->name;
        $speaker->line1 = $request->line1;
        $speaker->line2 = $request->line2;
        $speaker->line3 = $request->line3;
        $speaker->status = $request->status ?? 'active';

        // Handle file upload
        if ($request->hasFile('filename')) {
            // Delete old image if exists (for update case)
            if ($speaker->filename && Storage::disk('public')->exists('speakers/' . $speaker->filename)) {
                Storage::disk('public')->delete('speakers/' . $speaker->filename);
            }

            $file = $request->file('filename');
            $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->storeAs('speakers', $filename, 'public');
            $speaker->filename = $filename;
        }
        // If image was removed but no new image uploaded (update case)
        elseif ($request->input('image_removed') == '1' && $speaker->exists) {
            // Delete old image
            if ($speaker->filename && Storage::disk('public')->exists('speakers/' . $speaker->filename)) {
                Storage::disk('public')->delete('speakers/' . $speaker->filename);
            }
            $speaker->filename = null;
        }

        $speaker->save();

        $message = $id ? 'Speaker updated successfully!' : 'Speaker created successfully!';

        return redirect()->route('speakers')->with('success', $message);
    }

    /**
     * Remove the specified speaker.
     */
    public function delete($id)
    {
        $speaker = Speakers::findOrFail($id);

        // Delete image file if exists
        if ($speaker->filename && Storage::disk('public')->exists('speakers/' . $speaker->filename)) {
            Storage::disk('public')->delete('speakers/' . $speaker->filename);
        }

        $speaker->delete();

        return redirect()->route('speakers')
            ->with('success', 'Speaker deleted successfully!');
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
            // Delete image file if exists
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
}
