<?php

use App\Http\Controllers\Admin\BannerController;
use App\Http\Controllers\Admin\BrandsController;
use App\Http\Controllers\Admin\ContentController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\DaynamicFieldsController;
use App\Http\Controllers\Admin\ProfileController;
use App\Http\Controllers\Admin\SiteSettingsController;
use App\Http\Controllers\Admin\SpeakersController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Admin\HomeSettingController;
use App\Http\Controllers\Admin\UserAttendenceController;


use Illuminate\Support\Facades\Route;

Route::get('/login', [LoginController::class, 'index'])->name('login');
Route::post('/login', [LoginController::class, 'authenticate'])->name('post_login');
Route::get('/logout', [LoginController::class, 'logout'])->name('logout');

Route::middleware(['auth:admin'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::resource('/user', UserController::class);
    Route::post('/user/delete-multiple', [UserController::class, 'deleteMultiple'])->name('user.deleteMultiple');
    Route::get('users/datatable', [UserController::class, 'datatable'])->name('user.datatable');
    Route::get('/my-profile', [ProfileController::class, 'index'])->name('profile');
    Route::post('/profile/update', [ProfileController::class, 'update'])->name('profile.update');
    Route::get('/password', [ProfileController::class, 'password'])
        ->name('password');

    Route::post('/password/update', [ProfileController::class, 'updatePassword'])
        ->name('password.update');
    Route::get('site-settings', [SiteSettingsController::class, 'index'])->name('settings');
    Route::post('site-settings/update', [SiteSettingsController::class, 'update'])->name('settings.update');

    Route::get('banners', [BannerController::class, 'index'])->name('banners');
    Route::get('banners/add-edit/{id?}', [BannerController::class, 'addEditForm'])->name('banner.add_edit_form');
    Route::match(['POST', 'PUT'], 'banners/save/{id?}', [BannerController::class, 'save'])
        ->name('banners.save');
    Route::get('banner/show/{banner}', [BannerController::class, 'show'])->name('banner.show');
    Route::delete('delete/{id}', [BannerController::class, 'delete'])->name('banner.delete');
    Route::post('delete-multiple', [BannerController::class, 'deleteMultiple'])->name('banner.deleteMultiple');
    Route::post('banner/toggle-status/{id}', [BannerController::class, 'toggleStatus'])->name('banner.toggleStatus');
    Route::get('banner/datatable', [BannerController::class, 'datatable'])->name('banner.datatable');

    Route::get('speakers', [SpeakersController::class, 'index'])->name('speakers');
    Route::get('speakers/add-edit/{id?}', [SpeakersController::class, 'addEditForm'])->name('speaker.add_edit_form');
    Route::match(['POST', 'PUT'], 'speakers/store/{id?}', [SpeakersController::class, 'store'])
        ->name('speakers.store');
    Route::delete('speaker/{id}', [SpeakersController::class, 'delete'])->name('speaker.delete');
    Route::post('speaker/delete-multiple', [SpeakersController::class, 'deleteMultiple'])->name('speaker.deleteMultiple');
    Route::post('/toggle-status/{id}', [SpeakersController::class, 'toggleStatus'])->name('speaker.toggleStatus');
    Route::get('speaker/datatable', [SpeakersController::class, 'datatable'])->name('speaker.datatable');


    Route::get('brand', [BrandsController::class, 'index'])->name('brand');
    Route::get('brand/add-edit/{id?}', [BrandsController::class, 'addEditForm'])->name('brand.add_edit_form');
    Route::match(['POST', 'PUT'], 'brand/store/{id?}', [BrandsController::class, 'store'])->name('brand.store');
    Route::delete('brand/delete/{id}', [BrandsController::class, 'delete'])->name('brand.delete');
    Route::post('brand/delete-multiple', [BrandsController::class, 'deleteMultiple'])->name('brand.deleteMultiple');
    Route::post('brand/toggle-status/{id}', [BrandsController::class, 'toggleStatus'])->name('brand.toggleStatus');
    Route::get('brand/datatable', [BrandsController::class, 'datatable'])->name('brand.datatable');

    Route::get('content', [ContentController::class, 'index'])->name('content');
    Route::get('content/add-edit/{id?}', [ContentController::class, 'edit'])->name('content.add_edit_form');
    Route::put('content/update/{id}', [ContentController::class, 'update'])->name('content.update');
    Route::get('content/datatable', [ContentController::class, 'datatable'])->name('content.datatable');

    Route::get('dynamic-fields', [DaynamicFieldsController::class, 'index'])->name('dynamic-fields');
    Route::post('dynamic-fields/store', [DaynamicFieldsController::class, 'store'])->name('dynamic-fields.store');


    Route::get('home_setting', [HomeSettingController::class, 'index'])->name('home_setting');
    Route::get('home_setting/create/{id?}', [HomeSettingController::class, 'addEditForm'])->name('home_setting.create');
    Route::match(['POST', 'PUT'], 'home_setting/store/{id?}', [HomeSettingController::class, 'save'])->name('home_setting.save');
    Route::delete('home_setting/delete/{id}', [HomeSettingController::class, 'delete'])->name('home_setting.delete');
    Route::post('home_setting/delete-multiple', [HomeSettingController::class, 'deleteMultiple'])->name('home_setting.deleteMultiple');
    Route::post('home_setting/toggle-status/{id}', [HomeSettingController::class, 'toggleStatus'])->name('home_setting.toggleStatus');
    Route::get('home_setting/datatable', [HomeSettingController::class, 'datatable'])->name('home_setting.datatable');

    Route::get('attendance', [UserAttendenceController::class, 'index'])->name('user_attendance');
    Route::delete('attendance/delete/{id}', [UserAttendenceController::class, 'delete'])->name('user_attendance.delete');
    Route::post('attendance/delete-multiple', [UserAttendenceController::class, 'deleteMultiple'])->name('user_attendance.deleteMultiple');
    Route::get('attendance/datatable', [UserAttendenceController::class, 'datatable'])->name('user_attendance.datatable');

});
