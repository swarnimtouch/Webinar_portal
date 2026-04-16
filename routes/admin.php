<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Admin\{
    DashboardController,
    UserController,
    ProfileController,
    SiteSettingsController,
    BannerController,
    SpeakersController,
    BrandsController,
    ContentController,
    DynamicFieldsController,
    HomeSettingController,
    UserAttendanceController,
    FeedbackController,
    PollController,
    UserQuizResult,
    ChatLogController,
    CertificateController,
    CertificateLogController,
    EventsController,
    SubAdminController
};

/*
|--------------------------------------------------------------------------
| Auth Routes (UNCHANGED)
|--------------------------------------------------------------------------
*/
Route::controller(LoginController::class)->group(function () {
    Route::get('/login', 'index')->name('login');
    Route::post('/login', 'authenticate')->name('post_login');
    Route::get('/logout', 'logout')->name('logout');
});


/*
|--------------------------------------------------------------------------
| Admin Routes (STRUCTURED ONLY)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth:admin'])->group(function () {

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    /*
    | Events
    */
    Route::controller(EventsController::class)->group(function () {
        Route::get('events', 'index')->name('events');
        Route::get('events/add-edit/{id?}', 'addEditForm')->name('events.add_edit_form');
        Route::match(['POST', 'PUT'], 'events/save/{id?}', 'save')->name('events.save');
        Route::delete('events/delete/{id}', 'delete')->name('events.delete');

        Route::post('events/delete-multiple', 'deleteMultiple')->name('events.deleteMultiple');
        Route::post('events/toggle-status/{id}', 'toggleStatus')->name('events.toggleStatus');
        Route::get('events/datatable', 'datatable')->name('events.datatable');
    });

    /*
    | Sub Admin
    */
    Route::controller(SubAdminController::class)->group(function () {
        Route::get('sub-admin', 'index')->name('sub_admin');
        Route::get('sub-admin/add-edit/{id?}', 'addEditForm')->name('sub_admin.add_edit_form');
        Route::match(['POST', 'PUT'], 'sub-admin/save/{id?}', 'save')->name('sub_admin.save');
        Route::delete('sub-admin/delete/{id}', 'delete')->name('sub_admin.delete');

        Route::post('sub-admin/delete-multiple', 'deleteMultiple')->name('sub_admin.deleteMultiple');
        Route::post('sub-admin/toggle-status/{id}', 'toggleStatus')->name('sub_admin.toggleStatus');
        Route::get('sub-admin/datatable', 'datatable')->name('sub_admin.datatable');
    });

    /*
    | Users
    */
    Route::controller(UserController::class)->group(function () {
        Route::get('users', 'index')->name('user.index');
        Route::get('user/add-edit/{id?}', 'addEditForm')->name('user.add_edit_form');
        Route::match(['POST', 'PUT'], 'user/save/{id?}', 'save')->name('user.save');
        Route::get('user/show/{id}', 'show')->name('user.show');
        Route::delete('users/{id}', 'destroy')->name('user.destroy');

        Route::post('/user/delete-multiple', 'deleteMultiple')->name('user.deleteMultiple');
        Route::get('users/datatable', 'datatable')->name('user.datatable');

        Route::get('/get-countries', 'countries')->name('users.countries');
        Route::get('/get-states/{country}', 'states')->name('users.states');
        Route::get('/get-cities/{state}', 'cities')->name('users.cities');
    });

    /*
    | Profile
    */
    Route::controller(ProfileController::class)->group(function () {
        Route::get('/my-profile', 'index')->name('profile');
        Route::post('/profile/update', 'update')->name('profile.update');
        Route::get('/password', 'password')->name('password');
        Route::post('/password/update', 'updatePassword')->name('password.update');
        Route::post('check-email-exists', 'checkEmailExists')->name('check-email-exists');
        Route::post('check-mobile-exists', 'checkMobileExists')->name('check-mobile-exists');
    });

    /*
    | Settings
    */
    Route::controller(SiteSettingsController::class)->group(function () {
        Route::get('site-settings', 'index')->name('settings');
        Route::post('site-settings/save', 'save')->name('settings.save');
    });

    /*
    | Banners
    */
    Route::controller(BannerController::class)->group(function () {
        Route::get('banners', 'index')->name('banners');
        Route::get('banners/add-edit/{id?}', 'addEditForm')->name('banner.add_edit_form');
        Route::match(['POST', 'PUT'], 'banners/save/{id?}', 'save')->name('banners.save');
        Route::delete('delete/{id}', 'delete')->name('banner.delete');

        Route::post('delete-multiple', 'deleteMultiple')->name('banner.deleteMultiple');
        Route::post('banner/toggle-status/{id}', 'toggleStatus')->name('banner.toggleStatus');
        Route::get('banner/datatable', 'datatable')->name('banner.datatable');
    });

    /*
    | Speakers
    */
    Route::controller(SpeakersController::class)->group(function () {
        Route::get('speakers', 'index')->name('speakers');
        Route::get('speakers/add-edit/{id?}', 'addEditForm')->name('speaker.add_edit_form');
        Route::match(['POST', 'PUT'], 'speakers/save/{id?}', 'save')->name('speakers.save');
        Route::delete('speaker/{id}', 'delete')->name('speaker.delete');

        Route::post('speaker/delete-multiple', 'deleteMultiple')->name('speaker.deleteMultiple');
        Route::post('/toggle-status/{id}', 'toggleStatus')->name('speaker.toggleStatus');
        Route::get('speaker/datatable', 'datatable')->name('speaker.datatable');
    });

    /*
    | Brands
    */
    Route::controller(BrandsController::class)->group(function () {
        Route::get('brand', 'index')->name('brand');
        Route::get('brand/add-edit/{id?}', 'addEditForm')->name('brand.add_edit_form');
        Route::match(['POST', 'PUT'], 'brand/save/{id?}', 'save')->name('brand.save');
        Route::delete('brand/delete/{id}', 'delete')->name('brand.delete');

        Route::post('brand/delete-multiple', 'deleteMultiple')->name('brand.deleteMultiple');
        Route::post('brand/toggle-status/{id}', 'toggleStatus')->name('brand.toggleStatus');
        Route::get('brand/datatable', 'datatable')->name('brand.datatable');
    });

    /*
    | Content
    */
    Route::controller(ContentController::class)->group(function () {
        Route::get('content', 'index')->name('content');
        Route::get('content/add-edit/{id?}', 'addEditForm')->name('content.add_edit_form');
        Route::put('content/save/{id}', 'save')->name('content.save');
        Route::get('content/datatable', 'datatable')->name('content.datatable');
    });

    /*
    | Misc Modules
    */
    Route::controller(DynamicFieldsController::class)->group(function () {
        Route::get('dynamic-fields', 'index')->name('dynamic-fields');
        Route::post('dynamic-fields/save', 'save')->name('dynamic-fields.save');
    });

    Route::controller(HomeSettingController::class)->group(function () {
        Route::get('home-setting', 'index')->name('home_setting');
        Route::post('/home-setting/save', 'save')->name('home_setting.save');
    });

    Route::controller(UserAttendanceController::class)->group(function () {
        Route::get('attendance', 'index')->name('user_attendance');
        Route::delete('attendance/delete/{id}', 'delete')->name('user_attendance.delete');
        Route::post('attendance/delete-multiple', 'deleteMultiple')->name('user_attendance.deleteMultiple');
        Route::get('attendance/datatable', 'datatable')->name('user_attendance.datatable');
    });

    Route::controller(FeedbackController::class)->group(function () {
        Route::get('feedback', 'index')->name('feedback.index');
        Route::delete('feedback/delete/{id}', 'delete')->name('feedback.delete');
        Route::post('feedback/delete-multiple', 'deleteMultiple')->name('feedback.deleteMultiple');
        Route::get('feedback/datatable', 'datatable')->name('feedback.datatable');
    });

    Route::controller(PollController::class)->group(function () {
        Route::get('poll', 'index')->name('poll');
        Route::get('poll/add-edit/{id?}', 'addEditForm')->name('poll.add_edit_form');
        Route::match(['POST', 'PUT'], 'poll/save/{id?}', 'save')->name('poll.save');
        Route::delete('poll/delete/{id}', 'delete')->name('poll.delete');

        Route::post('poll/delete-multiple', 'deleteMultiple')->name('poll.deleteMultiple');
        Route::post('poll/toggle-status/{id}', 'toggleStatus')->name('poll.toggleStatus');
        Route::get('poll/datatable', 'datatable')->name('poll.datatable');
    });

    Route::controller(UserQuizResult::class)->group(function () {
        Route::get('user_quiz_result', 'index')->name('user_quiz_result');
        Route::delete('user_quiz_result/delete/{id}', 'delete')->name('user_quiz_result.delete');
        Route::post('user_quiz_result/delete-multiple', 'deleteMultiple')->name('user_quiz_result.deleteMultiple');
        Route::get('user_quiz_result/datatable', 'datatable')->name('user_quiz_result.datatable');
    });

    Route::controller(ChatLogController::class)->group(function () {
        Route::get('chatlog', 'index')->name('chat_log');
        Route::delete('chatlog/delete/{id}', 'delete')->name('chat_log.delete');
        Route::post('chatlog/delete-multiple', 'deleteMultiple')->name('chat_log.deleteMultiple');
        Route::get('chatlog/datatable', 'datatable')->name('chat_log.datatable');
    });

    Route::controller(CertificateController::class)->group(function () {
        Route::get('certificate', 'index')->name('certificate');
        Route::get('certificate/add-edit/{id?}', 'addEditForm')->name('certificate.add_edit_form');
        Route::match(['POST', 'PUT'], 'certificate/save/{id?}', 'save')->name('certificate.save');
        Route::delete('certificate/delete/{id}', 'delete')->name('certificate.delete');

        Route::post('certificate/delete-multiple', 'deleteMultiple')->name('certificate.deleteMultiple');
        Route::post('certificate/toggle-status/{id}', 'toggleStatus')->name('certificate.toggleStatus');
        Route::get('certificate/datatable', 'datatable')->name('certificate.datatable');
    });

    Route::controller(CertificateLogController::class)->group(function () {
        Route::get('certificate-log', 'index')->name('certificate-log');
        Route::delete('certificate-log/delete/{id}', 'delete')->name('certificate-log.delete');
        Route::post('certificate-log/delete-multiple', 'deleteMultiple')->name('certificate-log.deleteMultiple');
        Route::get('certificate-log/datatable', 'datatable')->name('certificate-log.datatable');
    });

});
