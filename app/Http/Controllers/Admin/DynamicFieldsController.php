<?php

namespace App\Http\Controllers\Admin;

use App\Models\DynamicFields;
use Illuminate\Http\Request;

class DynamicFieldsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $fields = DynamicFields::orderBy('index_no')->get();

        return view('admin.dynamic_fields.index', [
            'fields' => $fields,
            'maxIndex' => $fields->count(),
            'title' => __('Dynamic Fields'),
            'breadcrumb' => breadcrumb([
                __('Dynamic Fields') => route('admin.dynamic-fields')
            ])
        ]);
    }


    /**
     * Store a newly created resource in storage.
     */
    public function save(Request $request)
    {
        try {
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

            DynamicFields::whereIn('field_name', ['email', 'mobile_number', 'password'])
                ->update(['login_with' => 0]);

            if ($request->filled('login_with')) {
                DynamicFields::where('id', $request->login_with)
                    ->update(['login_with' => 1]);
            }

            if ($request->filled('password_required') && $request->password_required == 1) {
                DynamicFields::where('field_name', 'password')
                    ->update(['login_with' => 1]);
            }

            return redirect()->back()->with('success', 'Fields updated successfully!');

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error: ' . $e->getMessage());
        }
    }
}
