<?php

use App\Http\Controllers\Admin\BannerController;
use App\Http\Controllers\Admin\BrandsController;
use App\Http\Controllers\Admin\ContentController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\DynamicFieldsController;
use App\Http\Controllers\Admin\ProfileController;
use App\Http\Controllers\Admin\SiteSettingsController;
use App\Http\Controllers\Admin\SpeakersController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Admin\HomeSettingController;
use App\Http\Controllers\Admin\UserAttendanceController;
use App\Http\Controllers\Admin\FeedbackController;
use App\Http\Controllers\Admin\PollController;
use App\Http\Controllers\Admin\UserQuizResult;
use App\Http\Controllers\Admin\ChatLogController;
use App\Http\Controllers\Admin\CertificateController;
use App\Http\Controllers\Admin\CertificateLogController;
use Illuminate\Support\Facades\Route;

Route::get('/login', [LoginController::class, 'index'])->name('login');
Route::post('/login', [LoginController::class, 'authenticate'])->name('post_login');
Route::get('/logout', [LoginController::class, 'logout'])->name('logout');

Route::middleware(['auth:admin'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('user/index', [UserController::class, 'index'])->name('user.index');
    Route::get('user/add-edit/{id?}', [UserController::class, 'addEditForm'])->name('user.add_edit_form');
    Route::match(['POST', 'PUT'], 'user/save/{id?}', [UserController::class, 'save'])
        ->name('user.save');
    Route::get('user/show/{id}', [UserController::class, 'show'])->name('user.show');
    Route::delete('users/{id}', [UserController::class, 'destroy'])->name('user.destroy');
    Route::post('/user/delete-multiple', [UserController::class, 'deleteMultiple'])->name('user.deleteMultiple');
    Route::get('users/datatable', [UserController::class, 'datatable'])->name('user.datatable');
    Route::get('/my-profile', [ProfileController::class, 'index'])->name('profile');
    Route::post('/profile/update', [ProfileController::class, 'update'])->name('profile.update');
    Route::get('/password', [ProfileController::class, 'password'])
        ->name('password');

    Route::post('/password/update', [ProfileController::class, 'updatePassword'])
        ->name('password.update');
    Route::get('site-settings', [SiteSettingsController::class, 'index'])->name('settings');
    Route::post('site-settings/save', [SiteSettingsController::class, 'save'])->name('settings.save');

    Route::get('banners', [BannerController::class, 'index'])->name('banners');
    Route::get('banners/add-edit/{id?}', [BannerController::class, 'addEditForm'])->name('banner.add_edit_form');
    Route::match(['POST', 'PUT'], 'banners/save/{id?}', [BannerController::class, 'save'])
        ->name('banners.save');
    Route::delete('delete/{id}', [BannerController::class, 'delete'])->name('banner.delete');
    Route::post('delete-multiple', [BannerController::class, 'deleteMultiple'])->name('banner.deleteMultiple');
    Route::post('banner/toggle-status/{id}', [BannerController::class, 'toggleStatus'])->name('banner.toggleStatus');
    Route::get('banner/datatable', [BannerController::class, 'datatable'])->name('banner.datatable');

    Route::get('speakers', [SpeakersController::class, 'index'])->name('speakers');
    Route::get('speakers/add-edit/{id?}', [SpeakersController::class, 'addEditForm'])->name('speaker.add_edit_form');
    Route::match(['POST', 'PUT'], 'speakers/save/{id?}', [SpeakersController::class, 'save'])
        ->name('speakers.save');
    Route::delete('speaker/{id}', [SpeakersController::class, 'delete'])->name('speaker.delete');
    Route::post('speaker/delete-multiple', [SpeakersController::class, 'deleteMultiple'])->name('speaker.deleteMultiple');
    Route::post('/toggle-status/{id}', [SpeakersController::class, 'toggleStatus'])->name('speaker.toggleStatus');
    Route::get('speaker/datatable', [SpeakersController::class, 'datatable'])->name('speaker.datatable');


    Route::get('brand', [BrandsController::class, 'index'])->name('brand');
    Route::get('brand/add-edit/{id?}', [BrandsController::class, 'addEditForm'])->name('brand.add_edit_form');
    Route::match(['POST', 'PUT'], 'brand/save/{id?}', [BrandsController::class, 'save'])->name('brand.save');
    Route::delete('brand/delete/{id}', [BrandsController::class, 'delete'])->name('brand.delete');
    Route::post('brand/delete-multiple', [BrandsController::class, 'deleteMultiple'])->name('brand.deleteMultiple');
    Route::post('brand/toggle-status/{id}', [BrandsController::class, 'toggleStatus'])->name('brand.toggleStatus');
    Route::get('brand/datatable', [BrandsController::class, 'datatable'])->name('brand.datatable');

    Route::get('content', [ContentController::class, 'index'])->name('content');
    Route::get('content/add-edit/{id?}', [ContentController::class, 'addEditForm'])->name('content.add_edit_form');
    Route::put('content/save/{id}', [ContentController::class, 'save'])->name('content.save');
    Route::get('content/datatable', [ContentController::class, 'datatable'])->name('content.datatable');

    Route::get('dynamic-fields', [DynamicFieldsController::class, 'index'])->name('dynamic-fields');
    Route::post('dynamic-fields/save', [DynamicFieldsController::class, 'save'])->name('dynamic-fields.save');


    Route::get('home-setting', [HomeSettingController::class, 'index'])->name('home_setting');
    Route::post('/home-setting/save', [HomeSettingController::class, 'save'])->name('home_setting.save');

    Route::get('attendance', [UserAttendanceController::class, 'index'])->name('user_attendance');
    Route::delete('attendance/delete/{id}', [UserAttendanceController::class, 'delete'])->name('user_attendance.delete');
    Route::post('attendance/delete-multiple', [UserAttendanceController::class, 'deleteMultiple'])->name('user_attendance.deleteMultiple');
    Route::get('attendance/datatable', [UserAttendanceController::class, 'datatable'])->name('user_attendance.datatable');

    Route::get('feedback', [FeedbackController::class, 'index'])->name('feedback.index');
    Route::delete('feedback/delete/{id}', [FeedbackController::class, 'delete'])->name('feedback.delete');
    Route::post('feedback/delete-multiple', [FeedbackController::class, 'deleteMultiple'])->name('feedback.deleteMultiple');
    Route::get('feedback/datatable', [FeedbackController::class, 'datatable'])->name('feedback.datatable');

    Route::get('poll', [PollController::class, 'index'])->name('poll');
    Route::get('poll/add-edit/{id?}', [PollController::class, 'addEditForm'])->name('poll.add_edit_form');
    Route::match(['POST', 'PUT'], 'poll/save/{id?}', [PollController::class, 'save'])->name('poll.save');
    Route::delete('poll/delete/{id}', [PollController::class, 'delete'])->name('poll.delete');
    Route::post('poll/delete-multiple', [PollController::class, 'deleteMultiple'])->name('poll.deleteMultiple');
    Route::post('poll/toggle-status/{id}', [PollController::class, 'toggleStatus'])->name('poll.toggleStatus');
    Route::get('poll/datatable', [PollController::class, 'datatable'])->name('poll.datatable');

    Route::get('user_quiz_result', [UserQuizResult::class, 'index'])->name('user_quiz_result');
    Route::delete('user_quiz_result/delete/{id}', [UserQuizResult::class, 'delete'])->name('user_quiz_result.delete');
    Route::post('user_quiz_result/delete-multiple', [UserQuizResult::class, 'deleteMultiple'])->name('user_quiz_result.deleteMultiple');
    Route::get('user_quiz_result/datatable', [UserQuizResult::class, 'datatable'])->name('user_quiz_result.datatable');

    Route::get('chatlog', [ChatLogController::class, 'index'])->name('chatlog');
    Route::delete('chatlog/delete/{id}', [ChatLogController::class, 'delete'])->name('chatlog.delete');
    Route::post('chatlog/delete-multiple', [ChatLogController::class, 'deleteMultiple'])->name('chatlog.deleteMultiple');
    Route::get('chatlog/datatable', [ChatLogController::class, 'datatable'])->name('chatlog.datatable');

    Route::get('certificate', [CertificateController::class, 'index'])->name('certificate');
    Route::get('certificate/add-edit/{id?}', [CertificateController::class, 'addEditForm'])->name('certificate.add_edit_form');
    Route::match(['POST', 'PUT'], 'certificate/save/{id?}', [CertificateController::class, 'save'])->name('certificate.save');
    Route::delete('certificate/delete/{id}', [CertificateController::class, 'delete'])->name('certificate.delete');
    Route::post('certificate/delete-multiple', [CertificateController::class, 'deleteMultiple'])->name('certificate.deleteMultiple');
    Route::post('certificate/toggle-status/{id}', [CertificateController::class, 'toggleStatus'])->name('certificate.toggleStatus');
    Route::get('certificate/datatable', [CertificateController::class, 'datatable'])->name('certificate.datatable');

    Route::get('certificate-log', [CertificateLogController::class, 'index'])->name('certificate-log');
    Route::delete('certificate-log/delete/{id}', [CertificateLogController::class, 'delete'])->name('certificate-log.delete');
    Route::post('certificate-log/delete-multiple', [CertificateLogController::class, 'deleteMultiple'])->name('certificate-log.deleteMultiple');
    Route::get('certificate-log/datatable', [CertificateLogController::class, 'datatable'])->name('certificate-log.datatable');

    Route::get('/get-countries', [UserController::class, 'countries'])->name('users.countries');

    Route::get('/get-states/{country}', [UserController::class, 'states'])->name('users.states');

    Route::get('/get-cities/{state}', [UserController::class, 'cities'])->name('users.cities');
    
});
