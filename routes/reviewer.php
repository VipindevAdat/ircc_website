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

Route::middleware(['web','auth:reviewer'])->group(function () {
   Route::get('/', [App\Http\Controllers\Reviewer\HomeController::class, 'index'])->name('dashboard');
   Route::resource('account', App\Http\Controllers\Reviewer\UserController::class);
   Route::post('update-password', [App\Http\Controllers\Reviewer\UserController::class, 'updatePassword'])->name('update-password');
   Route::get('abstracts', [App\Http\Controllers\Reviewer\AbstractController::class, 'index'])->name('abstracts');
   Route::get('reviewer/abstract/score', [App\Http\Controllers\Reviewer\AbstractController::class, 'addScore'])->name('addScore');
   Route::post('reviewer/abstract/{id}/score', [App\Http\Controllers\Reviewer\AbstractController::class, 'storeScore'])->name('storeScore');
   Route::get('abstractView', [App\Http\Controllers\Reviewer\AbstractController::class, 'abstractView'])->name('abstractView');


});

View::Composer(['footer'], function($view){
    if(auth()->check()){
        $view->with([
            'settings' => Setting::get(),
        ]);
    }

});