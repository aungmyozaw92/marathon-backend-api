<?php
Route::post('auth/login', 'Auth\AuthController@login');

Route::group(['middleware' => ['jwt.verify', 'jwt.verify:agent']], function () {
    Route::get('auth/logout', 'Auth\AuthController@logout');
    Route::get('auth/profile', 'Auth\AuthController@profile');
    Route::post('auth/profile', 'Auth\AuthController@update_profile');
    Route::post('upload_profile', 'AttachmentController@store');
    Route::post('check_password', 'Auth\AuthController@check_password');
    Route::get('home', 'HomeController@index');

    Route::get('finance_detail', 'TransactionController@finance_detail');
    Route::get('finish_vouchers', 'VoucherController@finish_vouchers');
    /**
     * Incoming Waybills
     */
    Route::apiResource('waybills', 'WaybillController');

    /**
     * Delivery Voucher
     */
    Route::apiResource('vouchers', 'VoucherController');
    /**
     * Transaction
     */
    Route::apiResource('transactions', 'TransactionController');
    Route::get('commission_history', 'TransactionController@commission_history');

    /**
     * Topup
     */
    Route::post('topup', 'TopupController@store');

    Route::get('/vouchers/{voucher}/messages', 'VoucherMessageController@show');
});
