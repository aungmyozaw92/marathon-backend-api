<?php

Route::post('auth/validate_phone_number', 'Auth\AuthController@validate_phone_number');
Route::post('auth/verifiy_otp_code', 'Auth\AuthController@verifyOtpCode');
Route::post('auth/register', 'MerchantController@register');

Route::post('auth/login', 'Auth\AuthController@login');
Route::post('auth/forget_password_validate', 'Auth\AuthController@forget_password_validate');
Route::post('auth/reset_password', 'Auth\AuthController@reset_password');
// Route::post('auth/save_device_token', 'Auth\AuthController@save_device_token');

Route::post('calculate_amount', 'CalculateAmountController@calculate_price');
Route::get('/incomplete_vouchers/{voucher}', 'IncompleteVoucherController@show');
Route::post('/incomplete_vouchers/{voucher}/update_receiver', 'IncompleteVoucherController@update_receiver');
Route::group(['middleware' => ['jwt.verify', 'jwt.verify:merchant']], function () {

    /**
     * Merchant Auth and  Profile Routes
     */
	Route::get('home', 'HomeController@index');
	Route::get('get_master_records', 'Auth\AuthController@get_master_records');
    Route::post('auth/logout', 'Auth\AuthController@logout');
    Route::get('auth/profile', 'Auth\AuthController@profile');
    Route::post('auth/profile', 'Auth\AuthController@update_profile');
    Route::post('upload_profile', 'AttachmentController@store');
    Route::post('auth/confirm_password', 'Auth\AuthController@confirm_password');
    Route::post('auth/save_device_token', 'Auth\AuthController@save_device_token');
	Route::put('auth/refresh_device_token', 'Auth\AuthController@refresh_device_token');
	Route::post('test_noti', 'Auth\AuthController@test_noti');

    /**
     * Merchant Associate Routes
     */
    Route::get('merchant_associates', 'MerchantAssociateController@index');
    Route::post('merchant_associates', 'MerchantAssociateController@store');
    Route::put('merchant_associates/{merchant_associate}', 'MerchantAssociateController@update');
    Route::delete('merchant_associates/{merchant_associate}', 'MerchantAssociateController@destroy');
    /**
     * Product Routes
     */
    Route::apiResource('products', 'ProductController');
    Route::post('products/{product}/upload', 'ProductController@upload');
    Route::delete('products/{product}/attachments/{attachment}', 'ProductController@delete_file');
    /**
     * ProductType Routes
     */
    Route::apiResource('product_types', 'ProductTypeController');
    /**
     * Tag Routes
     */
    Route::apiResource('tags', 'TagController');
    /**
     * ProductTag Routes
     */
    Route::apiResource('product_tags', 'ProductTagController');
    /**
     * Inventory Routes
     */
    Route::apiResource('inventories', 'InventoryController');
    /**
     * Vouchers Routes
     */
    Route::apiResource('vouchers', 'VoucherController');
	Route::get('draft_vouchers', 'VoucherController@draftVouchers');
	Route::get('delivering_vouchers','VoucherController@deliveringVouchers');
	Route::get('delivered_vouchers', 'VoucherController@deliveringVouchers');
	Route::get('failed_attempt_vouchers', 'VoucherController@failedAttemptVouchers');
	Route::get('solved_failed_attempt_vouchers', 'VoucherController@failedAttemptVouchers');
    Route::put('/vouchers/{voucher}/return_it_back', 'VoucherController@return_it_back');
    Route::put('/vouchers/{voucher}/postpone_it', 'VoucherController@postpone_it');
	/**
	 * IncompleteVoucher Routes
	 */
	Route::get('incomplete_vouchers', 'IncompleteVoucherController@index');
    Route::post('/incomplete_vouchers', 'IncompleteVoucherController@store');
    Route::put('/incomplete_vouchers/{voucher}', 'IncompleteVoucherController@update');
	Route::delete('/incomplete_vouchers/{voucher}', 'IncompleteVoucherController@destroy');
	Route::get('/incomplete_vouchers/{voucher}/redirect', 'IncompleteVoucherController@redirect_from_link');

    /**
     * Pickup Routes
     */
    Route::apiResource('pickups', 'PickupController');
    /**
     * Withdraw Routes
     */
	Route::get('search_transaction', 'MerchantSheetController@search_transaction');
	Route::get('transactions', 'MerchantSheetController@transaction_lists');
	Route::get('transactions/{transaction}', 'MerchantSheetController@transaction_detail');
    Route::post('create_withdraw', 'MerchantSheetController@create_withdraw');
    /**
     * AccountInformation Routes
     */
    Route::apiResource('account_information', 'AccountInformationController');
    /**
     * SetDefault Routes
     */
    Route::put('set_default/{merchant}', 'MerchantController@set_default');
    /**
     * Return sheet Routes
     */
    Route::apiResource('return_sheets', 'ReturnSheetController')->only(['index', 'show']);;
});
