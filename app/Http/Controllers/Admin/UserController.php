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

class UserController extends Controller
{
    public function index()
    {
        $authUser = auth()->user();

        if ($authUser->type === 'sub_admin') {
            $dynamicFields = DynamicFields::where('status', 'active')
                ->where('event_id', $authUser->event_id)
                ->orderBy('index_no')
                ->get();
        } else {
            $dynamicFields = DynamicFields::where('status', 'active')
                ->orderBy('index_no')
                ->get()
                ->unique('field_name');
        }

        $usersColumns = Schema::getColumnListing('users');
        $excludeColumns = ['password'];

        $validDynamicFields = $dynamicFields->filter(function ($field) use ($usersColumns, $excludeColumns) {
            if (in_array($field->field_name, $excludeColumns)) {
                return false;
            }
            return in_array($field->field_name, $usersColumns);
        })->values();

        $users = User::where('type', 'doctor')->get();

        return view('admin.users.index', [
            'users' => $users,
            'valid_dynamic_fields' => $validDynamicFields,
            'title' => __('Users'),
            'breadcrumb' => breadcrumb([__('Users') => route('admin.user.index')])
        ]);
    }

    public function addEditForm($id = null)
    {
        $user = $id ? User::findOrFail($id) : null;
        $authUser = auth()->user();
        $events = Events::get();

        $fieldsQuery = DynamicFields::join('attributes', 'dynamic_fields.attribute_id', '=', 'attributes.id')
            ->select('dynamic_fields.*', 'attributes.type as attr_type')
            ->where('dynamic_fields.status', 'active')
            ->orderBy('dynamic_fields.index_no');

        if ($authUser->type === 'sub_admin') {
            $activeFields = $fieldsQuery
                ->where('dynamic_fields.event_id', $authUser->event_id)
                ->get();
        } else {
            $activeFields = ($id && $user->event_id)
                ? $fieldsQuery
                    ->where('dynamic_fields.event_id', $user->event_id)
                    ->get()
                : collect();
        }

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
        $activeFields = DynamicFields::where('status', 'active')->get();

        $rules = [];
        $messages = [];

        foreach ($activeFields as $field) {
            $fieldName = $field->field_name;

            $fieldMapping = [
                'mobile_number' => 'mobile',
            ];

            $dbFieldName = $fieldMapping[$fieldName] ?? $fieldName;

            if ($field->is_required) {
                if ($fieldName === 'email') {
                    $rules[$dbFieldName] = 'required|email|unique:users,email' . ($id ? ",$id" : '');
                } elseif ($fieldName === 'password') {
                    $rules['password'] = $id ? 'nullable|min:6' : 'required|min:6';
                } elseif ($fieldName === 'avatar') {
                    if ($id && $request->avatar_removed == '1' && !$request->hasFile('avatar')) {
                        $rules['avatar'] = 'required|image|mimes:jpg,jpeg,png,gif|max:5120';
                    } else {
                        $rules['avatar'] = $id ? 'nullable|image|mimes:jpg,jpeg,png,gif|max:5120'
                            : 'required|image|mimes:jpg,jpeg,png,gif|max:5120';
                    }
                } elseif (in_array($fieldName, ['mobile_number', 'alternative_mobile_number'])) {
                    $rules[$dbFieldName] = 'required|digits:10';
                } else {
                    $rules[$dbFieldName] = 'required';
                }
            } else {
                if ($fieldName === 'email' && $request->has($dbFieldName)) {
                    $rules[$dbFieldName] = 'nullable|email|unique:users,email' . ($id ? ",$id" : '');
                } elseif ($fieldName === 'avatar' && $request->hasFile('avatar')) {
                    $rules['avatar'] = 'nullable|image|mimes:jpg,jpeg,png,gif|max:5120';
                } elseif (in_array($fieldName, ['mobile_number', 'alternative_mobile_number']) && $request->has($dbFieldName)) {
                    $rules[$dbFieldName] = 'nullable|digits:10';
                }
            }
            $rules['event_id'] = 'nullable';
            $messages[$dbFieldName . '.required'] = $field->label . ' is required';
        }

        $request->validate($rules, $messages);

        $userData = [];

        foreach ($activeFields as $field) {
            $fieldName = $field->field_name;

            $fieldMapping = [
                'mobile_number' => 'mobile',
            ];

            $dbFieldName = $fieldMapping[$fieldName] ?? $fieldName;

            if ($fieldName === 'avatar') {
                if ($id && $request->avatar_removed == '1') {
                    if ($user->avatar) {
                        Storage::disk('public')->delete($user->avatar);
                    }
                    $userData['avatar'] = null;
                }

                if ($request->hasFile('avatar')) {
                    if ($user?->avatar) {
                        Storage::disk('public')->delete($user->avatar);
                    }
                    $userData['avatar'] = $request->file('avatar')->store('avatars', 'public');
                }
            } elseif ($fieldName === 'password') {
                if ($request->filled('password')) {
                    $userData['password'] = Hash::make($request->password);
                }
            } else {
                if ($request->has($dbFieldName)) {
                    $userData[$dbFieldName] = $request->$dbFieldName;
                }
            }
        }
        $userData['name'] = $request->first_name . $request->last_name;
        $userData['event_id'] = $request->event_id;
        $userData['type'] = 'doctor';

        if ($user) {
            $user->update($userData);
        } else {
            User::create($userData);
        }

        return redirect()->route('admin.user.index')->with('success', 'User Saved Successfully.');
    }

    public function show($id)
    {
        $user = User::findOrFail($id);

        $activeFields = DynamicFields::where('status', 'active')
            ->orderBy('index_no')
            ->get();

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
                ->unique('field_name'); // <-- Fix duplicate columns
        }

        $userTableColumns = \Schema::getColumnListing('users');

        $validDynamicFields = $activeFields->filter(function ($field) use ($userTableColumns) {
            return in_array($field->field_name, $userTableColumns);
        })->values(); // <-- re-index

        $select = ['id', 'event_id'];
        foreach ($validDynamicFields as $field) {
            if (!in_array($field->field_name, $select)) { // prevent duplicate select columns
                $select[] = $field->field_name;
            }
        }

        $query = User::with('event')->select($select)->where('type', 'doctor');

        if ($authUser->type === 'sub_admin') {
            $query->where('event_id', $authUser->event_id);
        }

        if ($request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($validDynamicFields, $search) {
                foreach ($validDynamicFields as $field) {
                    $q->orWhere($field->field_name, 'LIKE', "%$search%");
                }
            });
        }

        $recordsTotal = $query->count();

        $data = $query
            ->skip($request->start)
            ->take($request->length)
            ->get()
            ->map(function ($user) {
                $row = $user->toArray();
                $row['event_name'] = $user->event->name ?? '-';
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

    public function getEventFields($eventId)
    {
        $fields = DynamicFields::with('attributeInput')
            ->where('event_id', $eventId)
            ->where('status', 'active')
            ->orderBy('index_no')
            ->get()
            ->map(function ($field) {
                $field->attr_type = $field->attributeInput->type ?? 'text';
                return $field;
            });

        // ✅ Sirf yeh line badlo
        return view('admin.users._dynamic_fields', [
            'active_fields' => $fields,
            'user' => null
        ])->render();
    }
}
