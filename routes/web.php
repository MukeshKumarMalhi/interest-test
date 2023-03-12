<?php

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

Route::get('/', function () {
    return view('welcome');
});

Route::get('/email/verify/{id}/{hash}', function (\App\Http\Requests\EmailVerificationRequest $request) {
    $request->fulfill();
    return "Thanks for Verifying your Email!";
})->middleware(['signed'])->name('verification.verify');
