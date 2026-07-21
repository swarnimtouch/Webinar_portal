<?php

namespace App\Http\Controllers\Admin;

use App\Models\City;
use App\Models\Country;
use App\Models\DynamicFields;
use App\Models\Events;
use App\Models\State;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index()
    {
        $authUser = auth()->user();

        $dynamicFields = DynamicFields::where('status', 'active')
            ->when($authUser->type === 'sub_admin', fn($q) => $q->where('event_id', $authUser->event_id))
            ->orderBy('index_no')
            ->get()
            ->when($authUser->type !== 'sub_admin', fn($c) => $c->unique('field_name'));

        $excludeColumns = ['password'];
        $validDynamicFields = $dynamicFields->reject(fn ($field) => in_array($field->field_name, $excludeColumns))->values();

        $users = User::where('type', 'doctor')->get();
        $user = User::with('event')->get()->unique('event_id')->values();
        return view('admin.users.index', [
            'users' => $users,
            'user' => $user,
            'valid_dynamic_fields' => $validDynamicFields,
            'title' => __('Users'),
            'breadcrumb' => breadcrumb([__('Users') => route('admin.user.index')])
        ]);
    }

    public function addEditForm($id = null)
    {
        $authUser = auth()->user();
        $user = $id ? User::with('dynamicFieldValues')->findOrFail($id) : null;
        $events = Events::get();

        $fieldsQuery = DynamicFields::with('attribute_data')
            ->active()
            ->orderBy('index_no');

        $activeFields = match (true) {
            $authUser->type === 'sub_admin' => $fieldsQuery
                ->where('event_id', $authUser->event_id)
                ->get(),

            $authUser->type !== 'sub_admin' && $id && $user?->event_id => $fieldsQuery
                ->where('event_id', $user->event_id)
                ->get()
                ->unique('field_name'),

            default => collect(),
        };

        return view('admin.users.add_edit', [
            'user' => $user,
            'active_fields' => $activeFields,
            'events' => $events,
            'is_admin' => $authUser->type === 'admin',
            'title' => __('Users'),
            'breadcrumb' => breadcrumb([
                __('Users') => route('admin.user.index'),
                ($id ? 'Edit' : 'Add') . ' User' => ''
            ]),
        ]);
    }


    public function save(Request $request, $id = null)
    {
        $user = $id ? User::findOrFail($id) : null;

        $eventId = $request->event_id ?? auth()->user()->event_id;

        $activeFields = DynamicFields::where('status', 'active')
            ->where('event_id', $eventId)
            ->get();

        $fieldMapping = [
            'mobile_number' => 'mobile',
        ];

        $rules = [];
        $messages = [];

        foreach ($activeFields as $field) {
            $fieldName = $field->field_name;
            $dbFieldName = $fieldMapping[$fieldName] ?? $fieldName;

            if ($field->is_required) {
                if ($fieldName === 'email') {
                    $rules[$dbFieldName] = ['required', 'email', Rule::unique('users', 'email')->where('event_id', $eventId)->ignore($id)];
                } elseif ($fieldName === 'password') {
                    $rules['password'] = $id ? 'nullable|min:6' : 'required|min:6';
                } elseif ($fieldName === 'avatar') {
                    if ($id && $request->avatar_removed == '1' && !$request->hasFile('avatar')) {
                        $rules['avatar'] = 'required|image|mimes:jpg,jpeg,png,gif|max:5120';
                    } else {
                        $rules['avatar'] = $id
                            ? 'nullable|image|mimes:jpg,jpeg,png,gif|max:5120'
                            : 'required|image|mimes:jpg,jpeg,png,gif|max:5120';
                    }
                } elseif ($fieldName === 'mobile_number') {
                    $rules[$dbFieldName] = ['required', 'digits:10', Rule::unique('users', 'mobile')->where('event_id', $eventId)->ignore($id)];
                } else {
                    $rules[$dbFieldName] = 'required';
                }
            } else {
                if ($fieldName === 'email' && $request->has($dbFieldName)) {
                    $rules[$dbFieldName] = ['nullable', 'email', Rule::unique('users', 'email')->where('event_id', $eventId)->ignore($id)];
                } elseif ($fieldName === 'avatar' && $request->hasFile('avatar')) {
                    $rules['avatar'] = 'nullable|image|mimes:jpg,jpeg,png,gif|max:5120';
                } elseif ($fieldName === 'mobile_number' && $request->has($dbFieldName)) {
                    $rules[$dbFieldName] = ['nullable', 'digits:10', Rule::unique('users', 'mobile')->where('event_id', $eventId)->ignore($id)];
                }
            }

            $messages[$dbFieldName . '.required'] = $field->label . ' is required';
        }

        $rules['event_id'] = 'nullable';

        $request->validate($rules, $messages);

        $data = $request->except(['_token', '_method', 'avatar_removed', 'has_existing_avatar']);
        $userColumns = Schema::getColumnListing('users');
        $customValues = [];
        foreach ($activeFields as $field) {
            $column = $fieldMapping[$field->field_name] ?? $field->field_name;
            if (!in_array($column, $userColumns, true) && $field->field_name !== 'password') {
                $value = $request->input($field->field_name);
                if ($request->hasFile($field->field_name)) {
                    $value = $request->file($field->field_name)->store('dynamic-fields', 'public');
                } elseif (is_array($value)) {
                    $value = implode(', ', $value);
                }
                $customValues[$field->id] = $value;
                unset($data[$field->field_name]);
            }
        }

        $activeFieldNames = $activeFields->pluck('field_name');
        foreach (['country', 'state', 'city'] as $locationField) {
            if (!$activeFieldNames->contains($locationField)) {
                // Also clear an old value when an existing user is moved to an
                // event where this location field is disabled.
                $data[$locationField] = null;
            }
        }

        if (isset($data['mobile_number'])) {
            $data['mobile'] = $data['mobile_number'];
            unset($data['mobile_number']);
        }

        if (!empty($data['first_name']) || !empty($data['last_name'])) {
            $data['name'] = trim(($data['first_name'] ?? '') . ' ' . ($data['last_name'] ?? ''));
        }

        if (!empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        unset($data['avatar']);

        if ($id && $request->avatar_removed == '1') {
            if ($user->avatar) {
                Storage::disk('public')->delete($user->avatar);
            }
            $data['avatar'] = null;
        }

        if ($request->hasFile('avatar')) {
            if ($user?->avatar) {
                Storage::disk('public')->delete($user->avatar);
            }
            $data['avatar'] = $request->file('avatar')->store('avatars', 'public');
        }

        $data['type'] = 'doctor';
        $data['event_id'] = $eventId;

        if ($user) {
            $user->update(collect($data)->only($userColumns)->all());
        } else {
            $user = User::create(collect($data)->only($userColumns)->all());
        }

        foreach ($customValues as $fieldId => $value) {
            $user->dynamicFieldValues()->updateOrCreate(['dynamic_field_id' => $fieldId], ['value' => $value]);
        }

        return redirect()->route('admin.user.index')->with('success', 'User Saved Successfully');
    }

    public function show($id)
    {
        $user = User::with('dynamicFieldValues')->findOrFail($id);

        $activeFields = DynamicFields::where('status', 'active')
            ->orderBy('index_no')
            ->where('event_id', $user->event_id)->get();

        return view('admin.users.show', ['user' => $user, 'active_fields' => $activeFields, 'title' => __('Users'), 'breadcrumb' => breadcrumb([__('Users') => route('admin.user.index'), 'User Details' => ''])]);
    }


    public function destroy($id)
    {
        $user = User::findOrFail($id);

        if ($user->avatar) {
            Storage::disk('public')->delete($user->avatar);
        }

        $user->delete();

        return response()->json(['success' => true]);
    }

    public function deleteMultiple(Request $request)
    {
        $users = User::whereIn('id', $request->ids)->get();

        foreach ($users as $user) {
            if ($user->avatar) {
                Storage::disk('public')->delete($user->avatar);
            }
        }

        User::whereIn('id', $request->ids)->delete();

        return response()->json(['success' => true]);
    }

    public function datatable(Request $request)
    {
        $authUser = auth()->user();

        if ($authUser->type === 'sub_admin') {
            $activeFields = DynamicFields::where('status', 'active')
                ->where('event_id', $authUser->event_id)
                ->orderBy('index_no')
                ->get();
        } else {
            $activeFields = DynamicFields::where('status', 'active')
                ->orderBy('index_no')
                ->get()
                ->unique('field_name');
        }

        $validDynamicFields = $activeFields->reject(fn ($field) => $field->field_name === 'password')->values();
        $userTableColumns = Schema::getColumnListing('users');
        $query = User::with(['event', 'dynamicFieldValues.field'])->where('type', 'doctor');

        if ($authUser->type === 'sub_admin') {
            $query->where('event_id', $authUser->event_id);
        }

        if ($request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($validDynamicFields, $search, $userTableColumns) {
                foreach ($validDynamicFields as $field) {
                    $column = $field->field_name === 'mobile_number' ? 'mobile' : $field->field_name;
                    if (in_array($column, $userTableColumns, true)) $q->orWhere($column, 'LIKE', "%$search%");
                }
                $q->orWhereHas('dynamicFieldValues', fn ($values) => $values->where('value', 'LIKE', "%$search%"));
            });
        }
        if ($request->filled('event')) {
            $query->where('event_id', $request->event);
        }
        $recordsTotal = $query->count();

        $data = $query
            ->skip($request->start)
            ->take($request->length)
            ->get()
            ->map(function ($user) use ($validDynamicFields, $userTableColumns) {
                $row = $user->toArray();
                $row['event_name'] = $user->event->name ?? '-';
                foreach ($validDynamicFields as $field) {
                    $column = $field->field_name === 'mobile_number' ? 'mobile' : $field->field_name;
                    $eventField = in_array($column, $userTableColumns, true) || $field->event_id == $user->event_id
                        ? $field
                        : $user->dynamicFieldValues->first(fn ($value) => $value->field?->field_name === $field->field_name)?->field;
                    $row[$field->field_name] = $eventField ? $user->dynamicValue($eventField) : null;
                }
                return $row;
            });

        return response()->json([
            "draw" => intval($request->draw),
            "recordsTotal" => $recordsTotal,
            "recordsFiltered" => $recordsTotal,
            "data" => $data
        ]);
    }

    public function countries()
    {
        return Country::select('id', 'name')
            ->orderBy('name')
            ->get();

    }

    public function states($countryId)
    {
        return State::select('id', 'name')
            ->where('country_id', $countryId)
            ->orderBy('name')
            ->get();
    }

    public function cities($stateId)
    {
        return City::select('id', 'name')
            ->where('state_id', $stateId)
            ->orderBy('name')
            ->get();

    }

    public function getEventFields(Request $request, $eventId)
    {
        $activeFields = DynamicFields::with('attribute_data')
            ->active()
            ->where('event_id', $eventId)
            ->orderBy('index_no')
            ->get();

        return view('admin.users._dynamic_fields', [
            'active_fields' => $activeFields,
            'user' => null,
            'parent_field' => $request->input('parent_field'),
            'parent_value' => $request->input('parent_value'),
        ]);
    }

    public function export(Request $request)
    {
        $authUser = auth()->user();
        $isAdmin = $authUser->type === 'admin';
        $search = $request->get('search');

        if ($isAdmin) {
            $activeFields = DynamicFields::where('status', 'active')
                ->where('field_name', '!=', 'password')
                ->orderBy('index_no')
                ->get()
                ->unique('field_name');
        } else {
            $activeFields = DynamicFields::where('status', 'active')
                ->where('field_name', '!=', 'password')
                ->where('event_id', $authUser->event_id)
                ->orderBy('index_no')
                ->get();
        }

        $userTableColumns = Schema::getColumnListing('users');
        $validDynamicFields = $activeFields->values();

        $query = User::with(['event', 'dynamicFieldValues.field'])
            ->where('type', 'doctor')
            ->when(!$isAdmin, fn($q) => $q->where('event_id', $authUser->event_id))
            ->when($request->filled('event'), fn ($q) => $q->where('event_id', $request->event))
            ->when($search, function ($q) use ($validDynamicFields, $search, $userTableColumns) {
                $q->where(function ($inner) use ($validDynamicFields, $search, $userTableColumns) {
                    foreach ($validDynamicFields as $field) {
                        $column = $field->field_name === 'mobile_number' ? 'mobile' : $field->field_name;
                        if (in_array($column, $userTableColumns, true)) $inner->orWhere($column, 'LIKE', "%{$search}%");
                    }
                    $inner->orWhereHas('dynamicFieldValues', fn ($values) => $values->where('value', 'LIKE', "%{$search}%"));
                });
            })
            ->get();

        $dynamicHeaders = $validDynamicFields->pluck('label')->toArray();

        $headers = $isAdmin
            ? array_merge(['Event'], $dynamicHeaders)
            : $dynamicHeaders;

        $rows = [];
        $rows[] = implode(',', $headers);

        $esc = fn($val) => '"' . str_replace('"', '""', (string)($val ?? '')) . '"';

        foreach ($query as $user) {
            $row = [];

            if ($isAdmin) {
                $row[] = $esc($user->event?->name ?? 'N/A');
            }

            foreach ($validDynamicFields as $field) {
                $column = $field->field_name === 'mobile_number' ? 'mobile' : $field->field_name;
                $eventField = in_array($column, $userTableColumns, true) || $field->event_id == $user->event_id
                    ? $field
                    : $user->dynamicFieldValues->first(fn ($value) => $value->field?->field_name === $field->field_name)?->field;
                $row[] = $esc($eventField ? ($user->dynamicValue($eventField) ?? 'N/A') : 'N/A');
            }

            $rows[] = implode(',', $row);
        }

        $csv = implode("\n", $rows);
        $filename = 'users_export_' . now()->format('Y-m-d') . '.csv';

        return response($csv, 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }
}
