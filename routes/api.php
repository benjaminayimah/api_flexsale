<?php

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
    'sign-up' => 'API\signupController',
    'temp-upload' => 'API\tempController',
    'temp-editupload' => 'API\tempEditController',
    'products' => 'API\productController',
    'tag' => 'API\tagController',
    'product-detail' => 'API\productDetailController',
    'add-to-cart' => 'API\cartController',
    'checkout' => 'API\checkoutController',
    'store' => 'API\storeController',
    'discount' => 'API\discountController',
    'perform-sale' => 'API\saleController',
    'suppliers' => 'API\supplierController',
    'trash' => 'API\trashController',
    'notification' => 'API\notificationController',
    'search' => 'API\searchController',
    'waiting-list' => 'API\mailingController',
    'product-batch' => 'API\stockController',




    

]);
Route::post('/fetch-item' , [
    'uses' => 'API\saleController@fetchItem',
]);
Route::post('/fetch-detailed-record-list' , [
    'uses' => 'API\saleController@fetchDetailedRecordList',
]);
Route::post('/receipt-detailed-record' , [
    'uses' => 'API\saleController@receiptDetailedRecord',
]);
Route::post('/get-admin-users' , [
    'uses' => 'API\userController@fetchAdmins',
]);
Route::post('/get-this-admin-user' , [
    'uses' => 'API\userController@fetchThisAdmin',
]);
Route::post('/move-to-trash' , [
    'uses' => 'API\trashController@moveThisToTrash',
]);
Route::post('/bulk-restore-trash' , [
    'uses' => 'API\trashController@bulkRestoreProducts',
]);
Route::post('/bulk-delete-trash-selection' , [
    'uses' => 'API\trashController@bulkDeleteTrash',
]);
Route::post('/empty-trash' , [
    'uses' => 'API\trashController@emptyTrash',
]);
Route::post('/oauth-signin' , [
    'uses' => 'API\signinController@OAuthSignIn',
]);
Route::post('/oauth-sign-up' , [
    'uses' => 'API\signinController@OAuthSignUp',
]);
Route::post('/refresh-user' , [
    'uses' => 'API\userController@reFreshUser',
]);
Route::post('/fetch-prod-batches' , [
    'uses' => 'API\stockController@fetchBatches',
]);




Route::post('/create-admin-user' , [
    'uses' => 'API\signupController@createAdminUser',
]);
Route::put('/edit-admin-user/{id}' , [
    'uses' => 'API\signupController@editAdminUser',
]);
Route::put('/reset-password/{id}' , [
    'uses' => 'API\signupController@resetPassword',
]);
Route::post('/switch-store' , [
    'uses' => 'API\storeController@switchStore',
]);
Route::post('/store-temp-upload' , [
    'uses' => 'API\tempController@storeTempUpload',
]);
Route::delete('/del-store-temp/{id}' , [
    'uses' => 'API\tempController@delStoreTemp',
]);
Route::delete('/del-prod-temp/{id}' , [
    'uses' => 'API\tempController@delProdTemp',
]);
Route::post('/submit-store-image' , [
    'uses' => 'API\storeController@submitStImage',
]);
Route::post('/update-store-image' , [
    'uses' => 'API\storeController@updateStoreImage',
]);
Route::post('/supplier-this-supplier' , [
    'uses' => 'API\supplierController@fetchThisSupplier',
]);
Route::post('/filter-sale-record' , [
    'uses' => 'API\saleController@filterSaleRecord',
]);







Route::post('/check-unit' , [
    'uses' => 'API\productController@checkUnit',
]);
Route::post('/reset-temp-img' , [
    'uses' => 'API\tempController@resetTempImage',
]);

Route::post('/get-all-filters' , [
    'uses' => 'API\tagController@getAllFilters',
]);
Route::post('/get-this-filter' , [
    'uses' => 'API\tagController@getThisFilter',
]);
Route::post('/get-this-discount' , [
    'uses' => 'API\discountController@getThisDiscount',
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
