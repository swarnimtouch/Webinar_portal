<?php

namespace App\Http\Controllers\Admin;

use App\Models\UserAttendance;
use App\Models\DynamicFields;
use App\Models\User;
use Illuminate\Http\Request;
use Carbon\Carbon;

class UserAttendanceController extends Controller
{
    public function index()
    {
        $activeFields = DynamicFields::where('status', 'active')
            ->orderBy('index_no')
            ->get();

        return view('admin.user_attendance.index', [
            'title' => __('User Attendance'),
            'breadcrumb' => breadcrumb([
                __('User Attendance') => route('admin.user_attendance')
            ]),
            'active_fields' => $activeFields
        ]);
    }

    public function datatable(Request $request)
    {
        $activeFields = DynamicFields::where('status', 'active')
            ->orderBy('index_no')
            ->get();

        $query = UserAttendance::query()
            ->join('users', 'user_attendances.user_id', '=', 'users.id')
            ->select([
                'user_attendances.id as attendance_id',
                'user_attendances.session_time',
                'users.created_at as registration_date',
                'users.*'
            ]);

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search, $activeFields) {
                foreach ($activeFields as $field) {
                    $dbColumn = $this->mapFieldNameToColumn($field->field_name);
                    if ($dbColumn && \Schema::hasColumn('users', $dbColumn)) {
                        $q->orWhere('users.' . $dbColumn, 'like', "%{$search}%");
                    }
                }
                $q->orWhere('user_attendances.session_time', 'like', "%{$search}%");
            });
        }

        $total = $query->count();
        if ($request->has('order')) {
            $columns = $request->columns;
            foreach ($request->order as $order) {
                $columnIndex = $order['column'];
                $columnName = $columns[$columnIndex]['data'] ?? null;
                $direction = $order['dir'];

                if ($columnName === 'session_time') {
                    $query->orderBy('user_attendances.session_time', $direction);
                } elseif ($columnName === 'registration_date') {
                    $query->orderBy('users.created_at', $direction);
                } elseif ($columnName) {
                    $dbColumn = $this->mapFieldNameToColumn($columnName);

                    if ($dbColumn && \Schema::hasColumn('users', $dbColumn)) {
                        $query->orderBy('users.' . $dbColumn, $direction);
                    }
                }
            }
        } else {
            $query->orderBy('users.created_at', 'desc');
        }

        $length = $request->input('length', 10);
        $start = $request->input('start', 0);
        $records = $query->skip($start)->take($length)->get();

        $data = $records->map(function ($record) use ($activeFields) {
            $row = [
                'attendance_id' => $record->attendance_id,
            ];

            foreach ($activeFields as $field) {
                $dbColumn = $this->mapFieldNameToColumn($field->field_name);
                $row[$field->field_name] = $record->{$dbColumn} ?? '-';
            }

            $row['session_time'] = $record->session_time ?? 0;
            $row['registration_date'] = $record->registration_date
                ? Carbon::parse($record->registration_date)->format('d M Y h:i A')
                : '-';


            return $row;
        });

        return response()->json([
            'draw' => $request->input('draw', 1),
            'recordsTotal' => $total,
            'recordsFiltered' => $total,
            'data' => $data
        ]);
    }

    /**
     * Map dynamic field names to actual database column names
     */
    private function mapFieldNameToColumn($fieldName)
    {
        $fieldMap = [
            'mobile_number' => 'mobile',
            'first_name' => 'first_name',
            'last_name' => 'last_name',
            'email' => 'email',
            'gender' => 'gender',
            'country' => 'country',
            'state' => 'state',
            'city' => 'city',
        ];

        return $fieldMap[$fieldName] ?? $fieldName;
    }

    public function delete($id)
    {
        try {
            $attendance = UserAttendance::findOrFail($id);
            $attendance->delete();

            return response()->json([
                'success' => true,
                'message' => 'Attendance record deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting attendance record'
            ], 500);
        }
    }

    public function deleteMultiple(Request $request)
    {
        try {
            $ids = $request->input('ids', []);

            if (empty($ids)) {
                return response()->json([
                    'success' => false,
                    'message' => 'No records selected'
                ], 400);
            }

            UserAttendance::whereIn('id', $ids)->delete();

            return response()->json([
                'success' => true,
                'message' => 'attendance records deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting selected records'
            ], 500);
        }
    }
}
