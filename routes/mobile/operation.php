<?php
Route::post('auth/login', 'Auth\AuthController@login');

Route::group(['middleware' => ['jwt.verify', 'jwt.verify:operation']], function () {
    Route::get('auth/logout', 'Auth\AuthController@logout');
    Route::get('auth/profile', 'Auth\AuthController@profile');
    Route::post('auth/profile', 'Auth\AuthController@update_profile');

    /**
     * City Routes
     */
    Route::apiResource('cities', 'CityController');
    /**
     * Zone Routes
     */
    Route::apiResource('zones', 'ZoneController');
    /**
     * Bus Stations Routes
     */
    Route::apiResource('/bus_stations', 'BusStationController')->only(['index', 'show']);

    /**
     * Gate Routes
     */
    Route::apiResource('/gates', 'GateController')->only(['index', 'show']);

    /**
     * Gloabal Scale Routes
     */
    Route::apiResource('/global_scales', 'GlobalScaleController')->only(['index', 'show']);
    /**
     * Payment Types Routes
     */
    Route::apiResource('/payment_types', 'PaymentTypeController')->only(['index', 'show']);

    /**
     * merchants Routes
     */
    Route::apiResource('/merchants', 'MerchantController')->only(['index', 'show']);

    /**
     * Deliveries Routes
     */
    Route::apiResource('/deliveries', 'DeliveryController')->only(['index', 'show']);

    /**
     * Pickups Routes
     */
    Route::apiResource('/pickups', 'PickupController')->only(['index', 'show', 'store']);
    Route::post('/pickups/{pickup}/closed', 'PickupController@closed');


    /**
     * Vouchers Routes
     */
    Route::post('/vouchers/{voucher}/change_store_status', 'VoucherController@change_store_status');
    Route::apiResource('/vouchers', 'VoucherController');

    /**
     * Update Fee for pickups
     */
    Route::post('/pickups/{pickup}/update_pickup_fee', 'PickupController@update_pickup_fee');

    /**
     * Delisheets
     */
    Route::apiResource('deli_sheets', 'DeliSheetController')->only(['index', 'show', 'store']);
    Route::post('/deli_sheets/{deli_sheet}/assign_voucher', 'DeliSheetController@assign_voucher');
    Route::post('deli_sheets/{deli_sheet}/remove_vouchers', 'DeliSheetController@removeVouchers');
    Route::post('deli_sheets/{deli_sheet}/confirm_scan', 'DeliSheetController@confirm_scan');
    /**
     * Waybill
     */
    Route::apiResource('waybills', 'WaybillController')->only(['index', 'show']);
    Route::post('/waybills/{waybill}/assign_voucher', 'WaybillController@assign_voucher');
    Route::post('waybills/{waybill}/remove_vouchers', 'WaybillController@removeVouchers');
    Route::post('waybills/{waybill}/confirm_scan', 'WaybillController@confirm_scan');

    Route::post('scan_remove_voucher', 'VoucherController@scan_remove_voucher');
});
