<?php

use App\Http\Controllers\Admin\ContentController;
use App\Http\Controllers\Website\HomeController;
use App\Http\Controllers\Website\DashboardController;
use App\Http\Controllers\Website\ChatController;
use App\Http\Controllers\Website\CertificateController;
use Illuminate\Support\Facades\Route;


Route::get('/', [HomeController::class, 'index'])->name('home');

Route::post('login', [HomeController::class, 'login'])->name('login');
Route::post('register', [HomeController::class, 'register'])->name('register');
Route::get('logout', [HomeController::class, 'logout'])->name('logout');

Route::get('/get-countries', [HomeController::class, 'countries'])->name('countries');
Route::get('/get-states/{country}', [HomeController::class, 'states'])->name('states');
Route::get('/get-cities/{state}', [HomeController::class, 'cities'])->name('cities');

Route::middleware(['auth'])->group(function () {
    Route::get('dashboard', [DashboardController::class, 'dashboard'])->name('dashboard');
    Route::get('/resources/{resourceId}/download', [DashboardController::class, 'downloadResource'])->name('resource.download');
    Route::post('/dashboard/attendance/update', [DashboardController::class, 'updateSessionTime'])->name('dashboard.attendance.update');
    Route::post('/feedback/save', [DashboardController::class, 'feedbackSave'])->name('feedback.save');
    Route::get('/poll', [DashboardController::class, 'getPoll'])->name('poll');
    Route::post('/poll/vote', [DashboardController::class, 'submitPoll'])->name('poll.vote');
    Route::get('/chat/messages', [ChatController::class, 'getMessages'])->name('chat.messages');
    Route::post('/chat/send',    [ChatController::class, 'sendMessage'])->name('chat.send');
    Route::post('/raise-hand',   [ChatController::class, 'raiseHand'])->name('raise.hand');
    Route::get('/hand-status',   [ChatController::class, 'handStatus'])->name('hand.status');
    Route::post('/attendance/join',   [DashboardController::class, 'attendanceJoin'])->name('attendance.join');
    Route::post('/attendance/leave',  [DashboardController::class, 'attendanceLeave'])->name('attendance.leave');
    Route::get('certificate/generate/{certificateId}/{userId}', [CertificateController::class, 'generate'])->name('certificate.generate');
});

Route::get('/{contentSlug}', [ContentController::class, 'show']);
