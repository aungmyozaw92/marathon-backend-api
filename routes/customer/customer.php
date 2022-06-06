<?php

Route::post('auth/request_code', 'Auth\AuthController@requestCode');
Route::post('auth/verify_request', 'Auth\AuthController@verifyCode');
// Route::post('auth/login', 'Auth\AuthController@login');

Route::group(['middleware' => ['jwt.verify', 'jwt.verify:customer']], function () {
    Route::get('auth/logout', 'Auth\AuthController@logout');
    Route::get('auth/profile', 'Auth\AuthController@profile');
    Route::post('auth/profile', 'Auth\AuthController@update_profile');
    Route::post('auth/change_password', 'Auth\AuthController@update_password');

    Route::apiResource('vouchers', 'VoucherController');

});
