<?php

use Illuminate\Support\Facades\Route;
use App\Models\Setting;
use Spatie\Honeypot\ProtectAgainstSpam;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

//  Auth::routes(['register' => false]);
Route::get('/', function () {
    return redirect('home');
});
Auth::routes(['verify' => true]);
Route::middleware(ProtectAgainstSpam::class)->group(function() {
    Auth::routes(['register']);
});


Route::get('/', [App\Http\Controllers\HomeController::class, 'index'])->name('/');
Route::post('ajaxLoad', [App\Http\Controllers\HomeController::class, 'ajaxLoad'])->name('ajaxLoad');
Route::post('ajaxAccompany', [App\Http\Controllers\HomeController::class, 'ajaxAccompany'])->name('ajaxAccompany');
Route::post('projectSubmit', [App\Http\Controllers\HomeController::class, 'projectSubmit'])->name('projectSubmit');
Route::post('abstractNow', [App\Http\Controllers\HomeController::class, 'abstractNow'])->name('abstractNow');
Route::post('abstractSubmit', [App\Http\Controllers\HomeController::class, 'abstractSubmit'])->name('abstractSubmit');
Route::post('isRegister', [App\Http\Controllers\HomeController::class, 'isRegister'])->name('isRegister');
Route::get('about', [App\Http\Controllers\HomeController::class, 'about'])->name('about');
Route::get('committee', [App\Http\Controllers\HomeController::class, 'committee'])->name('committee');
Route::get('speakers', [App\Http\Controllers\HomeController::class, 'speakers'])->name('speakers');
Route::get('program-schedule', [App\Http\Controllers\HomeController::class, 'programSchedule'])->name('program-schedule');
Route::get('donation', [App\Http\Controllers\HomeController::class, 'donation'])->name('donation');
Route::get('awards', [App\Http\Controllers\HomeController::class, 'awards'])->name('awards');
Route::get('guidelines', [App\Http\Controllers\HomeController::class, 'guidelines'])->name('guidelines');
Route::get('contact-us', [App\Http\Controllers\HomeController::class, 'contact_us'])->name('contact-us');
Route::get('privacy-policy', [App\Http\Controllers\HomeController::class, 'policy'])->name('privacy-policy');
Route::get('faq', [App\Http\Controllers\HomeController::class, 'faq'])->name('faq');
Route::get('term-condition', [App\Http\Controllers\HomeController::class, 'term_condition'])->name('term-condition');
Route::get('reviews', [App\Http\Controllers\HomeController::class, 'reviews'])->name('reviews');
Route::get('abstract-book', [App\Http\Controllers\HomeController::class, 'abstractBook'])->name('abstract-book');
Route::get('gallery', [App\Http\Controllers\HomeController::class, 'gallery'])->name('gallery');

// Route::get('register', [App\Http\Controllers\HomeController::class, 'register'])->name('register');
// Route::get('register/registration', [App\Http\Controllers\HomeController::class, 'registration'])->name('register.registration');
Route::get('spot-regi', [App\Http\Controllers\HomeController::class, 'spotRegistration'])->name('spot-regi');
Route::post('spotVerify', [App\Http\Controllers\HomeController::class, 'spotVerify'])->name('spotVerify');

Route::match(['get', 'post'],'register/abstract', [App\Http\Controllers\HomeController::class, 'abstract_submission'])->name('register.abstract');
Route::post('contact_submit', [App\Http\Controllers\HomeController::class, 'contact_submit'])->name('contact-submit');
Route::post('subscribe_submit', [App\Http\Controllers\HomeController::class, 'subscribe_submit'])->name('subscribe-submit');
Route::post('faq_submit', [App\Http\Controllers\HomeController::class, 'faq_submit'])->name('faq-submit');
Route::get('mailTest', [App\Http\Controllers\HomeController::class, 'mailTest'])->name('mailTest');

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
Route::get('/register-success', [App\Http\Controllers\HomeController::class, 'registerSuccess'])->name('register-success');
Route::get('/register-failure', [App\Http\Controllers\HomeController::class, 'registerFailure'])->name('register-failure');
Route::get('send-otp', [App\Http\Controllers\HomeController::class, 'sendOtp'])->name('send-otp');
Route::post('check-email', [App\Http\Controllers\Auth\RegisterController::class, 'checkEmail'])->name('check-email');

Route::get('venue-and-accommodation',function() {
    
    return view('venue-and-accommodation')->with(['settings'=>Setting::where('status',1)->get()]);
})->name('venue-and-accommodation');

Route::middleware(['auth:customer','verified'])->group(function () {
Route::get('accounts/dashboard', [App\Http\Controllers\DashboardController::class, 'index'])->name('accounts.dashboard');
Route::get('viewParticipants', [App\Http\Controllers\DashboardController::class, 'viewParticipants'])->name('viewParticipants');
Route::get('viewDashboard', [App\Http\Controllers\DashboardController::class, 'viewDashboard'])->name('viewDashboard');
Route::resource('participant', App\Http\Controllers\ParticipantController::class);
Route::get('participants', [App\Http\Controllers\ParticipantController::class, 'participants'])->name('participants');
Route::get('payment-details', [App\Http\Controllers\ParticipantController::class, 'payment_details'])->name('payment-details');
Route::resource('abstract', App\Http\Controllers\AbstractController::class);
Route::get('participants-category', [App\Http\Controllers\AbstractController::class, 'participantsCategory'])->name('participants-category');

// Payment routes - specific routes before resource to avoid conflicts
Route::get('payment/summary', [App\Http\Controllers\PaymentController::class, 'getPaymentSummary'])->name('payment.summary');
Route::post('accounts/registration/payment', [App\Http\Controllers\PaymentController::class, 'store'])->name('account.registration.payment');
Route::post('payment/cancel', [App\Http\Controllers\PaymentController::class, 'payment_cancel'])->name('payment.cancel');
Route::resource('payment', App\Http\Controllers\PaymentController::class);

Route::post('refund-request', [App\Http\Controllers\PaymentController::class, 'refundRequest'])->name('refund-request');
Route::post('accounts/update-attending-status', [App\Http\Controllers\DashboardController::class, 'updateAttendingStatus'])->name('account.update-attending-status');

});

// Public routes (no auth required)
Route::get('payment/success', [App\Http\Controllers\PaymentController::class, 'paymentSuccess'])->name('payment.success');
Route::get('payment/cancel', [App\Http\Controllers\PaymentController::class, 'payment_cancel'])->name('payment.cancel');
Route::get('payment/verify/{payment}', [App\Http\Controllers\PaymentController::class, 'verifyPayment'])->name('payment.verify')->middleware('signed');
Route::post('payment-response', [App\Http\Controllers\PaymentController::class, 'paymentResponse'])->name('payment.response');





View::Composer(['components.footer', 'components.header', 'layouts.app','auth.register','auth.login'], function ($view) {
    $view->with([
        'settings' => Setting::get(),
    ]);
});
