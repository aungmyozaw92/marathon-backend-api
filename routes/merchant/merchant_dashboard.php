<?php

Route::post('auth/login', 'Auth\AuthController@login');

Route::group(['middleware' => ['jwt.verify', 'jwt.verify:merchant']], function () {
    Route::get('auth/logout', 'Auth\AuthController@logout');
    Route::get('auth/profile', 'Auth\AuthController@profile');
    Route::post('auth/profile', 'Auth\AuthController@update_profile');

    Route::post('upload_profile', 'AttachmentController@store');

    Route::post('calculate_amount', 'CalculateAmountController@calculate_price');

    Route::apiResource('cities', 'CityController')->only(['index', 'show']);
    Route::apiResource('products', 'ProductController');
    Route::post('products/{product}/upload', 'ProductController@upload');
    Route::delete('products/{product}/attachments/{attachment}', 'ProductController@delete_file');
    Route::apiResource('product_types', 'ProductTypeController');
    Route::apiResource('zones', 'ZoneController')->only(['index', 'show']);
    Route::apiResource('staffs', 'StaffController')->only(['index', 'show']);
    Route::apiResource('merchants', 'MerchantController')->only(['index', 'show']);
    Route::apiResource('payment_types', 'PaymentTypeController')->only(['index', 'show']);
    Route::apiResource('bus_stations', 'BusStationController')->only(['index', 'show']);
    Route::apiResource('delivery_statuses', 'DeliveryStatusController')->only(['index', 'show']);
    Route::apiResource('metas', 'MetaController')->only(['index', 'show']);
    Route::apiResource('global_scales', 'GlobalScaleController')->only(['index', 'show']);
    Route::apiResource('gates', 'GateController')->only(['index', 'show']);
    Route::apiResource('call_statuses', 'CallStatusController')->only(['index', 'show']);
    Route::apiResource('store_statuses', 'StoreStatusController')->only(['index', 'show']);
    Route::apiResource('log_statuses', 'LogStatusController')->only(['index', 'show']);
    Route::apiResource('failure_statuses', 'FailureStatusController')->only(['index', 'show']);
    Route::apiResource('tracking_statuses', 'TrackingStatusController')->only(['index', 'show']);
    Route::apiResource('delegate_durations', 'DelegateDurationController')->only(['index', 'show']);
    Route::apiResource('banks', 'BankController')->only(['index']);
    Route::apiResource('customers', 'CustomerController');
    Route::get('customers/{customer}/vouchers', 'CustomerController@get_vouchers');
    Route::apiResource('tags', 'TagController');
    Route::apiResource('product_tags', 'ProductTagController');
    Route::apiResource('inventories', 'InventoryController');
    Route::post('inventories/{inventory}/add_qty', 'InventoryController@add_qty');
    Route::apiResource('variation_metas', 'VariationMetaController');
    Route::apiResource('merchant_customers', 'MerchantCustomerController');
    // Route::apiResource('product_variations', 'ProductVariationController')->only(['store', 'destroy']);

    /**
     * Merchant Transaction Histories Lists
     */

    Route::get('transaction_histories', 'MerchantController@transaction_lists');

    Route::apiResource('merchant_associates', 'MerchantAssociateController');

    /**
     * Pickup Routes
     */
    Route::get('/pickups/get_all_by_date', 'PickupController@getAllByDate');
    Route::apiResource('pickups', 'PickupController');
    Route::post('/pickups/{pickup}/closed', 'PickupController@closed');


    /**
     * Voucher Routes
     */
    Route::apiResource('vouchers', 'VoucherController');
    Route::post('import_vouchers', 'VoucherController@importVouchers');
    Route::get('draft_vouchers', 'VoucherController@draftVouchers');
    Route::get('binded_vouchers', 'VoucherController@bindedVouchers');
    Route::get('delivering_vouchers', 'VoucherController@deliveringVouchers');
    Route::get('attempt_vouchers', 'VoucherController@attemptVouchers');
    Route::get('delivered_vouchers', 'VoucherController@deliveredVouchers');
    Route::get('cannot_delivered_vouchers', 'VoucherController@cannotDeliveredVouchers');
    Route::get('pending_return_vouchers', 'VoucherController@pendingReturnVouchers');
    Route::get('returning_vouchers', 'VoucherController@returningVouchers');
    Route::get('returned_vouchers', 'VoucherController@returnedVouchers');
    Route::get('delivered_and_returning_vouchers', 'VoucherController@deliveredReturningVouchers');

    Route::get('/incomplete_vouchers', 'IncompleteVoucherController@index');
    Route::get('/incomplete_vouchers/{voucher}', 'IncompleteVoucherController@show');
    Route::post('/incomplete_vouchers', 'IncompleteVoucherController@store');
    Route::put('/incomplete_vouchers/{voucher}', 'IncompleteVoucherController@update');
    Route::post('/incomplete_vouchers/{voucher}/update_receiver', 'IncompleteVoucherController@update_receiver');

    Route::get('dashboard', 'DashboardController@index');

    //Upload Voucher Image
    Route::post('vouchers/{voucher}/upload', 'VoucherController@upload');

    /**
     * Merchant Sheets
     */
    Route::apiResource('merchant_sheets', 'MerchantSheetController')->only(['index', 'show']);

    /**
     * Return Sheets
     */
    Route::apiResource('return_sheets', 'ReturnSheetController')->only(['index', 'show']);

    /**
     * Account Information
     */
    Route::apiResource('account_informations', 'AccountInformationController');

    /**
     * Merchant Associate
     */
    Route::apiResource('merchant_associates', 'MerchantAssociateController');

    /**
     * Attachments
     */
    Route::apiResource('attachments', 'AttachmentController')->only(['destroy']);

     /**
     * Withdraw Routes
     */
    Route::post('transactions/create_withdraw', 'MerchantSheetController@create_withdraw');

     /**
     * Order
     */
    Route::apiResource('orders', 'OrderController');
    Route::post('orders/{order}/update_status', 'OrderController@update_status');
});
