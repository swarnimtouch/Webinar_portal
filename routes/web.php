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
use App\Http\Controllers\Website\HomeController;


use Illuminate\Support\Facades\Route;


Route::get('/', [HomeController::class, 'index'])->name('home');
Route::post('/website/login', [HomeController::class, 'login'])
    ->name('website.login.submit');
Route::post('/website/register', [HomeController::class, 'register'])
    ->name('website.register.submit');
Route::middleware(['auth'])->prefix('website')->group(function () {
    Route::get('/dashboard',[\App\Http\Controllers\Website\DashboardController::class, 'dashboard'])->name('website.dashboard');
});



Route::get('/login', [LoginController::class, 'show'])->name('login');
Route::post('/login', [LoginController::class, 'authenticate']);
Route::get('/logout', [LoginController::class, 'logout'])->name('logout');

Route::middleware(['auth'])->group(function () {
    Route::get('/admin/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::resource('admin/user', UserController::class);
    Route::post('admin/user/delete-multiple', [UserController::class, 'deleteMultiple'])->name('user.deleteMultiple');


//    Route::get('/', function () {
//        return redirect()->route('dashboard');
//    });
    Route::get('/admin/admin-profile', [ProfileController::class, 'index'])->name('admin.profile');
    Route::post('/admin/profile/update', [ProfileController::class, 'update'])->name('admin.profile.update');
    Route::get('/admin/password', [ProfileController::class, 'password'])
        ->name('admin.password');

    Route::post('/admin/password/update', [ProfileController::class, 'updatePassword'])
        ->name('admin.password.update');
    Route::get('admin/sitesettings', [SiteSettingsController::class, 'index'])    ->name('settings');
    Route::post('admin/sitesettings/update', [SiteSettingsController::class, 'update'])    ->name('settings.update');

    Route::get('admin/banners',[BannerController::class, 'index'])    ->name('banners');
    Route::get('admin/banners/create/{id?}',[BannerController::class, 'create'])    ->name('banner.create');
    Route::match(['POST', 'PUT'], 'admin/banners/store/{id?}', [BannerController::class, 'store'])
        ->name('banners.store');
    Route::get('admin/banner/show/{banner}', [BannerController::class, 'show'])->name('banner.show');
    Route::delete('admin/delete/{id}', [BannerController::class, 'delete'])->name('banner.delete');
    Route::post('/banner/delete-multiple', [BannerController::class, 'deleteMultiple'])->name('banner.deleteMultiple');
    Route::post('/banner/toggle-status/{id}', [BannerController::class, 'toggleStatus'])->name('banner.toggleStatus');

    Route::get('admin/speakers', [SpeakersController::class, 'index'])    ->name('speakers');
    Route::get('admin/speakers/create/{id?}',[SpeakersController::class, 'create'])    ->name('speaker.create');
    Route::match(['POST', 'PUT'], 'admin/speakers/store/{id?}', [SpeakersController::class, 'store'])
        ->name('speakers.store');
    Route::delete('admin/speaker/{id}', [SpeakersController::class, 'delete'])->name('speaker.delete');
    Route::post('admin/speaker/delete-multiple', [SpeakersController::class, 'deleteMultiple'])->name('speaker.deleteMultiple');
    Route::post('/toggle-status/{id}', [SpeakersController::class, 'toggleStatus'])->name('speaker.toggleStatus');


    Route::get('admin/brand',[BrandsController::class, 'index'])    ->name('brand');
    Route::get('admin/brand/create/{id?}', [BrandsController::class, 'create'])->name('brand.create');
    Route::match(['POST', 'PUT'], 'admin/brand/store/{id?}', [BrandsController::class, 'store'])->name('brand.store');
    Route::delete('/brand/delete/{id}', [BrandsController::class, 'delete'])->name('brand.delete');
    Route::post('/brand/delete-multiple', [BrandsController::class, 'deleteMultiple'])->name('brand.deleteMultiple');
    Route::post('/brand/toggle-status/{id}', [BrandsController::class, 'toggleStatus'])->name('brand.toggleStatus');

    Route::get('admin/content',[ContentController::class, 'index'])    ->name('content');
    Route::get('admin/content/edit/{id}',[ContentController::class, 'edit'])    ->name('content.edit');
    Route::put('admin/content/update/{id}',[ContentController::class, 'update'])    ->name('content.update');

    Route::get('admin/dynamic-fields',[DaynamicFieldsController::class,'index'])    ->name('dynamic-fields');
    Route::post('admin/dynamic-fields/store',[DaynamicFieldsController::class,'store'])    ->name('dynamic-fields.store');


});
Route::get('/{slug}', [ContentController::class, 'show']);
