<?php

namespace App\Http\Controllers\Admin;

use App\Models\HomeSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class HomeSettingController extends Controller
{
    /**
     * Display a listing of home settings
     */
    public function index()
    {
        $response = [
            'title' => __('Home Settings'),
            'breadcrumb' => breadcrumb([__('Home Settings') => '']),
        ];
        return view('admin.home_setting.index', $response);
    }

    /**
     * Show the form for creating/editing home setting
     */
    public function addEditForm($id = null)
    {
        $home_setting = $id ? HomeSetting::findOrFail($id) : new HomeSetting();

        $response = [
            'homeSetting' => $home_setting,
            'title' => __('Home Settings'),
            'breadcrumb' => breadcrumb([
                __('Home Settings') => route('admin.home_setting'),
                ($id ? 'Edit' : 'Add') . ' Home Setting' => ''
            ]),
        ];
        return view('admin.home_setting.add_edit', $response);
    }

    /**
     * Store or update a home setting
     */
    public function save(Request $request, $id = null)
    {
        $validator = Validator::make($request->all(), [
            'player_type' => 'required|in:youtube,vimeo,other',
            'url' => 'required',
            'title' => 'nullable|string|max:255',
            'player_id' => 'nullable|string|max:255',
            'publish_date' => 'nullable|date',
            'event_start_time' => 'nullable|date',
            'event_end_time' => 'nullable|date|after_or_equal:event_start_time',
            'active_from_date' => 'nullable|date',
            'active_to_date' => 'nullable|date|after_or_equal:active_from_date',
            'user_attendance' => 'nullable|boolean'
        ], [
            'player_type.required' => 'Player type is required',
            'player_type.in' => 'Invalid player type selected',
            'url.required' => 'Video URL is required',
            'url.url' => 'Please enter a valid URL',
            'event_end_time.after_or_equal' => 'Event end time must be after or equal to start time',
            'active_to_date.after_or_equal' => 'Active to date must be after or equal to from date'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $homeSetting = $id ? HomeSetting::findOrFail($id) : new HomeSetting();

            $homeSetting->title = $request->title;
            $homeSetting->player_type = $request->player_type;
            $homeSetting->url = $request->url;
            $homeSetting->player_id = $request->player_id;
            $homeSetting->publish_date = $request->publish_date;
            $homeSetting->event_start_time = $request->event_start_time;
            $homeSetting->event_end_time = $request->event_end_time;
            $homeSetting->active_from_date = $request->active_from_date;
            $homeSetting->active_to_date = $request->active_to_date;
            $homeSetting->user_attendance = $request->has('user_attendance') ? 1 : 0;

            $homeSetting->save();

            $message = $id
                ? __('Home setting updated successfully!')
                : __('Home setting created successfully!');

            return redirect()->route('admin.home_setting')
                ->with('success', $message);

        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['error' => __('An error occurred: ') . $e->getMessage()]);
        }
    }

    /**
     * DataTable for home settings listing
     */
    public function datatable(Request $request)
    {
        $query = HomeSetting::query();

        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('player_type', 'like', "%{$search}%")
                    ->orWhere('url', 'like', "%{$search}%");
            });
        }

        if ($request->has('player_type') && !empty($request->player_type)) {
            $query->where('player_type', $request->player_type);
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
                    'player_type' => 'player_type',
                    'publish_date' => 'publish_date',
                    'event_start_time' => 'event_start_time',
                    'event_end_time' => 'event_end_time',
                    'user_attendance' => 'user_attendance',
                    default => 'id'
                };

                $query->orderBy($dbColumn, $direction);
            }
        } else {
            $query->orderBy('id', 'desc');
        }

        $length = $request->input('length', 10);
        $start = $request->input('start', 0);
        $homeSettings = $query->skip($start)->take($length)->get();

        $data = $homeSettings->map(function ($homeSetting) {
            return [
                'id' => $homeSetting->id,
                'title' => $homeSetting->title ?? 'N/A',
                'player_type' => ucfirst($homeSetting->player_type),
                'url' => htmlspecialchars($homeSetting->url),
                'player_id' => $homeSetting->player_id ?? 'N/A',
                'publish_date' => $homeSetting->publish_date ? $homeSetting->publish_date->format('d M Y') : 'N/A',
                'event_start_time' => $homeSetting->event_start_time ? $homeSetting->event_start_time->format('d M Y h:i A') : 'N/A',
                'event_end_time' => $homeSetting->event_end_time ? $homeSetting->event_end_time->format('d M Y h:i A') : 'N/A',
                'active_from_date' => $homeSetting->active_from_date ? $homeSetting->active_from_date->format('d M Y h:i A') : 'N/A',
                'active_to_date' => $homeSetting->active_to_date ? $homeSetting->active_to_date->format('d M Y h:i A') : 'N/A',
                'user_attendance' => $homeSetting->user_attendance ? 'Yes' : 'No',
                'created_at' => $homeSetting->created_at->format('d M Y'),
            ];
        });

        return response()->json([
            'draw' => $request->input('draw', 1),
            'recordsTotal' => $total,
            'recordsFiltered' => $total,
            'data' => $data
        ]);
    }

    /**
     * Delete a home setting
     */
    public function delete($id)
    {
        try {
            $homeSetting = HomeSetting::findOrFail($id);
            $homeSetting->delete();

            return response()->json([
                'success' => true,
                'message' => __('Home setting deleted successfully!')
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => __('Error deleting home setting: ') . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete multiple home settings
     */
    public function deleteMultiple(Request $request)
    {
        try {
            $ids = $request->input('ids', []);

            if (empty($ids)) {
                return response()->json([
                    'success' => false,
                    'message' => __('No home settings selected')
                ], 400);
            }

            HomeSetting::whereIn('id', $ids)->delete();

            return response()->json([
                'success' => true,
                'message' => count($ids) . __(' home setting(s) deleted successfully!')
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => __('Error deleting home settings: ') . $e->getMessage()
            ], 500);
        }
    }
}
