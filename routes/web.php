<?php


use App\Http\Controllers\Admin\ContentController;
use App\Http\Controllers\Website\HomeController;
use App\Http\Controllers\Website\DashboardController;
use App\Http\Controllers\Website\ChatController;
use App\Http\Controllers\Website\CertificateController;


use Illuminate\Support\Facades\Route;


Route::get('/', [HomeController::class, 'index'])->name('home');

Route::post('/website/login', [HomeController::class, 'login'])
    ->name('website.login.submit');

Route::post('/website/register', [HomeController::class, 'register'])
    ->name('website.register.submit');
Route::get('/website/logout', [HomeController::class, 'logout'])->name('website.logout');

Route::get('/get-countries', [HomeController::class, 'countries']);

Route::get('/get-states/{country}', [HomeController::class, 'states']);

Route::get('/get-cities/{state}', [HomeController::class, 'cities']);

Route::get('/admin', function () {
    return redirect(route('admin.dashboard'));
});
Route::middleware(['auth'])->prefix('website')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'dashboard'])->name('website.dashboard');

    Route::post('/dashboard/attendance/update', [DashboardController::class, 'updateSessionTime'])->name('dashboard.attendance.update');

    Route::post('/feedback/store', [DashboardController::class, 'store'])
        ->name('feedback.store');

    Route::get('/poll', [DashboardController::class, 'getPoll'])->name('poll');

    Route::post('/poll/vote', [DashboardController::class, 'submitPoll'])->name('poll.vote');

    Route::get('/chat/messages', [ChatController::class, 'fetchMessages'])->name('chat.messages');

    Route::post('/chat/send', [ChatController::class, 'sendMessage'])->name('chat.send');
    Route::get('certificate/generate/{certificateId}/{userId}',
        [CertificateController::class, 'generate']
    )->name('admin.certificate.generate');
});

Route::get('/{slug}', [ContentController::class, 'show']);


