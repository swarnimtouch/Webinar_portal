<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function (){
    return "Welcome To Webinar Portal";
});
Route::get('admin', function (){
    return redirect()->route('admin.login');
});
