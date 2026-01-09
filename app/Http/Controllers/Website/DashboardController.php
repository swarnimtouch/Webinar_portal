<?php

namespace App\Http\Controllers\Website;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController
{
    //
    public function dashboard()
    {
        return view('website.dashboard');
    }
}
