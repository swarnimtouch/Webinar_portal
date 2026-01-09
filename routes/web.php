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
Route::get('/website/logout', [HomeController::class, 'logout'])->name('website.logout');
Route::middleware(['auth'])->prefix('website')->group(function () {
    Route::get('/dashboard',[\App\Http\Controllers\Website\DashboardController::class, 'dashboard'])->name('website.dashboard');
});
Route::get('/get-countries', [HomeController::class, 'countries']);
Route::get('/get-states/{country}', [HomeController::class, 'states']);
Route::get('/get-cities/{state}', [HomeController::class, 'cities']);
Route::get('/{slug}', [ContentController::class, 'show']);
