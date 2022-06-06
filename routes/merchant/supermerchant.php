<?php

Route::post('auth/login', 'Auth\AuthController@login');

Route::group(['middleware' => ['jwt.verify', 'jwt.verify:merchant']], function () {
    // Route::get('auth/logout', 'Auth\AuthController@logout');
    Route::get('auth/profile', 'Auth\AuthController@profile');
    Route::post('calculate_amount', 'HomeController@calculate_price');
    Route::post('calculate_amount_detail', 'HomeController@calculate_price_detail');
    Route::post('check_route', 'HomeController@check_route');
    Route::get('get_data', 'HomeController@get_master_records');

    Route::apiResource('merchants', 'MerchantController');
    Route::post('merchants/{merchant}/merchant_associates', 'MerchantAssociateController@store');
    Route::put('merchants/{merchant}/merchant_associates/{merchant_associate}', 'MerchantAssociateController@update');
    Route::delete('merchants/{merchant}/merchant_associates/{merchant_associate}', 'MerchantAssociateController@destroy');
    // Route::apiResource('zones', 'ZoneController')->only(['index', 'show']);
    // Route::apiResource('staffs', 'StaffController')->only(['index', 'show']);
    // Route::apiResource('merchants', 'MerchantController')->only(['index', 'show']);
    // Route::apiResource('payment_types', 'PaymentTypeController')->only(['index', 'show']);
    // Route::apiResource('bus_stations', 'BusStationController')->only(['index', 'show']);
    // Route::apiResource('delivery_statuses', 'DeliveryStatusController')->only(['index', 'show']);
    // Route::apiResource('metas', 'MetaController')->only(['index', 'show']);
    // Route::apiResource('global_scales', 'GlobalScaleController')->only(['index', 'show']);
    // Route::apiResource('gates', 'GateController')->only(['index', 'show']);
    // Route::apiResource('call_statuses', 'CallStatusController')->only(['index', 'show']);
    // Route::apiResource('store_statuses', 'StoreStatusController')->only(['index', 'show']);
    // Route::apiResource('log_statuses', 'LogStatusController')->only(['index', 'show']);
    // Route::apiResource('failure_statuses', 'FailureStatusController')->only(['index', 'show']);
    // Route::apiResource('tracking_statuses', 'TrackingStatusController')->only(['index', 'show']);
    // Route::apiResource('delegate_durations', 'DelegateDurationController')->only(['index', 'show']);

    /**
     * Voucher Routes
     */
    Route::apiResource('vouchers', 'VoucherController')->only(['index', 'store', 'show']);
    
    Route::apiResource('pickups', 'PickupController')->only(['index', 'store', 'show']);
    Route::post('pickups/{pickup}/add_vouchers', 'PickupController@add_voucher');
    Route::post('pickups/{pickup}/remove_vouchers', 'PickupController@remove_voucher');
    Route::get('vouchers/{voucher}/tracking_status', 'VoucherController@voucher_trackings');
});
