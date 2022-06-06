<?php

Route::post('auth/login', 'Auth\AuthController@login');

Route::group(['middleware' => ['jwt.verify', 'jwt.verify:delivery']], function () {
    Route::get('auth/logout', 'Auth\AuthController@logout');
    Route::get('auth/profile', 'Auth\AuthController@profile');
    Route::post('auth/profile', 'Auth\AuthController@update_profile');
    Route::post('auth/change_password', 'Auth\AuthController@update_password');

    // Route::get('get_data', 'HomeController@get_master_records');
    Route::apiResource('pickups', 'PickupController');
    Route::apiResource('deli_sheets', 'DeliSheetController');
});
