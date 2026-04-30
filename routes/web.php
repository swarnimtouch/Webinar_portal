<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Broadcast;
use App\Http\Controllers\Admin\CategoryController;


Broadcast::routes(['middleware' => ['web', 'event', 'auth:web']]);
/*Route::get('/', function () {
    return "Welcome To Webinar Portal";
});*/
Route::get('admin', function () {
    return redirect()->route('admin.login');
});

Route::prefix('admin')->name('admin.')->group(function () {

    Route::resource('category', CategoryController::class);

});
