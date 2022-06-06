<?php

Route::post('auth/login', 'Auth\AuthController@login');

Route::post('calculate_amount', 'CalculateAmountController@calculate_price');

Route::group(['middleware' => ['jwt.verify', 'jwt.verify:merchant']], function () {

    Route::get('get_master_records', 'Auth\AuthController@get_master_records');

    Route::get('auth/logout', 'Auth\AuthController@logout');
    Route::get('auth/profile', 'Auth\AuthController@profile');
    Route::post('auth/profile', 'Auth\AuthController@update_profile');

    Route::post('upload_profile', 'AttachmentController@store');

    /**
     * Merchant Associate Routes
     */
    Route::post('merchant_associates', 'MerchantAssociateController@store');
    Route::put('merchant_associates/{merchant_associate}', 'MerchantAssociateController@update');
    Route::delete('merchant_associates/{merchant_associate}', 'MerchantAssociateController@destroy');

    /**
     * City Routes
     */
    Route::apiResource('cities', 'CityController')->only(['index', 'show']);
    Route::post('city_gate', 'GateController@gate_by_city');

    /**
     * Zone Routes
     */
    Route::apiResource('zones', 'ZoneController')->only(['index', 'show']);

    /**
     * GlobalScale Routes
     */
    Route::apiResource('/global_scales', 'GlobalScaleController')->only(['index', 'show']);

    /**
     * Payment Type Routes
     */
    Route::apiResource('/payment_types', 'PaymentTypeController')->only(['index', 'show']);

    /**
     * Bus Stations Routes
     */
    Route::apiResource('/bus_stations', 'BusStationController')->only(['index', 'show']);

    /**
     * Gates Routes
     */
    Route::apiResource('/gates', 'GateController')->only(['index', 'show']);

    /**
     * Stores Routes
     */
    Route::apiResource('stores', 'StoreController');

    Route::apiResource('products', 'ProductController');
    Route::post('products/{product}/upload', 'ProductController@upload');
    Route::delete('products/{product}/attachments/{attachment}', 'ProductController@delete_file');
    Route::apiResource('product_types', 'ProductTypeController');

    Route::apiResource('tags', 'TagController');
    Route::apiResource('product_tags', 'ProductTagController');
    Route::apiResource('inventories', 'InventoryController');
    Route::post('inventories/{inventory}/add_qty', 'InventoryController@add_qty');
    Route::apiResource('variation_metas', 'VariationMetaController');
    // Route::apiResource('product_variations', 'ProductVariationController')->only(['store', 'destroy']);

    /**
     * Vouchers Routes
     */
    Route::apiResource('vouchers', 'VoucherController');
    Route::get('vouchers/pickup/null', 'VoucherController@pickupNullVouchers');
    Route::get('draft_vouchers', 'VoucherController@draftVouchers');
    Route::get('binded_vouchers', 'VoucherController@bindedVouchers');
    /**
     * Vouchers Search with Customer name and Phone no
     */
    Route::get('search_voucher', 'VoucherController@search_voucher');

    Route::get('vouchers_filter', 'VoucherController@filter');

    Route::post('/vouchers/{voucher}/bind_qr', ['uses' => 'VoucherController@bindQR']);
    Route::post('/vouchers/{voucher}/unbind_qr', ['uses' => 'VoucherController@unBindQR']);

    Route::post('/check_qr', 'QrController@check_qr_code');
    /**
     * Pickup Routes
     */
    Route::apiResource('pickups', 'PickupController');
    /**
     * TrackingStatus Routes
     */
    Route::apiResource('tracking_status', 'TrackingStatusController');

    Route::apiResource('merchant_sheets', 'MerchantSheetController');

    Route::get('transactions', 'MerchantSheetController@transaction_lists');
    Route::post('create_withdraw', 'MerchantSheetController@create_withdraw');

    Route::get('search_transaction', 'MerchantSheetController@search_transaction');
});
