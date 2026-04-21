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
        $user = auth()->user();

        $activeFields = DynamicFields::where('status', 'active')
            ->when($user->type === 'sub_admin', function ($q) use ($user) {
                $q->where('event_id', $user->event_id);
            })
            ->where('field_name', '!=', 'password')
            ->orderBy('index_no')
            ->get()
            ->when($user->type !== 'sub_admin', function ($collection) {
                return $collection->unique('field_name');
            });

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
        $user = auth()->user();

        $activeFields = DynamicFields::where('status', 'active')
            ->where('field_name', '!=', 'password')
            ->when($user->type === 'sub_admin', fn($q) => $q->where('event_id', $user->event_id))
            ->orderBy('index_no')
            ->get();

        $query = UserAttendance::with(['user.event'])
            ->when($user->type === 'sub_admin', function ($q) use ($user) {
                $q->whereHas('user', fn($uq) => $uq->where('event_id', $user->event_id));
            });

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search, $activeFields) {
                $q->whereHas('user', function ($uq) use ($search, $activeFields) {
                    $uq->where(function ($inner) use ($search, $activeFields) {
                        foreach ($activeFields as $field) {
                            $dbColumn = $this->mapFieldNameToColumn($field->field_name);
                            if ($dbColumn && \Schema::hasColumn('users', $dbColumn)) {
                                $inner->orWhere($dbColumn, 'like', "%{$search}%");
                            }
                        }
                    });
                });
                $q->orWhere('session_time', 'like', "%{$search}%");
            });
        }

        $total = $query->count();

        // Sorting
        if ($request->filled('order')) {
            $columns = $request->columns;
            foreach ($request->order as $order) {
                $columnIndex = $order['column'];
                $columnName = $columns[$columnIndex]['data'] ?? null;
                $direction = $order['dir'];

                if ($columnName === 'session_time') {
                    $query->orderBy('session_time', $direction);

                } elseif ($columnName === 'registration_date') {
                    $query->join('users as u_sort', 'u_sort.id', '=', 'user_attendances.user_id')
                        ->orderBy('u_sort.created_at', $direction);

                } elseif ($columnName) {
                    $dbColumn = $this->mapFieldNameToColumn($columnName);
                    if ($dbColumn && \Schema::hasColumn('users', $dbColumn)) {
                        $query->join('users as u_sort', 'u_sort.id', '=', 'user_attendances.user_id')
                            ->orderBy('u_sort.' . $dbColumn, $direction);
                    }
                }
            }
        } else {
            $query->join('users as u_sort', 'u_sort.id', '=', 'user_attendances.user_id')
                ->orderBy('u_sort.created_at', 'desc');
        }

        $length = $request->input('length', 10);
        $start = $request->input('start', 0);
        $records = $query->skip($start)->take($length)->get();

        $data = $records->map(function ($attendance) use ($activeFields) {
            $user = $attendance->user;

            $row = [
                'attendance_id' => $attendance->id,
            ];

            foreach ($activeFields as $field) {
                $dbColumn = $this->mapFieldNameToColumn($field->field_name);
                $row[$field->field_name] = $user?->{$dbColumn} ?? 'N/A';
            }

            $row['event'] = $user?->event?->name ?? 'N/A';
            $row['session_time'] = $attendance->session_time ?? 0;
            $row['registration_date'] = $user?->created_at
                ? Carbon::parse($user->created_at)->format('d M Y h:i A')
                : '-';

            return $row;
        });

        return response()->json([
            'draw' => $request->input('draw', 1),
            'recordsTotal' => $total,
            'recordsFiltered' => $total,
            'data' => $data,
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

    public function export(Request $request)
    {
        $user = auth()->user();
        $isAdmin = $user->type === 'admin';
        $search = $request->get('search');

        $activeFields = DynamicFields::where('status', 'active')
            ->when(!$isAdmin, fn($q) => $q->where('event_id', $user->event_id))
            ->where('field_name', '!=', 'password')
            ->orderBy('index_no')
            ->get()
            ->when($isAdmin, fn($collection) => $collection->unique('field_name'));

        $query = UserAttendance::with(['user.event'])
            ->when(!$isAdmin, function ($q) use ($user) {
                $q->whereHas('user', fn($uq) => $uq->where('event_id', $user->event_id));
            })
            ->when($search, function ($q) use ($search, $activeFields) {
                $q->where(function ($q) use ($search, $activeFields) {
                    $q->whereHas('user', function ($uq) use ($search, $activeFields) {
                        $uq->where(function ($inner) use ($search, $activeFields) {
                            foreach ($activeFields as $field) {
                                $dbColumn = $this->mapFieldNameToColumn($field->field_name);
                                if ($dbColumn && \Schema::hasColumn('users', $dbColumn)) {
                                    $inner->orWhere($dbColumn, 'like', "%{$search}%");
                                }
                            }
                        });
                    });
                    $q->orWhere('session_time', 'like', "%{$search}%");
                });
            })
            ->join('users as u_sort', 'u_sort.id', '=', 'user_attendances.user_id')
            ->orderBy('u_sort.created_at', 'desc')
            ->get();

        $dynamicHeaders = $activeFields->pluck('field_name')->map(fn($f) => ucwords(str_replace('_', ' ', $f)))->toArray();

        $headers = $isAdmin
            ? array_merge(['Event'], $dynamicHeaders, ['Session Time', 'Registration Date'])
            : array_merge($dynamicHeaders, ['Session Time', 'Registration Date']);

        $rows = [];
        $rows[] = implode(',', $headers);

        $esc = fn($val) => '"' . str_replace('"', '""', (string)$val) . '"';

        foreach ($query as $attendance) {
            $u = $attendance->user;

            $row = [];

            if ($isAdmin) {
                $row[] = $esc($u?->event?->name ?? '');
            }

            foreach ($activeFields as $field) {
                $dbColumn = $this->mapFieldNameToColumn($field->field_name);
                $row[] = $esc($u?->{$dbColumn} ?? '');
            }

            $row[] = $esc($attendance->session_time ?? 0);
            $row[] = $esc($u?->created_at ? Carbon::parse($u->created_at)->format('d M Y h:i A') : '');

            $rows[] = implode(',', $row);
        }

        $csv = implode("\n", $rows);
        $filename = 'user_attendance_export_' . now()->format('Y-m-d') . '.csv';

        return response($csv, 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }
}
