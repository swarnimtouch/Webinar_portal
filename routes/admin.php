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
    EventSettingController,
    UserAttendanceController,
    FeedbackController,
    PollController,
    UserPollResultController,
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
        Route::get('events/companies/search', 'searchCompanies')->name('events.companies.search');
        Route::post('events/companies', 'storeCompany')->name('events.companies.store');
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
        Route::get('users/export', 'export')->name('user.export');

        Route::get('/get-countries', 'countries')->name('users.countries');
        Route::get('/get-states/{country}', 'states')->name('users.states');
        Route::get('/get-cities/{state}', 'cities')->name('users.cities');
        Route::get('get-event-fields/{eventId}', 'getEventFields')->name('get-event-fields');
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
        Route::match(['POST', 'PUT'], 'content/save/{id?}', 'save')->name('content.save');
        Route::get('content/datatable', 'datatable')->name('content.datatable');
    });

    /*
    | Misc Modules
    */
    Route::controller(DynamicFieldsController::class)->group(function () {
        Route::get('dynamic_fields', 'index')->name('dynamic_fields');
        Route::post('dynamic_fields/save', 'save')->name('dynamic_fields.save');
    });

    /*
        | Event
        */
    Route::controller(EventSettingController::class)->group(function () {
        Route::get('event-setting', 'index')->name('event_setting');
        Route::post('event-setting/save', 'save')->name('event_setting.save');
    });

    /*
    | User Attendance
    */
    Route::controller(UserAttendanceController::class)->group(function () {
        Route::get('user_attendance', 'index')->name('user_attendance');
        Route::delete('user_attendance/delete/{id}', 'delete')->name('user_attendance.delete');
        Route::post('user_attendance/delete-multiple', 'deleteMultiple')->name('user_attendance.deleteMultiple');
        Route::get('user_attendance/datatable', 'datatable')->name('user_attendance.datatable');
        Route::get('user_attendance/export', 'export')->name('user_attendance.export');
    });

    /*
    | Feedback
    */
    Route::controller(FeedbackController::class)->group(function () {
        Route::get('feedback', 'index')->name('feedback.index');
        Route::delete('feedback/delete/{id}', 'delete')->name('feedback.delete');
        Route::post('feedback/delete-multiple', 'deleteMultiple')->name('feedback.deleteMultiple');
        Route::get('feedback/datatable', 'datatable')->name('feedback.datatable');
        Route::get('feedback/export', 'export')->name('feedback.export');

    });

    /*
    | Poll
    */
    Route::controller(PollController::class)->group(function () {
        Route::get('poll', 'index')->name('poll');
        Route::get('poll/add-edit/{id?}', 'addEditForm')->name('poll.add_edit_form');
        Route::match(['POST', 'PUT'], 'poll/save/{id?}', 'save')->name('poll.save');
        Route::delete('poll/delete/{id}', 'delete')->name('poll.delete');

        Route::post('poll/delete-multiple', 'deleteMultiple')->name('poll.deleteMultiple');
        Route::post('poll/toggle-status/{id}', 'toggleStatus')->name('poll.toggleStatus');
        Route::get('poll/datatable', 'datatable')->name('poll.datatable');
    });

    /*
    | Poll Result
    */
    Route::controller(UserPollResultController::class)->group(function () {
        Route::get('user_poll_result', 'index')->name('user_poll_result');
        Route::delete('user_poll_result/delete/{id}', 'delete')->name('user_poll_result.delete');
        Route::post('user_poll_result/delete-multiple', 'deleteMultiple')->name('user_poll_result.deleteMultiple');
        Route::get('user_poll_result/datatable', 'datatable')->name('user_poll_result.datatable');
        Route::get('user_poll_result/export', 'export')->name('user_poll_result.export');
    });

    /*
    | Chat Log
    */
    Route::controller(ChatLogController::class)->group(function () {
        Route::get('chat_log', 'index')->name('chat_log');
        Route::delete('chat_log/delete/{id}', 'delete')->name('chat_log.delete');
        Route::post('chat_log/delete-multiple', 'deleteMultiple')->name('chat_log.deleteMultiple');
        Route::get('chat_log/datatable', 'datatable')->name('chat_log.datatable');
        Route::get('chat_log/export', 'export')->name('chat_log.export');
        Route::get('chat-log/{id}', 'show')->name('chat_log.show');
        Route::post('chat-log/{id}/send', 'sendMessage')->name('chat_log.send');

    });

    /*
    | Certificate
    */
    Route::controller(CertificateController::class)->group(function () {
        Route::get('certificate', 'index')->name('certificate');
        Route::get('certificate/add-edit/{id?}', 'addEditForm')->name('certificate.add_edit_form');
        Route::match(['POST', 'PUT'], 'certificate/save/{id?}', 'save')->name('certificate.save');
        Route::delete('certificate/delete/{id}', 'delete')->name('certificate.delete');

        Route::post('certificate/delete-multiple', 'deleteMultiple')->name('certificate.deleteMultiple');
        Route::post('certificate/toggle-status/{id}', 'toggleStatus')->name('certificate.toggleStatus');
        Route::get('certificate/datatable', 'datatable')->name('certificate.datatable');
    });

    /*
    | Certificate Log
    */
    Route::controller(CertificateLogController::class)->group(function () {
        Route::get('certificate_log', 'index')->name('certificate_log');
        Route::delete('certificate_log/delete/{id}', 'delete')->name('certificate_log.delete');
        Route::post('certificate_log/delete-multiple', 'deleteMultiple')->name('certificate_log.deleteMultiple');
        Route::get('certificate_log/datatable', 'datatable')->name('certificate_log.datatable');
        Route::get('certificate_log/export', 'export')->name('certificate_log.export');
    });


});
