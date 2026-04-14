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
        $homeSetting = HomeSetting::first();

        if (!$homeSetting) {
            $homeSetting = new HomeSetting();
        }

        $response = [
            'homeSetting' => $homeSetting,
            'title' => __('Home Settings'),
            'breadcrumb' => breadcrumb([
                __('Home Settings') => ''
            ]),
        ];

        return view('admin.home_setting.index', $response);
    }


    /**
     * Show the form for creating/editing home setting
     */


    /**
     * Store or update a home setting
     */
    public function save(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'player_type' => 'required|in:youtube,vimeo,other',
            'url' => 'required',
            'title' => 'nullable|string|max:255',
            'player_id' => 'required|string|max:255',
            'publish_date' => 'required|date',
            'about_us' => 'nullable|string',
            'event_start_time' => 'nullable|date',
            'event_end_time' => 'nullable|date|after_or_equal:event_start_time',
            'active_from_date' => 'nullable|date',
            'active_to_date' => 'nullable|date|after_or_equal:active_from_date',
            'user_attendance' => 'nullable|boolean'
        ], [
            'player_type.required' => 'Player type is required',
            'player_type.in' => 'Invalid player type selected',
            'url.required' => 'Video iframe code is required',
            'player_id.required' => 'Player ID is required',
            'publish_date.required' => 'Publish date is required',
            'about_us.required' => 'About us is required',
            'event_start_time.required' => 'Event start time is required',
            'event_end_time.required' => 'Event end time is required',
            'event_end_time.after_or_equal' => 'Event end time must be after or equal to start time',
            'active_from_date.required' => 'Active from date is required',
            'active_to_date.required' => 'Active to date is required',
            'active_to_date.after_or_equal' => 'Active to date must be after or equal to from date'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $homeSetting = HomeSetting::first() ?? new HomeSetting();

            $homeSetting->title = $request->title;
            $homeSetting->player_type = $request->player_type;
            $homeSetting->url = $request->url;
            $homeSetting->player_id = $request->player_id;
            $homeSetting->publish_date = $request->publish_date;
            $homeSetting->about_us = $request->about_us;
            $homeSetting->event_start_time = $request->event_start_time ?: null;
            $homeSetting->event_end_time = $request->event_end_time ?: null;
            $homeSetting->active_from_date = $request->active_from_date ?: null;
            $homeSetting->active_to_date = $request->active_to_date ?: null;
            $homeSetting->user_attendance = $request->has('user_attendance') ? 1 : 0;

            $homeSetting->save();

            $message = $homeSetting->wasRecentlyCreated
                ? __('Home setting created successfully!')
                : __('Home setting updated successfully!');

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

}
