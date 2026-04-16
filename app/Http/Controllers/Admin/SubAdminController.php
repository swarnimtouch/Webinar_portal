<?php

namespace App\Http\Controllers\Admin;

use App\Models\Events;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class SubAdminController
{
    public function index()
    {
        return view('admin.sub_admin.index', ['title' => __('Sub Admin'), 'breadcrumb' => breadcrumb([__('Sub Admin') => route('admin.sub_admin')])]);
    }

    public function addEditForm($id = null)
    {
        $subAdmin = $id ? User::findOrFail($id) : new User();

        $response = [
            'sub_admin' => $subAdmin,
            'events' => Events::Active()->get(),
            'title' => __('Sub Admin'),
            'breadcrumb' => breadcrumb([__('Sub Admin') => route('admin.sub_admin'), ($id ? 'Edit' : 'Add' . ' Sub Admin') => '']),
        ];
        return view('admin.sub_admin.add_edit', $response);
    }


    public function save(Request $request, $id = null)
    {
        $subAdmin = $id ? User::findOrFail($id) : new User();
        $request->validate([
            'event_id' => ['required', 'exists:events,id'],
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email'],
            'mobile' => ['required', 'string', 'max:255'],
            'avatar' => [
                Rule::requiredIf(fn() => !$id),
                'file',
                'mimes:jpg,jpeg,png,webp',
                'max:5120',
            ],
            'password' => ['nullable', 'string']
        ]);

        $subAdmin->type = 'sub_admin';
        $subAdmin->event_id = $request->event_id ?? null;
        $subAdmin->name = $request->name ?? null;
        $subAdmin->email = $request->email ?? null;
        $subAdmin->mobile = $request->mobile ?? null;
        if ($request->hasFile('avatar')) {
            if ($subAdmin?->avatar) {
                Storage::disk('public')->delete($subAdmin->avatar);
            }
            $subAdmin->avatar = $request->file('avatar')->store('avatars', 'public');
        }

        if ($request->has('password')) {
            $subAdmin->password = $request->password;
        }

        $subAdmin->save();

        return redirect()->route('admin.sub_admin')
            ->with('success', 'Sub Admin Saved Successfully');
    }


    public function delete($id)
    {
        try {
            $subAdmin = User::findOrFail($id);
            $subAdmin->delete();

            return response()->json(['success' => true, 'message' => 'Sub Admin deleted successfully']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error deleting Sub Admin'], 500);
        }
    }


    public function deleteMultiple(Request $request)
    {
        try {
            $ids = $request->input('ids', []);
            if (empty($ids)) {
                return response()->json(['success' => false, 'message' => 'No sub admin selected'], 400);
            }
            $subAdmin = User::whereIn('id', $ids)->get();
            foreach ($subAdmin as $sa) {
                if (Storage::exists('public/avatars/' . $sa->avatar)) {
                    Storage::delete('public/avatars/' . $sa->avatar);
                }
                $sa->delete();
            }
            return response()->json(['success' => true, 'message' => 'Events deleted successfully']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error deleting events'], 500);
        }
    }

    public function toggleStatus(Request $request, $id)
    {
        try {
            $subAdmin = User::findOrFail($id);
            $subAdmin->status = $request->input('status');
            $subAdmin->save();
            return response()->json([
                'success' => true,
                'message' => 'Status updated successfully',
                'status' => $subAdmin->status
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
        $query = User::whereType('sub_admin');
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('mobile', 'like', "%{$search}%");
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
                    'email' => 'email',
                    'mobile' => 'mobile',
                    default => 'id'
                };
                $query->orderBy($dbColumn, $direction);
            }
        } else {
            $query->orderBy('id', 'desc');
        }
        $length = $request->input('length', 10);
        $start = $request->input('start', 0);
        $subAdmin = $query->skip($start)->take($length)->get();
        $data = $subAdmin->map(function ($sa) {
            return [
                'id' => $sa->id,
                'avatar' => $sa->avatar,
                'name' => $sa->name,
                'email' => $sa->email,
                'mobile' => $sa->mobile,
                'status' => $sa->status,
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
