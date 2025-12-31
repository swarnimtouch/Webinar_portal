<?php
namespace App\Http\Controllers\Admin;

class DashboardController extends Controller
{
    public function __construct()
    {
    }

    public function index()
    {
        $breadcrumbs = [
            'Dashboard' => ''
        ];

        return view('admin.dashboard', compact('breadcrumbs'));
    }

}
