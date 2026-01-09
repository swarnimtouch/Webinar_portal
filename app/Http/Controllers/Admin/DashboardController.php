<?php

namespace App\Http\Controllers\Admin;

class DashboardController extends Controller
{
    public function index()
    {
        return view('admin.dashboard', ['title' => __('Dashboard'), 'breadcrumb' => breadcrumb([__('Dashboard') => route('admin.dashboard')])]);
    }

}
