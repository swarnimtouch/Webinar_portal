<?php

namespace App\Http\Controllers\Admin;

use App\Models\DynamicFields;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    public function index()
    {

        $dynamicFields = DynamicFields::where('status', 'active')
            ->orderBy('index_no')
            ->get();
        $usersColumns = Schema::getColumnListing('users');
        $excludeColumns = ['password'];
        $validDynamicFields = $dynamicFields->filter(function ($field) use ($usersColumns, $excludeColumns) {
            if (in_array($field->field_name, $excludeColumns)) {
                return false;
            }
            return in_array($field->field_name, $usersColumns);
        });
        $users = User::where('type', 'doctor')->get();

        return view('admin.users.index', ['users' => $users, 'valid_dynamic_fields' => $validDynamicFields, 'title' => __('Users'), 'breadcrumb' => breadcrumb([__('Users') => route('admin.user.index')])]);
    }

    public function addEditForm($id = null)
    {
        $user = $id ? User::findOrFail($id) : null;

        $activeFields = DynamicFields::where('status', 'active')
            ->orderBy('index_no')
            ->get();

        $breadcrumb = $id
            ? breadcrumb([__('Users') => route('admin.user.index'), 'Edit User' => ''])
            : breadcrumb([__('Users') => route('admin.user.index'), 'Add User' => '']);

        return view('admin.users.add_edit', [
            'user' => $user,
            'activeFields' => $activeFields,
            'title' => __('Users'),
            'breadcrumb' => $breadcrumb,
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
                'alternative_mobile_number' => 'alternative_mobile',
            ];

            $dbFieldName = $fieldMapping[$fieldName] ?? $fieldName;

            if ($field->is_required) {
                if ($fieldName === 'email') {
                    $rules[$dbFieldName] = 'required|email|unique:users,email' . ($id ? ",$id" : '');
                } elseif ($fieldName === 'password') {
                    // Required only on create; optional on update
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

            $messages[$dbFieldName . '.required'] = $field->label . ' is required';
        }

        $request->validate($rules, $messages);

        $userData = [];

        foreach ($activeFields as $field) {
            $fieldName = $field->field_name;

            $fieldMapping = [
                'mobile_number' => 'mobile',
                'alternative_mobile_number' => 'alternative_mobile',
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

        $userData['type'] = 'doctor';

        if ($user) {
            $user->update($userData);
            $message = 'User updated successfully';
        } else {
            User::create($userData);
            $message = 'User created successfully';
        }

        return redirect()->route('admin.user.index')->with('success', $message);
    }

    public function show($id)
    {
        $user = User::findOrFail($id);

        $activeFields = DynamicFields::where('status', 'active')
            ->orderBy('index_no')
            ->get();

        return view('admin.users.show', ['user' => $user, 'activeFields' => $activeFields, 'title' => __('Users'), 'breadcrumb' => breadcrumb([__('Users') => route('admin.user.index'), 'User Details' => ''])]);
    }


    public function destroy($id)
    {
        $user = User::findOrFail($id);

        // Delete avatar if exists
        if ($user->avatar) {
            Storage::disk('public')->delete($user->avatar);
        }

        $user->delete();

        return response()->json(['success' => true]);
    }

    public function deleteMultiple(Request $request)
    {
        $users = User::whereIn('id', $request->ids)->get();

        // Delete avatars
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
        $activeFields = DynamicFields::where('status', 'active')->orderBy('index_no')->get();

        $userTableColumns = \Schema::getColumnListing('users');

        $validDynamicFields = $activeFields->filter(function ($field) use ($userTableColumns) {
            return in_array($field->field_name, $userTableColumns);
        });

        $select = ['id'];
        foreach ($validDynamicFields as $field) {
            $select[] = $field->field_name;
        }

        $query = User::select($select)->where('type', 'doctor');
        /*** GLOBAL SEARCH ***/
        if ($request->search) {
            $search = $request->search;

            $query->where(function ($q) use ($validDynamicFields, $search) {
                foreach ($validDynamicFields as $field) {
                    $q->orWhere($field->field_name, 'LIKE', "%$search%");
                }
            });
        }

        /*** PAGINATION ***/
        $recordsTotal = $query->count();

        $data = $query
            ->skip($request->start)
            ->take($request->length)
            ->get();

        return response()->json([
            "draw" => intval($request->draw),
            "recordsTotal" => $recordsTotal,
            "recordsFiltered" => $recordsTotal,
            "data" => $data
        ]);
    }
}
