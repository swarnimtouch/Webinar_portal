<?php

namespace App\Http\Controllers\Website;

use App\Models\HomeSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController
{
    //
    public function dashboard()
    {
        $home_setting=HomeSetting::first();
        return view('website.dashboard',compact('home_setting'));
    }
}
