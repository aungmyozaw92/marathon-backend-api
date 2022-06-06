<?php
Route::post('send_message', 'NotificationController@sendMessage');
Route::post('auth/login', 'Auth\AuthController@login');

Route::post('calculate_amount', 'CalculateAmountController@calculate_price');

Route::group(['middleware' => ['jwt.verify', 'jwt.verify:delivery']], function () {
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

    //Pickup
    Route::apiResource('pickups', 'PickupController');
    Route::post('/pickups/{pickup}/upload', 'PickupController@upload');

    /* //qr_scan */
    Route::post('scan_qr', 'QrController@scan_qr');

    /* //Voucher */
    Route::apiResource('vouchers', 'VoucherController');
    Route::post('/vouchers/{voucher}/update_status', 'VoucherController@update_status');
    Route::get('/scan_voucher/{voucher_no}', 'VoucherController@scan_voucher');

    /**
     * Delisheets
     */
    Route::get('deli_sheet_vouchers', 'DeliSheetVoucherController@index');
    Route::get('deli_sheets', 'DeliSheetController@delivery');

    /**
     * waybills
     */
    Route::apiResource('/waybills', 'WaybillController')->only(['show']);
    Route::post('waybills/file_upload', 'WaybillController@upload');

    /**
     * Delivered Vouchers
     */

    Route::get('delivery_vouchers', 'DeliveryVoucherController@getDeliveryVouchers');

    Route::get('cant_deliver_vouchers', 'DeliveryVoucherController@getCantDeliveryVouchers');

    /**
     * Return Sheets
     */
    Route::apiResource('/return_sheets', 'ReturnSheetController')->only(['show']);
    Route::post('return_sheets/file_upload', 'ReturnSheetController@upload');

    //Get Voucher Histories
    Route::get('voucher_histories', 'DeliveryVoucherController@getVoucherHistory');
    //Get Pickup Histories
    Route::get('pickup_histories', 'PickupController@getPickupHistory');
    //Get Waybill Histories
    Route::get('waybill_histories', 'WaybillController@getWaybillHistory');
    //Get Return Histories
    Route::get('return_histories', 'ReturnSheetController@getReturnSheetHistory');

    //Finance Voucher
    Route::get('finance_vouchers', 'FinanceVoucherController@getVouchers');
    //Finance Pickup
    Route::get('finance_pickups', 'FinanceVoucherController@getPickups');

    //Upload attachment
    //Upload attendance
    Route::apiResource('/attachments', 'AttachmentController');
    Route::post('file_upload', 'AttachmentController@upload');

    //Upload attendance
    Route::apiResource('/attendances', 'AttendanceController');


    // Failure Statuses
    Route::get('failure_statuses', 'FailureStatusController@index');

    Route::get('hero_badges', 'HeroBadgeController@index');

    Route::get('point_logs', 'PointLogController@index');

    Route::apiResource('commission_logs', 'CommissionLogController')->only(['index', 'show']);

    Route::apiResource('metas', 'MetaController')->only(['index']);
});
