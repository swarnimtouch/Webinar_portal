<?php

namespace App\Http\Controllers\Admin;

use App\Models\DynamicFields;
use App\Models\Events;
use Illuminate\Http\Request;

class DynamicFieldsController extends Controller
{
    public function index(Request $request)
    {
        $authUser = auth()->user();

        if ($authUser->type === 'sub_admin') {
            $fields = DynamicFields::where('event_id', $authUser->event_id)
                ->orderBy('index_no')
                ->get();
            $selectedEventId = $authUser->event_id;
            $events = collect();
        } else {
            $events = Events::orderBy('name')->get();
            $selectedEventId = $request->get('event_id');


            $fields = $selectedEventId
                ? DynamicFields::where('event_id', $selectedEventId)
                    ->orderBy('index_no')
                    ->get()
                : collect();
        }

        return view('admin.dynamic_fields.index', [
            'fields' => $fields,
            'events' => $events,
            'selectedEventId' => $selectedEventId,
            'maxIndex' => $fields->count(),
            'title' => __('Dynamic Fields'),
            'breadcrumb' => breadcrumb([
                __('Dynamic Fields') => route('admin.dynamic_fields')
            ])
        ]);
    }

    public function save(Request $request)
    {
        try {
            $authUser = auth()->user();

            if ($request->filled('order_data')) {
                $orderData = json_decode($request->order_data, true);
                foreach ($orderData as $item) {
                    DynamicFields::where('id', $item['id'])
                        ->update(['index_no' => $item['index_no']]);
                }
            }

            if ($request->has('fields')) {
                foreach ($request->fields as $fieldId => $fieldData) {
                    DynamicFields::where('id', $fieldId)->update([
                        'label' => $fieldData['label'] ?? '',
                        'is_required' => $fieldData['is_required'] ?? 0,
                        'status' => $fieldData['status'] ?? 'inactive',
                    ]);
                }
            }

            $eventId = $request->filled('event_id')
                ? $request->event_id
                : $authUser->event_id;

            DynamicFields::where('event_id', $eventId)
                ->whereIn('field_name', ['email', 'mobile_number', 'password'])
                ->update(['login_with' => 0]);

            if ($request->filled('login_with')) {
                DynamicFields::where('id', $request->login_with)
                    ->update(['login_with' => 1]);
            }

            if ($request->filled('password_required') && $request->password_required == 1) {
                DynamicFields::where('field_name', 'password')
                    ->where('event_id', $eventId)
                    ->update(['login_with' => 1]);
            }

            return response()->json(['message' => 'Dynamic Fields Save successfully!']);

        } catch (\Exception $e) {
            return response()->json(['message' => 'Error: ' . $e->getMessage()], 500);
        }
    }
}
