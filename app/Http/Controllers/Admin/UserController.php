<?php

namespace App\Http\Controllers\Admin;

use App\Models\DaynamicFields;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    public function index()
    {
        // Get all active dynamic fields
        $activeFields = DaynamicFields::where('status', 'active')
            ->orderBy('index_no')
            ->get();

        // Get all users
        $users = User::where('type', 'doctor')->get();

        $breadcrumbs = [
            'User' => ''
        ];

        $title = 'Users';

        return view('admin.users.index', compact('users', 'activeFields', 'breadcrumbs', 'title'));
    }

    public function create()
    {
        // Get all active dynamic fields for the form
        $activeFields = DaynamicFields::where('status', 'active')
            ->orderBy('index_no')
            ->get();

        $breadcrumbs = [
            'Users' => route('user.index'),
            'Add User' => ''
        ];

        $title = ' User';

        return view('admin.users.add_edit', compact('activeFields', 'breadcrumbs', 'title'));
    }

    public function store(Request $request)
    {
        // Get active fields to build validation rules dynamically
        $activeFields = DaynamicFields::where('status', 'active')->get();

        $rules = [];
        $messages = [];

        foreach ($activeFields as $field) {
            $fieldName = $field->field_name;

            // Map field names to database columns
            $fieldMapping = [
                'mobile_number' => 'mobile',
                'alternative_mobile_number' => 'alternative_mobile',
            ];

            $dbFieldName = $fieldMapping[$fieldName] ?? $fieldName;

            if ($field->is_required) {
                if ($fieldName == 'email') {
                    $rules[$dbFieldName] = 'required|email|unique:users,email';
                } elseif ($fieldName == 'password') {
                    $rules['password'] = 'required|min:6';
                } elseif ($fieldName == 'avatar') {
                    $rules['avatar'] = 'required|image|mimes:jpg,jpeg,png,gif|max:5120';
                } elseif (in_array($fieldName, ['mobile_number', 'alternative_mobile_number'])) {
                    $rules[$dbFieldName] = 'required|digits:10';
                } else {
                    $rules[$dbFieldName] = 'required';
                }
            } else {
                if ($fieldName == 'email' && $request->has($dbFieldName)) {
                    $rules[$dbFieldName] = 'nullable|email|unique:users,email';
                } elseif ($fieldName == 'avatar' && $request->hasFile('avatar')) {
                    $rules['avatar'] = 'nullable|image|mimes:jpg,jpeg,png,gif|max:5120';
                } elseif (in_array($fieldName, ['mobile_number', 'alternative_mobile_number']) && $request->has($dbFieldName)) {
                    $rules[$dbFieldName] = 'nullable|digits:10';
                }
            }

            $messages[$dbFieldName . '.required'] = $field->label . ' is required';
        }

        $validated = $request->validate($rules, $messages);

        // Prepare data for insertion
        $userData = [];

        foreach ($activeFields as $field) {
            $fieldName = $field->field_name;

            $fieldMapping = [
                'mobile_number' => 'mobile',
                'alternative_mobile_number' => 'alternative_mobile',
            ];

            $dbFieldName = $fieldMapping[$fieldName] ?? $fieldName;

            if ($fieldName == 'avatar') {
                if ($request->hasFile('avatar')) {
                    $userData['avatar'] = $request->file('avatar')->store('avatars', 'public');
                }
            } elseif ($fieldName == 'password') {
                if ($request->filled('password')) {
                    $userData['password'] = Hash::make($request->password);
                }
            } else {
                if ($request->has($dbFieldName)) {
                    $userData[$dbFieldName] = $request->$dbFieldName;
                }
            }
        }

        // Set default type
        $userData['type'] = 'doctor';

        User::create($userData);

        return redirect()->route('user.index')->with('success', 'User created successfully');
    }
    public function show($id)
    {
        $user = User::findOrFail($id);

        // Get all active dynamic fields
        $activeFields = DaynamicFields::where('status', 'active')
            ->orderBy('index_no')
            ->get();

        $breadcrumbs = [
            'Users' => route('user.index'),
            'User Details' => ''
        ];

        $title = 'User Details';

        return view('admin.users.show', compact('user', 'activeFields', 'breadcrumbs', 'title'));
    }
    public function edit($id)
    {
        $user = User::findOrFail($id);

        // Get all active dynamic fields for the form
        $activeFields = DaynamicFields::where('status', 'active')
            ->orderBy('index_no')
            ->get();

        $breadcrumbs = [
            'Users' => route('user.index'),
            'Edit User' => ''
        ];

        $title = ' User';

        return view('admin.users.add_edit', compact('user', 'activeFields', 'breadcrumbs', 'title'));
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        // Get active fields to build validation rules dynamically
        $activeFields = DaynamicFields::where('status', 'active')->get();

        $rules = [];
        $messages = [];

        foreach ($activeFields as $field) {
            $fieldName = $field->field_name;

            // Map field names to database columns
            $fieldMapping = [
                'mobile_number' => 'mobile',
                'alternative_mobile_number' => 'alternative_mobile',
            ];

            $dbFieldName = $fieldMapping[$fieldName] ?? $fieldName;

            if ($field->is_required) {
                if ($fieldName == 'email') {
                    $rules[$dbFieldName] = 'required|email|unique:users,email,' . $id;
                } elseif ($fieldName == 'password') {
                    $rules['password'] = 'nullable|min:6'; // Password optional on update
                } elseif ($fieldName == 'avatar') {
                    // Avatar optional on update unless being removed
                    if ($request->avatar_removed == '1' && !$request->hasFile('avatar')) {
                        $rules['avatar'] = 'required|image|mimes:jpg,jpeg,png,gif|max:5120';
                    } else {
                        $rules['avatar'] = 'nullable|image|mimes:jpg,jpeg,png,gif|max:5120';
                    }
                } elseif (in_array($fieldName, ['mobile_number', 'alternative_mobile_number'])) {
                    $rules[$dbFieldName] = 'required|digits:10';
                } else {
                    $rules[$dbFieldName] = 'required';
                }
            } else {
                if ($fieldName == 'email' && $request->has($dbFieldName)) {
                    $rules[$dbFieldName] = 'nullable|email|unique:users,email,' . $id;
                } elseif ($fieldName == 'avatar' && $request->hasFile('avatar')) {
                    $rules['avatar'] = 'nullable|image|mimes:jpg,jpeg,png,gif|max:5120';
                } elseif (in_array($fieldName, ['mobile_number', 'alternative_mobile_number']) && $request->has($dbFieldName)) {
                    $rules[$dbFieldName] = 'nullable|digits:10';
                }
            }

            $messages[$dbFieldName . '.required'] = $field->label . ' is required';
        }

        $validated = $request->validate($rules, $messages);

        // Prepare data for update
        $userData = [];

        foreach ($activeFields as $field) {
            $fieldName = $field->field_name;

            $fieldMapping = [
                'mobile_number' => 'mobile',
                'alternative_mobile_number' => 'alternative_mobile',
            ];

            $dbFieldName = $fieldMapping[$fieldName] ?? $fieldName;

            if ($fieldName == 'avatar') {
                // Handle avatar removal
                if ($request->avatar_removed == '1') {
                    if ($user->avatar) {
                        Storage::disk('public')->delete($user->avatar);
                    }
                    $userData['avatar'] = null;
                }

                // Handle new avatar upload
                if ($request->hasFile('avatar')) {
                    // Delete old avatar
                    if ($user->avatar) {
                        Storage::disk('public')->delete($user->avatar);
                    }
                    $userData['avatar'] = $request->file('avatar')->store('avatars', 'public');
                }
            } elseif ($fieldName == 'password') {
                // Only update password if provided
                if ($request->filled('password')) {
                    $userData['password'] = Hash::make($request->password);
                }
            } else {
                if ($request->has($dbFieldName)) {
                    $userData[$dbFieldName] = $request->$dbFieldName;
                }
            }
        }

        $user->update($userData);

        return redirect()->route('user.index')->with('success', 'User updated successfully');
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
}
