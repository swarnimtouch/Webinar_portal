<?php

namespace App\Http\Controllers\Admin;

use App\Models\DynamicFields;
use App\Models\Events;
use App\Models\HomeSetting;
use App\Support\EventStorage;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class EventSettingController extends Controller
{
    public function index()
    {
        $user = Auth::guard('admin')->user();
        $response = [
            'event' => Events::findOrFail($user->event_id),
            'title' => __('Settings'),
            'breadcrumb' => breadcrumb([
                __('Settings') => ''
            ]),
        ];

        return view('admin.event_setting.index', $response);
    }
    public function save(Request $request)
    {
        $user = Auth::guard('admin')->user();
        $id = $user->event_id??null;
        $event = Events::findOrFail($id);
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email'],
            'phone' => ['required', 'string', 'max:255'],
            'theme_color' => ['required', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'favicon' => [
                Rule::requiredIf(fn() => !$id),
                'file',
                'mimes:jpg,jpeg,png,webp',
                'max:5120',
            ],
            'logo' => [
                Rule::requiredIf(fn() => !$id),
                'file',
                'mimes:jpg,jpeg,png,webp',
                'max:5120',
            ],
            'player_id' => ['nullable', 'string'],
            'player_type' => ['nullable', 'required_with:player_id', 'string'],
            'player_iframe' => ['nullable', 'string'],
            'publish_date' => ['required', 'date'],
            'start_time' => ['required'],
            'end_time' => ['required'],
            'active_user_from' => [Rule::requiredIf($request->boolean('is_log_attendance')), 'nullable', 'date'],
            'active_user_to' => [Rule::requiredIf($request->boolean('is_log_attendance')), 'nullable', 'date', 'after:active_user_from'],
        ]);

        $event->name = $request->name ?? null;
        $event->email = $request->email ?? null;
        $event->phone = $request->phone ?? null;
        $event->theme_color = strtolower($request->theme_color);
        $event->description = $request->description ?? null;

        if ($request->hasFile('favicon')) {
            $file = $request->file('favicon');
            $name = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            EventStorage::delete($event->getRawOriginal('favicon'), 'events/' . $event->getRawOriginal('favicon'));
            $event->favicon = EventStorage::store($file, $event, 'event-assets', $name);
        }

        if ($request->hasFile('logo')) {
            $file = $request->file('logo');
            $name = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            EventStorage::delete($event->getRawOriginal('logo'), 'events/' . $event->getRawOriginal('logo'));
            $event->logo = EventStorage::store($file, $event, 'event-assets', $name);
        }

        $event->footer_text = $request->footer_text ?? null;
        $event->player_id = $request->player_id ?? null;
        $event->player_type = $request->player_type ?? null;
        $event->player_iframe = $request->player_iframe ?? null;
        $event->publish_date = $request->publish_date ? Carbon::parse($request->publish_date)->format('Y-m-d') : null;
        $event->start_time = $request->start_time ? Carbon::parse($request->start_time)->format('Y-m-d H:i:s') : null;
        $event->end_time = $request->end_time ? Carbon::parse($request->end_time)->format('Y-m-d H:i:s') : null;
        $event->active_user_from = $request->boolean('is_log_attendance') && $request->active_user_from
            ? Carbon::parse($request->active_user_from)->format('Y-m-d H:i:s') : null;
        $event->active_user_to = $request->boolean('is_log_attendance') && $request->active_user_to
            ? Carbon::parse($request->active_user_to)->format('Y-m-d H:i:s') : null;
        $event->is_log_attendance = $request->is_log_attendance ?? 0;
        $event->enable_live_chat = $request->boolean('enable_live_chat');
        $event->enable_comments = $request->boolean('enable_comments');
        $event->enable_polls = $request->boolean('enable_polls');
        $event->enable_feedback = $request->boolean('enable_feedback');
        $event->save();
        $isDynamicFieldsExist = DynamicFields::where('event_id', $event->id)->count();
        if ($isDynamicFieldsExist == 0) {
            foreach (get_dynamic_fields() as $key => $fields) {
                $fields['event_id'] = $event->id;
                $fields['is_required'] = 1;
                $fields['created_at'] = now();
                $fields['updated_at'] = now();
                DynamicFields::insert($fields);
            }
        }
        return redirect()->route('admin.event_setting')
            ->with('success', 'Setting Saved Successfully');
    }

}
