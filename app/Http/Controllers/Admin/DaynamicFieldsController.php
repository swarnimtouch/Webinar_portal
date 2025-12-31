<?php

namespace App\Http\Controllers\Admin;

use App\Models\DaynamicFields;
use Illuminate\Http\Request;

class DaynamicFieldsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $fields = DaynamicFields::orderBy('index_no')->get();
        $maxIndex = $fields->count();

        $title = 'Dynamic Field';
        $breadcrumbs = [
            'Dynamic Fields' => ''
        ];
        return view('admin.dynamic_fields.index', compact('fields','maxIndex','title','breadcrumbs'));
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            // 1️⃣ Update order
            if ($request->filled('order_data')) {
                $orderData = json_decode($request->order_data, true);

                foreach ($orderData as $item) {
                    DaynamicFields::where('id', $item['id'])
                        ->update(['index_no' => $item['index_no']]);
                }
            }

            // 2️⃣ Update fields (label, required, status)
            if ($request->has('fields')) {
                foreach ($request->fields as $fieldId => $fieldData) {
                    DaynamicFields::where('id', $fieldId)->update([
                        'label' => $fieldData['label'] ?? '',
                        'is_required' => $fieldData['is_required'] ?? 0,
                        'status' => $fieldData['status'] ?? 'inactive',
                    ]);
                }
            }

            // 3️⃣ Reset all login_with fields
            DaynamicFields::whereIn('field_name', ['email', 'mobile_number', 'password'])
                ->update(['login_with' => 0]);

            // 4️⃣ Set selected primary field (email or mobile)
            if ($request->filled('login_with')) {
                DaynamicFields::where('id', $request->login_with)
                    ->update(['login_with' => 1]);
            }

            // 5️⃣ Set password if checked
            if ($request->filled('password_required') && $request->password_required == 1) {
                DaynamicFields::where('field_name', 'password')
                    ->update(['login_with' => 1]);
            }

            return redirect()->back()->with('success', 'Fields updated successfully!');

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error: ' . $e->getMessage());
        }
    }




    /**
     * Display the specified resource.
     */
    public function show(DaynamicFields $daynamicFields)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(DaynamicFields $daynamicFields)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, DaynamicFields $daynamicFields)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(DaynamicFields $daynamicFields)
    {
        //
    }
}
