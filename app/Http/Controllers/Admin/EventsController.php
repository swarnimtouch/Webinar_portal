<?php

namespace App\Http\Controllers\Admin;

use App\Models\DynamicFields;
use App\Models\Events;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class EventsController
{
    public function index()
    {
        return view('admin.events.index', ['title' => __('Events'), 'breadcrumb' => breadcrumb([__('Events') => route('admin.events')])]);
    }

    public function addEditForm($id = null)
    {
        $event = $id ? Events::findOrFail($id) : new Events();

        $response = [
            'event' => $event,
            'title' => __('Event'),
            'breadcrumb' => breadcrumb([__('Events') => route('admin.events'), ($id ? 'Edit' : 'Add' . ' Event') => '']),
        ];
        return view('admin.events.add_edit', $response);
    }


    public function save(Request $request, $id = null)
    {
        $event = $id ? Events::findOrFail($id) : new Events();
        $request->validate([
            'domain' => ['required', 'string', 'max:255'],
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email'],
            'phone' => ['required', 'string', 'max:255'],
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
            'player_id' => ['required', 'string'],
            'player_type' => ['required', 'string'],
            'player_iframe' => ['required', 'string'],
            'publish_date' => ['required', 'date'],
            'start_time' => ['required'],
            'end_time' => ['required'],
        ]);

        $event->slug = generate_slug($request->name) ?? null;
        $event->domain = $request->domain ?? null;
        $event->name = $request->name ?? null;
        $event->email = $request->email ?? null;
        $event->phone = $request->phone ?? null;
        $event->description = $request->description ?? null;

        if ($request->hasFile('favicon')) {
            $file = $request->file('favicon');
            $name = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->storeAs('events', $name, 'public');
            $event->favicon = $name;
        }

        if ($request->hasFile('logo')) {
            $file = $request->file('logo');
            $name = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->storeAs('events', $name, 'public');
            $event->logo = $name;
        }

        $event->footer_text = $request->footer_text ?? null;
        $event->player_id = $request->player_id ?? null;
        $event->player_type = $request->player_type ?? null;
        $event->player_iframe = $request->player_iframe ?? null;
        $event->publish_date = $request->publish_date ? Carbon::parse($request->publish_date)->format('Y-m-d') : null;
        $event->start_time = $request->start_time ? Carbon::parse($request->start_time)->format('Y-m-d H:i:s') : null;
        $event->end_time = $request->end_time ? Carbon::parse($request->end_time)->format('Y-m-d H:i:s') : null;
        $event->active_user_from = $request->active_user_from ? Carbon::parse($request->active_user_from)->format('Y-m-d H:i:s') : null;
        $event->active_user_to = $request->active_user_to ? Carbon::parse($request->active_user_to)->format('Y-m-d H:i:s') : null;
        $event->is_log_attendance = $request->is_log_attendance ?? 0;
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
        return redirect()->route('admin.events')
            ->with('success', 'Event Saved Successfully');
    }


    public function delete($id)
    {
        try {
            $event = Events::findOrFail($id);
            if (Storage::exists('public/events/' . $event->favicon)) {
                Storage::delete('public/events/' . $event->favicon);
            }
            if (Storage::exists('public/events/' . $event->logo)) {
                Storage::delete('public/events/' . $event->logo);
            }
            $event->delete();

            return response()->json(['success' => true, 'message' => 'Event deleted successfully']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error deleting event'], 500);
        }
    }


    public function deleteMultiple(Request $request)
    {
        try {
            $ids = $request->input('ids', []);
            if (empty($ids)) {
                return response()->json(['success' => false, 'message' => 'No events selected'], 400);
            }
            $events = Events::whereIn('id', $ids)->get();
            foreach ($events as $event) {
                if (Storage::exists('public/events/' . $event->favicon)) {
                    Storage::delete('public/events/' . $event->favicon);
                }
                if (Storage::exists('public/events/' . $event->logo)) {
                    Storage::delete('public/events/' . $event->logo);
                }
                $event->delete();
            }
            return response()->json(['success' => true, 'message' => 'Events deleted successfully']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error deleting events'], 500);
        }
    }

    public function toggleStatus(Request $request, $id)
    {
        try {
            $event = Events::findOrFail($id);
            $event->status = $request->input('status');
            $event->save();
            return response()->json([
                'success' => true,
                'message' => 'Status updated successfully',
                'status' => $event->status
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
        $query = Events::query();
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('domain', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            });
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
                    'name' => 'name',
                    'slug' => 'slug',
                    'domain' => 'domain',
                    'email' => 'email',
                    'phone' => 'phone',
                    default => 'id'
                };
                $query->orderBy($dbColumn, $direction);
            }
        } else {
            $query->orderBy('id', 'desc');
        }
        $length = $request->input('length', 10);
        $start = $request->input('start', 0);
        $events = $query->skip($start)->take($length)->get();
        $data = $events->map(function ($event) {
            return [
                'id' => $event->id,
                'logo' => $event->logo,
                'name' => $event->name,
                'slug' => $event->slug,
                'domain' => $event->domain,
                'email' => $event->email,
                'phone' => $event->phone,
                'status' => $event->status,
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
