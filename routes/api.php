<?php

//use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::apiResources([
    'user' => 'API\userController',
    'sign-in' => 'API\signinController',
    'temp-upload' => 'API\tempController',

    'temp-editupload' => 'API\tempEditController',
    'products' => 'API\productController',
    'tag' => 'API\tagController',
    'product-detail' => 'API\productDetailController',
    'add-to-cart' => 'API\cartController',
    'checkout' => 'API\checkoutController',
    'switch-store' => 'API\storeController',


]);
Route::post('/del-alltemp-img' , [
    'uses' => 'API\tempController@delAllTempImg',
]);
Route::post('/get-all-filters' , [
    'uses' => 'API\tagController@getAllFilters',
]);
Route::post('/get-this-filter' , [
    'uses' => 'API\tagController@getThisFilter',
]);



Route::post('/bulk-del-products', [
    'uses' => 'API\productController@bulkdelete',
]);
Route::post('/bulk-del-categories', [
    'uses' => 'API\categoryController@bulkdelete',
]);
Route::post('/product-update', [
    'uses' => 'API\productController@update',
]);
Route::post('/temp-update-img', [
    'uses' => 'API\tempController@storeEdit',
]);
Route::delete('/del-tempEdit', [
    'uses' => 'API\tempController@deleteEdit',
]);




Route::delete('/logout', [
    'uses' => 'API\signinController@destroy',
]);

Route::post('/get-cart', [
    'uses' => 'API\cartController@getcart',
]);
Route::post('/get-cartcount', [
    'uses' => 'API\cartController@getcartcount',
]);
Route::get('/check', [
    'uses' => 'API\checkoutController@store',
]);
