<?php

use App\Models\Information;
use App\Models\Setting;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

/* Route::get('/', function () {
    return view('welcome');
});
 */
Auth::routes(['register' => false]);

  Route::middleware(['web','auth:admin'])->group(function () {
    Route::get('/', [App\Http\Controllers\Admin\HomeController::class, 'index'])->name('dashboard');
    Route::resource('account', App\Http\Controllers\Admin\UserController::class);
    Route::resource('admin-users', App\Http\Controllers\Admin\AdminUserController::class);
    Route::resource('contact-us', App\Http\Controllers\Admin\ContactUsController::class);
    Route::resource('faq', App\Http\Controllers\Admin\FaqController::class);
    Route::resource('subscription', App\Http\Controllers\Admin\SubscriberController::class);
    Route::resource('mailTemplate', App\Http\Controllers\Admin\MailTemplateController::class);
    Route::resource('leads', App\Http\Controllers\Admin\LeadsController::class);
    Route::resource('category', App\Http\Controllers\Admin\CategoryController::class);
      Route::resource('category-class', App\Http\Controllers\Admin\CategoryClassController::class);
    Route::resource('abstract', App\Http\Controllers\Admin\AbstractController::class);
    Route::get('registration/participants', [App\Http\Controllers\Admin\RegistrationController::class, 'participants'])->name('registration.participants');
    Route::post('registration/participants/export', [App\Http\Controllers\Admin\RegistrationController::class, 'exportParticipants'])->name('registration.participants.export');
    Route::resource('registration', App\Http\Controllers\Admin\RegistrationController::class);
    Route::resource('payment', App\Http\Controllers\Admin\PaymentController::class);
    Route::resource('settings', App\Http\Controllers\Admin\SettingController::class);
Route::resource('landing-pages', App\Http\Controllers\Admin\LandingPageController::class);
    Route::get('social-media', [App\Http\Controllers\Admin\SocialMediaController::class, 'index'])->name('social-media.index');
    Route::post('social-media', [App\Http\Controllers\Admin\SocialMediaController::class, 'store'])->name('social-media.store');
    Route::post('award-graphics/headings', [App\Http\Controllers\Admin\AwardGraphicController::class, 'updateHeadings'])->name('award-graphics.headings');
    Route::resource('award-graphics', App\Http\Controllers\Admin\AwardGraphicController::class)->except(['show']);
    Route::resource('review', App\Http\Controllers\Admin\ReviewController::class);
    Route::resource('reviewer', App\Http\Controllers\Admin\ReviewerController::class);
    Route::resource('domain', App\Http\Controllers\Admin\AbstractDomainController::class);
    Route::get('program-schedule', [App\Http\Controllers\Admin\ProgramScheduleController::class, 'index'])->name('program-schedule.index');
    Route::post('program-schedule/settings', [App\Http\Controllers\Admin\ProgramScheduleController::class, 'updateSettings'])->name('program-schedule.settings');
    Route::resource('program-schedule-days', App\Http\Controllers\Admin\ProgramScheduleDayController::class)->except(['index', 'show']);
    Route::get('program-schedule-days/{program_schedule_day}/items', [App\Http\Controllers\Admin\ProgramScheduleItemController::class, 'index'])->name('program-schedule-items.index');
    Route::get('program-schedule-days/{program_schedule_day}/items/create', [App\Http\Controllers\Admin\ProgramScheduleItemController::class, 'create'])->name('program-schedule-items.create');
    Route::post('program-schedule-days/{program_schedule_day}/items', [App\Http\Controllers\Admin\ProgramScheduleItemController::class, 'store'])->name('program-schedule-items.store');
    Route::get('program-schedule-days/{program_schedule_day}/items/{program_schedule_item}/edit', [App\Http\Controllers\Admin\ProgramScheduleItemController::class, 'edit'])->name('program-schedule-items.edit');
    Route::put('program-schedule-days/{program_schedule_day}/items/{program_schedule_item}', [App\Http\Controllers\Admin\ProgramScheduleItemController::class, 'update'])->name('program-schedule-items.update');
    Route::delete('program-schedule-days/{program_schedule_day}/items/{program_schedule_item}', [App\Http\Controllers\Admin\ProgramScheduleItemController::class, 'destroy'])->name('program-schedule-items.destroy');
    Route::resource('gallery', App\Http\Controllers\Admin\GalleryController::class);
    Route::resource('abstract-book', App\Http\Controllers\Admin\AbstractBookController::class);
    Route::resource('team-category', App\Http\Controllers\Admin\TeamCategoryController::class);
    Route::resource('team', App\Http\Controllers\Admin\TeamController::class);
    Route::post('update-password', [App\Http\Controllers\Admin\UserController::class, 'updatePassword'])->name('update-password');
   // Route::get('getRegistration', [App\Http\Controllers\Admin\RegistrationController::class, 'getRegistration'])->name('registration');
    Route::post('getRegistrationById', [App\Http\Controllers\Admin\RegistrationController::class, 'getRegistrationById'])->name('getRegistrationById');
    Route::post('export-payments', [App\Http\Controllers\Admin\PaymentController::class, 'exportPayments'])->name('exportPayments');
    Route::post('approvePayment', [App\Http\Controllers\Admin\PaymentController::class, 'approvePayment'])->name('approvePayment');
    Route::get('paymentDetail', [App\Http\Controllers\Admin\PaymentController::class, 'paymentDetail'])->name('paymentDetail');
    Route::post('paymentRetrive', [App\Http\Controllers\Admin\PaymentController::class, 'paymentRetrive'])->name('paymentRetrive');
    Route::post('revertPaymentSync', [App\Http\Controllers\Admin\PaymentController::class, 'revertPaymentSync'])->name('revertPaymentSync');
    Route::post('revertPaymentsByTimestamp', [App\Http\Controllers\Admin\PaymentController::class, 'revertPaymentsByTimestamp'])->name('revertPaymentsByTimestamp');
    Route::post('clearCorruptedTransactionIds', [App\Http\Controllers\Admin\PaymentController::class, 'clearCorruptedTransactionIds'])->name('clearCorruptedTransactionIds');
    Route::post('checkPaymentStatus', [App\Http\Controllers\Admin\PaymentController::class, 'checkPaymentStatus'])->name('checkPaymentStatus');
    Route::get('getOldPendingPayments', [App\Http\Controllers\Admin\PaymentController::class, 'getOldPendingPayments'])->name('getOldPendingPayments');
    Route::get('verifyPaymentsAgainstGateway', [App\Http\Controllers\Admin\PaymentController::class, 'verifyPaymentsAgainstGateway'])->name('verifyPaymentsAgainstGateway');
    Route::post('markInvalidPaymentsAsFailed', [App\Http\Controllers\Admin\PaymentController::class, 'markInvalidPaymentsAsFailed'])->name('markInvalidPaymentsAsFailed');
    Route::post('resendPaymentEmail', [App\Http\Controllers\Admin\PaymentController::class, 'resendPaymentEmail'])->name('resendPaymentEmail');
    Route::post('verifyAccompany', [App\Http\Controllers\Admin\RegistrationController::class, 'verifyAccompany'])->name('verifyAccompany');
    Route::get('paymentView', [App\Http\Controllers\Admin\RegistrationController::class, 'paymentView'])->name('paymentView');
   // Route::get('abstractdetails', [App\Http\Controllers\Admin\AbstractController::class, 'abstractdetails'])->name('abstractdetails');
    Route::post('/admin/abstract/assign-reviewer', [App\Http\Controllers\Admin\AbstractController::class, 'assignReviewer'])->name('assignReviewer');
    Route::post('abstract/{abstract}/revert-to-draft', [App\Http\Controllers\Admin\AbstractController::class, 'revertToDraft'])->name('abstract.revert-to-draft');
    Route::match(['get', 'post'], 'irccExport', [App\Http\Controllers\Admin\HomeController::class, 'irccExport'])->name('irccExport');
    Route::get('abstractView', [App\Http\Controllers\Admin\AbstractController::class, 'abstractView'])->name('abstractView');
    Route::get('wordDownload', [App\Http\Controllers\Admin\AbstractController::class, 'wordDownload'])->name('wordDownload');
    Route::post('deleteTemplate', [App\Http\Controllers\Admin\MailTemplateController::class, 'deleteTemplate'])->name('deleteTemplate');
    Route::get('sendMail', [App\Http\Controllers\Admin\MailTemplateController::class, 'sendMail'])->name('sendMail');
    Route::post('sendMailSubmit', [App\Http\Controllers\Admin\MailTemplateController::class, 'sendMailSubmit'])->name('sendMailSubmit');
    Route::post('getTemplateById', [App\Http\Controllers\Admin\MailTemplateController::class, 'getTemplateById'])->name('getTemplateById');
    Route::post('getTagEmails', [App\Http\Controllers\Admin\MailTemplateController::class, 'getTagEmails'])->name('getTagEmails');
    Route::get('uploadLeads', [App\Http\Controllers\Admin\LeadsController::class, 'uploadLeads'])->name('uploadLeads');
    Route::get('sendInvi/{start}/{limit}/{template}/{table}', [App\Http\Controllers\Admin\LeadsController::class, 'sendInvi'])->name('sendInvi');
    Route::get('sendRegInvitation', [App\Http\Controllers\Admin\LeadsController::class, 'sendRegInvitation'])->name('sendRegInvitation');
    Route::post('deleteAttachmentById', [App\Http\Controllers\Admin\MailTemplateController::class, 'deleteAttachmentById'])->name('deleteAttachmentById');
    Route::post('deleteAbstractById', [App\Http\Controllers\Admin\AbstractBookController::class, 'deleteAbstractById'])->name('deleteAbstractById');
    Route::get('registrationReport', [App\Http\Controllers\Admin\HomeController::class, 'registrationReport'])->name('registrationReport');
    Route::post('export-registrations', [App\Http\Controllers\Admin\ExportController::class, 'exportRegistration'])->name('exportRegistration');
    Route::get('irccPdf/{id}', [App\Http\Controllers\Admin\AbstractController::class, 'irccPdf'])->name('irccPdf');
    Route::get('irccPdfAll', [App\Http\Controllers\Admin\HomeController::class, 'irccPdfAll'])->name('irccPdfAll');
    Route::post('editRegistration', [App\Http\Controllers\Admin\RegistrationController::class, 'editRegistration'])->name('editRegistration');
    Route::post('updateRegistration', [App\Http\Controllers\Admin\RegistrationController::class, 'updateRegistration'])->name('updateRegistration');
    Route::post('deleteParticipant', [App\Http\Controllers\Admin\RegistrationController::class, 'deleteParticipant'])->name('deleteParticipant');
    Route::get('registerSendMail/{id}', [App\Http\Controllers\Admin\RegistrationController::class, 'registerSendMail'])->name('registerSendMail');
    Route::get('/download-pdf/{abstractId}', [App\Http\Controllers\Admin\RegistrationController::class, 'downloadPdf'])->name('download.pdf');
    Route::post('addPayment', [App\Http\Controllers\Admin\RegistrationController::class, 'addPayment'])->name('addPayment');
    Route::post('deleteGalleryById','GalleryController@deleteGalleryById')->name('deleteGalleryById');
    Route::get('addSettings','SettingController@addSettings')->name('addSettings');
});

View::Composer(['footer'], function($view){
    if(auth()->check()){
        $view->with([
            'settings' => Setting::get(),
        ]);
    }

});
