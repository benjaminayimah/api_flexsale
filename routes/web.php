<?php

use App\Http\Controllers\API\storeController;
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
Route::get('/welcome-email', function () {
    return view('Email.welcomeMail');
});

Route::get('/generate-receipt/{user}/{store}/{receipt}/{token}', [storeController::class, 'generateReceipt']);

Route::get('/template', function () {
    return view('Email.template');
});