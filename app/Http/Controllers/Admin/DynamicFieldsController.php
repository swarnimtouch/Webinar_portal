<?php

namespace App\Http\Controllers\Admin;

use App\Models\DynamicFields;
use App\Models\Events;
use App\Models\Attribute;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

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
            'attributes' => Attribute::where('status', 'active')->orderBy('name')->get(),
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
            $eventId = $this->eventIdFor($request);

            if ($request->filled('order_data')) {
                $orderData = json_decode($request->order_data, true);
                foreach ($orderData as $item) {
                    DynamicFields::where('event_id', $eventId)->where('id', $item['id'])
                        ->update(['index_no' => $item['index_no']]);
                }
            }

            if ($request->has('fields')) {
                foreach ($request->fields as $fieldId => $fieldData) {
                    DynamicFields::where('event_id', $eventId)->where('id', $fieldId)->update([
                        'label' => $fieldData['label'] ?? '',
                        'is_required' => $fieldData['is_required'] ?? 0,
                        'status' => $fieldData['status'] ?? 'inactive',
                    ]);
                }
            }

            DynamicFields::where('event_id', $eventId)
                ->whereIn('field_name', ['email', 'mobile_number', 'password'])
                ->update(['login_with' => 0]);

            if ($request->filled('login_with')) {
                DynamicFields::where('event_id', $eventId)->where('id', $request->login_with)
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

    public function store(Request $request)
    {
        $eventId = $this->eventIdFor($request);
        $data = $request->validate([
            'label' => ['required', 'string', 'max:255'],
            'field_name' => [
                'nullable', 'string', 'max:255', 'regex:/^[a-z][a-z0-9_]*$/',
                Rule::unique('dynamic_fields')->where(fn ($query) => $query->where('event_id', $eventId)),
            ],
            'attribute_id' => ['required', Rule::exists('attributes', 'id')->where('status', 'active')],
            'options' => ['nullable', 'string'],
            'is_required' => ['nullable', 'boolean'],
        ]);

        $attribute = Attribute::findOrFail($data['attribute_id']);
        $fieldName = $data['field_name'] ?: Str::snake(Str::ascii($data['label']));
        if (!preg_match('/^[a-z][a-z0-9_]*$/', $fieldName)) {
            return back()->withErrors(['field_name' => 'Field name must start with a letter and contain only lowercase letters, numbers, and underscores.'])->withInput();
        }
        if (DynamicFields::where('event_id', $eventId)->where('field_name', $fieldName)->exists()) {
            return back()->withErrors(['field_name' => 'This field name already exists for the selected event.'])->withInput();
        }

        $inputValue = null;
        if (in_array($attribute->type, ['select', 'radio', 'checkbox'])) {
            $options = collect(preg_split('/\r\n|\r|\n|,/', $data['options'] ?? ''))
                ->map(fn ($option) => trim($option))->filter()->values();
            if ($options->isEmpty()) {
                return back()->withErrors(['options' => 'Add at least one option for this input type.'])->withInput();
            }
            $inputValue = $options->mapWithKeys(fn ($option) => [$option => $option])->toJson();
        }

        DynamicFields::create([
            'event_id' => $eventId,
            'index_no' => DynamicFields::where('event_id', $eventId)->max('index_no') + 1,
            'field_name' => $fieldName,
            'label' => $data['label'],
            'attribute_id' => $attribute->id,
            'input_value' => $inputValue,
            'is_required' => $request->boolean('is_required'),
            'type' => 'custom',
            'status' => 'active',
        ]);

        return redirect()->route('admin.dynamic_fields', ['event_id' => $eventId])
            ->with('success', 'Dynamic field added successfully.');
    }

    public function destroy(Request $request, int $id)
    {
        $field = DynamicFields::where('id', $id)->where('event_id', $this->eventIdFor($request))->firstOrFail();
        abort_if($field->type !== 'custom', 422, 'Default fields cannot be deleted.');
        $field->delete();

        return response()->json(['message' => 'Dynamic field deleted successfully.']);
    }

    private function eventIdFor(Request $request): int
    {
        $authUser = auth()->user();
        $eventId = $authUser->type === 'sub_admin' ? $authUser->event_id : $request->integer('event_id');
        abort_unless($eventId && Events::whereKey($eventId)->exists(), 422, 'Please select a valid event.');

        return $eventId;
    }
}
