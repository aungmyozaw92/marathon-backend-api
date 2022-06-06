<?php
Route::post('v1/auth/phone_login', 'Web\Api\v1\Auth\CustomerAuthController@phone_login');
// Route::post('pin_request', 'DashboardController@pinRequest');
// Route::post('pin_verify', 'DashboardController@pinVerify');

Route::group(['namespace' => 'Web\Api\v1', 'middleware' => 'jwt.verify:customer'], function () {
    Route::get('v1/customer/profile', 'Auth\CustomerAuthController@profile');
    Route::get('v1/customer/logout', 'Auth\AuthController@logout');
});


Route::post('v1/auth/login', 'Web\Api\v1\Auth\AuthController@login');
Route::get('export_vouchers', 'Web\Api\v1\ExportController@exportVouchers');
Route::post('v1/uab/response_payment', 'Web\Api\v1\BankApiController@ResponsePaymentAPI');

Route::group(['namespace' => 'Web\Api\v1', 'middleware' => 'jwt.verify', 'prefix' => 'v1'], function () {
    Route::get('get_master_records', 'HomeController@GetAllMasterRecords');
    Route::get('auth/logout', 'Auth\AuthController@logout');
    Route::get('auth/profile', 'Auth\AuthController@profile');
    Route::post('auth/profile', 'Auth\AuthController@update_profile');
    Route::post('profile/upload', 'Auth\AuthController@uploadProfile');
    Route::get('auth/clear_tokens', 'Auth\AuthController@clearAllTokens');
    Route::post('check_password', 'Auth\AuthController@check_password');

    /**
     * Check Api Valid
     */
    Route::get('auth/check_valid_token', ['uses' => 'Auth\AuthController@check', 'middleware' => 'roles', 'roles' => ['Admin', 'Finance', 'CustomerService', 'CS', 'Operation', 'OS', 'HQ']]);

    Route::get('dashboard', 'DashboardController@index');
    Route::get('cs_dashboard', 'DashboardController@cs_dashboard');

    /**
     * Notification for Pending Return and Postpone Vouchers
     */
    Route::get('return_and_postpone_vouchers_count', 'DashboardController@return_and_postpone_vouchers');

    Route::post('pin_request', 'DashboardController@pinRequest');
    Route::post('pin_verify', 'DashboardController@pinVerify');


    /**
     * Pickups
     */
    Route::get('/pickups/get_all_by_date', ['uses' => 'PickupController@getAllByDate', 'middleware' => 'roles', 'roles' => ['Admin', 'Finance', 'CustomerService', 'CS', 'Operation', 'OS', 'HQ', 'Agent']]);

    Route::get('/pickups', ['uses' => 'PickupController@index', 'middleware' => 'roles', 'roles' => ['Admin', 'Finance', 'CustomerService', 'CS', 'Operation', 'OS', 'HQ', 'Agent']]);
    Route::post('/pickups', ['uses' => 'PickupController@store', 'middleware' => 'roles', 'roles' => ['Admin', 'Finance', 'CustomerService', 'CS', 'Operation', 'OS', 'HQ', 'Agent']]);
    Route::delete('/pickups/{pickup}', ['uses' => 'PickupController@destroy', 'middleware' => 'roles', 'roles' => ['Admin', 'Operation', 'Agent']]);
    Route::put('/pickups/{pickup}', ['uses' => 'PickupController@update', 'middleware' => 'roles', 'roles' => ['Admin', 'Finance', 'CustomerService', 'CS', 'Operation', 'OS', 'HQ', 'Agent']]);
    Route::get('/pickups/{pickup}', ['uses' => 'PickupController@show', 'middleware' => 'roles', 'roles' => ['Admin', 'Finance', 'CustomerService', 'CS', 'Operation', 'OS', 'HQ', 'Agent']]);

    Route::post('/pickups/assign_pickup', ['uses' => 'PickupController@assign_pickup', 'middleware' => 'roles', 'roles' => ['Admin', 'Finance', 'CustomerService', 'CS', 'Operation', 'OS', 'HQ', 'Agent']]);
    Route::post('/pickups/{pickup}/closed', ['uses' => 'PickupController@closed', 'middleware' => 'roles', 'roles' => ['Admin', 'Finance', 'CustomerService', 'CS', 'Operation', 'OS', 'HQ', 'Agent']]);
    Route::post('/pickups/{pickup}/update_store_status', ['uses' => 'PickupController@update_store_status', 'middleware' => 'roles', 'roles' => ['Admin', 'Finance', 'CustomerService', 'CS', 'Operation', 'OS', 'HQ', 'Agent']]);
    Route::post('/pickups/{pickup}/update_undo_store_status', ['uses' => 'PickupController@update_undo_store_status', 'middleware' => 'roles', 'roles' => ['Admin', 'Finance', 'CustomerService', 'CS', 'Operation', 'OS', 'HQ', 'Agent']]);
    //Upload Pickup Image
    Route::post('pickups/{pickup}/upload', ['uses' => 'PickupController@upload', 'middleware' => 'roles', 'roles' => ['Admin', 'Finance', 'CustomerService', 'CS', 'Operation', 'OS', 'HQ']]);
    // update hero
    Route::post('pickups/{id}/change_hero', ['uses' => 'PickupController@change_hero', 'middleware' => 'roles', 'roles' => ['Admin', 'Finance', 'CustomerService', 'CS', 'Operation', 'OS', 'HQ']]);

    /**
     * History for pickups
     */
    Route::get('histories/pickups/{pickup}', ['uses' => 'PickupHistoryController@index', 'middleware' => 'roles', 'roles' => ['Admin', 'Finance', 'CustomerService', 'CS', 'Operation', 'OS', 'Agent']]);
    Route::get('histories/delisheets/{deli_sheet}', ['uses' => 'DeliSheetHistoryController@index', 'middleware' => 'roles', 'roles' => ['Admin', 'Finance', 'CustomerService', 'CS', 'Operation', 'OS', 'Agent']]);
    Route::get('histories/waybills/{waybill}', ['uses' => 'WaybillHistoryController@index', 'middleware' => 'roles', 'roles' => ['Admin', 'Finance', 'CustomerService', 'CS', 'Operation', 'OS', 'Agent']]);
    Route::get('histories/merchant_sheets/{merchant_sheet}', ['uses' => 'MerchantSheetHistoryController@index', 'middleware' => 'roles', 'roles' => ['Admin', 'Finance', 'CustomerService', 'CS', 'Operation', 'OS', 'Agent']]);
    Route::get('histories/return_sheets/{return_sheet}', ['uses' => 'ReturnSheetHistoryController@index', 'middleware' => 'roles', 'roles' => ['Admin', 'Finance', 'CustomerService', 'CS', 'Operation', 'OS', 'Agent']]);

    Route::post('export_pickup/{pickup}', 'ExportHistoryController@export_sheet');
    Route::post('export_delisheet/{delisheet}', 'ExportHistoryController@export_sheet');
    Route::post('export_waybill/{waybill}', 'ExportHistoryController@export_sheet');
    Route::post('export_merchantsheet/{merchantsheet}', 'ExportHistoryController@export_sheet');
    Route::post('print_pickup/{pickup}', 'ExportHistoryController@export_sheet');
    Route::post('print_voucher/{voucher}', 'ExportHistoryController@export_sheet');
    Route::post('print_delisheet/{delisheet}', 'ExportHistoryController@export_sheet');
    Route::post('print_waybill/{waybill}', 'ExportHistoryController@export_sheet');
    Route::post('print_merchantsheet/{merchantsheet}', 'ExportHistoryController@export_sheet');

    Route::post('sheet_event/{delisheet}', 'ExportHistoryController@sheet_event');
    /**
     * Update Fee for pickups
     */
    Route::post('/pickups/{pickup}/update_pickup_fee', ['uses' => 'PickupController@update_pickup_fee', 'middleware' => 'roles', 'roles' => ['Admin', 'Finance', 'CustomerService', 'CS', 'Operation', 'OS', 'HQ', 'Agent']]);
    Route::post('/pickups/{pickup}/update_requested_date', ['uses' => 'PickupController@update_requested_date', 'middleware' => 'roles', 'roles' => ['Admin', 'Finance', 'Operation', 'OS', 'HQ', 'Agent']]);

    // Route::get('merchant_search', 'MerchantController@search');
    // Route::get('merchants', 'MerchantController@index');

    /**
     * Vouchers
     */
    Route::get('/vouchers', ['uses' => 'VoucherController@index', 'middleware' => 'roles', 'roles' => ['Admin', 'Finance', 'CustomerService', 'CS', 'Operation', 'OS', 'HQ', 'Agent']]);
    Route::post('/vouchers', ['uses' => 'VoucherController@store', 'middleware' => 'roles', 'roles' => ['Admin', 'Finance', 'CustomerService', 'CS', 'Operation', 'OS', 'HQ', 'Agent']]);
    Route::delete('/vouchers/{voucher}', ['uses' => 'VoucherController@destroy', 'middleware' => 'roles', 'roles' => ['Admin', 'HQ', 'Agent']]);

    Route::put('/vouchers/{voucher}', ['uses' => 'VoucherController@update', 'middleware' => 'roles', 'roles' => ['Admin', 'Finance', 'CustomerService', 'CS', 'Operation', 'OS', 'HQ', 'Agent']]);
    Route::get('/vouchers/{voucher}', ['uses' => 'VoucherController@show', 'middleware' => 'roles', 'roles' => ['Admin', 'Finance', 'CustomerService', 'CS', 'Operation', 'OS', 'HQ', 'Agent']]);
    Route::post('/vouchers/{voucher}/closed', ['uses' => 'VoucherController@closed', 'middleware' => 'roles', 'roles' => ['Admin', 'Finance', 'CustomerService', 'CS', 'Operation', 'OS', 'HQ', 'Agent']]);
    Route::post('/vouchers/closed', ['uses' => 'VoucherController@closedVouchers', 'middleware' => 'roles', 'roles' => ['Admin', 'Finance', 'CustomerService', 'CS', 'Operation', 'OS', 'HQ', 'Agent']]);
    Route::post('/vouchers/{voucher}/update_status', ['uses' => 'VoucherController@update_status', 'middleware' => 'roles', 'roles' => ['Admin', 'Finance', 'CustomerService', 'CS', 'Operation', 'OS', 'HQ', 'Agent']]);
    Route::post('/vouchers/{voucher}/return', ['uses' => 'VoucherController@return', 'middleware' => 'roles', 'roles' => ['Admin', 'Finance', 'CustomerService', 'CS', 'Operation', 'OS', 'HQ', 'Agent']]);
    Route::post('/vouchers/{voucher}/undo_return', ['uses' => 'VoucherController@undo_return', 'middleware' => 'roles', 'roles' => ['Admin', 'Finance', 'CustomerService', 'CS', 'Operation', 'OS', 'HQ', '']]);
    Route::post('/vouchers/attachments/{attachment}/show_merchant', ['uses' => 'VoucherController@show_merchant_attachment', 'middleware' => 'roles', 'roles' => ['Admin', 'Finance', 'CustomerService', 'CS', 'Operation', 'OS', 'HQ', 'Agent']]);
    Route::post('/vouchers/attachments/{attachment}/unshow_merchant', ['uses' => 'VoucherController@unshow_merchant_attachment', 'middleware' => 'roles', 'roles' => ['Admin', 'Finance', 'CustomerService', 'CS', 'Operation', 'OS', 'HQ', 'Agent']]);
    Route::post('/vouchers/{voucher}/update_waybill_voucher', ['uses' => 'VoucherController@updateWaybillVoucher', 'middleware' => 'roles', 'roles' => ['Admin', 'Finance', 'CustomerService', 'CS', 'Operation', 'OS', 'HQ', 'Agent']]);

    Route::post('/vouchers/{voucher}/messages', ['uses' => 'VoucherMessageController@store', 'middleware' => 'roles', 'roles' => ['Admin', 'Finance', 'CustomerService', 'CS', 'Operation', 'OS', 'HQ', 'Agent']]);
    Route::get('/vouchers/{voucher}/messages', ['uses' => 'VoucherMessageController@show', 'middleware' => 'roles', 'roles' => ['Admin', 'Finance', 'CustomerService', 'CS', 'Operation', 'OS', 'HQ', 'Agent']]);

    // tracking in voucher detail
    Route::get('/vouchers/{voucher}/tracking', ['uses' => 'VoucherController@tracking_voucher', 'middleware' => 'roles', 'roles' => ['Admin', 'Finance', 'CustomerService', 'CS', 'Operation', 'OS', 'HQ', 'Agent']]);

    //Upload Voucher Image
    Route::post('vouchers/{voucher}/upload', ['uses' => 'VoucherController@upload', 'middleware' => 'roles', 'roles' => ['Admin', 'Finance', 'CustomerService', 'CS', 'Operation', 'OS', 'HQ', 'Agent']]);

    Route::post('/import_vouchers', ['uses' => 'VoucherController@importVouchers', 'middleware' => 'roles', 'roles' => ['Admin', 'HQ', 'Agent', 'Operation']]);
    Route::post('/manual_close_vouchers', ['uses' => 'VoucherController@manual_closed', 'middleware' => 'roles', 'roles' => ['Admin', 'HQ', 'Agent', 'Operation']]);
    
    Route::post('/voucher_receive', ['uses' => 'VoucherController@scanVoucherReceive', 'middleware' => 'roles', 'roles' => ['Admin', 'Finance', 'CustomerService', 'CS', 'Operation', 'OS', 'HQ', 'Agent']]);
    // Route::get('export_vouchers', 'ExportController@exportVouchers');

    Route::get('/draft_vouchers', ['uses' => 'VoucherController@draft_vouchers', 'middleware' => 'roles', 'roles' => ['Admin', 'HQ', 'Agent', 'Operation']]);
    Route::get('/draft_vouchers/{voucher}', ['uses' => 'VoucherController@draft_voucher_detail', 'middleware' => 'roles', 'roles' => ['Admin', 'HQ', 'Agent', 'Operation']]);
    


    Route::get('deli_sheet_vouchers', ['uses' => 'DeliSheetVoucherController@index', 'middleware' => 'roles', 'roles' => ['Admin', 'Finance', 'CustomerService', 'CS', 'Operation', 'OS', 'HQ', 'Agent']]);
    Route::get('express_deli_sheet_vouchers', ['uses' => 'ExpressDeliSheetVoucherController@index', 'middleware' => 'roles', 'roles' => ['Admin', 'Finance', 'CustomerService', 'CS', 'Operation', 'OS', 'HQ', 'Agent']]);
    Route::get('way_bill_vouchers', ['uses' => 'WayBillVoucherController@index', 'middleware' => 'roles', 'roles' => ['Admin', 'Finance', 'CustomerService', 'CS', 'Operation', 'OS', 'HQ', 'Agent']]);
    Route::get('bus_sheet_vouchers', ['uses' => 'BusSheetVoucherController@index', 'middleware' => 'roles', 'roles' => ['Admin', 'Finance', 'CustomerService', 'CS', 'Operation', 'OS', 'HQ', 'Agent']]);
    Route::get('merchant_sheet_vouchers', ['uses' => 'MerchantSheetVoucherController@index', 'middleware' => 'roles', 'roles' => ['Admin', 'Finance', 'CustomerService', 'CS', 'Operation', 'OS', 'HQ', 'Agent']]);
    Route::get('branch_sheet_vouchers', ['uses' => 'BranchSheetVoucherController@index', 'middleware' => 'roles', 'roles' => ['Admin', 'Finance', 'CustomerService', 'CS', 'Operation', 'OS', 'HQ', 'Agent']]);
    Route::get('return_sheet_vouchers', ['uses' => 'ReturnSheetVoucherController@index', 'middleware' => 'roles', 'roles' => ['Admin', 'Finance', 'CustomerService', 'CS', 'Operation', 'OS', 'HQ', 'Agent']]);

    /**
     * History for vouchers
     */
    Route::get('histories/vouchers/{voucher}', ['uses' => 'VoucherHistoryController@index', 'middleware' => 'roles', 'roles' => ['Admin', 'Finance', 'CustomerService', 'CS', 'Operation', 'OS', 'HQ', 'Agent']]);

    /**
     * QR
     */
    // Route::apiResource('qrs', 'QrController');
    Route::get('/qrs', ['uses' => 'QrController@index', 'middleware' => 'roles', 'roles' => ['Admin', 'Finance', 'CustomerService', 'CS', 'Operation', 'OS', 'HQ']]);
    Route::post('/qrs', ['uses' => 'QrController@store', 'middleware' => 'roles', 'roles' => ['Admin', 'Finance', 'CustomerService', 'CS', 'Operation', 'OS', 'HQ']]);
    Route::delete('/qrs/{qr}', ['uses' => 'QrController@destroy', 'middleware' => 'roles', 'roles' => ['Admin', 'HQ', 'Operation']]);
    Route::put('/qrs/{qr}', ['uses' => 'QrController@update', 'middleware' => 'roles', 'roles' => ['Admin', 'Finance', 'CustomerService', 'CS', 'Operation', 'OS', 'HQ']]);
    Route::get('/qrs/{qr}', ['uses' => 'QrController@show', 'middleware' => 'roles', 'roles' => ['Admin', 'Finance', 'CustomerService', 'CS', 'Operation', 'OS', 'HQ']]);

    /**
     * Attendace For staff
     */
    Route::post('generate_attendance_code', ['uses' => 'AttendanceController@generateAttendanceCode', 'middleware' => 'roles', 'roles' => ['Admin', 'Finance', 'CustomerService', 'CS', 'Operation', 'OS', 'HQ']]);
    //Route::apiResource('attendances', 'AttendanceController')->only(['index', 'show']);
    Route::get('/attendances', ['uses' => 'AttendanceController@index', 'middleware' => 'roles', 'roles' => ['Admin', 'Finance', 'CustomerService', 'CS', 'Operation', 'OS', 'HQ']]);
    Route::get('/attendances/{attendance}', ['uses' => 'AttendanceController@show', 'middleware' => 'roles', 'roles' => ['Admin', 'Finance', 'CustomerService', 'CS', 'Operation', 'OS', 'HQ']]);

    /**
     * DeliSheet
     */
    Route::get('deli_sheets/delivery_pickups', ['uses' => 'DeliSheetController@deliveryPickups', 'middleware' => 'roles', 'roles' => ['Admin', 'Finance', 'CustomerService', 'CS', 'Operation', 'OS', 'HQ', 'Agent']]);
    Route::post('deli_sheets/change_delivery', ['uses' => 'DeliSheetController@change_delivery', 'middleware' => 'roles', 'roles' => ['Admin', 'Finance', 'CustomerService', 'CS', 'Operation', 'OS', 'HQ', 'Agent']]);
    Route::get('deli_sheets/delivery/{delivery}', ['uses' => 'DeliSheetController@delivery', 'middleware' => 'roles', 'roles' => ['Admin', 'Finance', 'CustomerService', 'CS', 'Operation', 'OS', 'HQ', 'Agent']]);
    Route::post('deli_sheets/{deli_sheet}/remove_vouchers', ['uses' => 'DeliSheetController@removeVouchers', 'middleware' => 'roles', 'roles' => ['Admin', 'Finance', 'CustomerService', 'CS', 'Operation', 'OS', 'HQ', 'Agent']]);
    Route::post('deli_sheets/{deli_sheet}/add_vouchers', ['uses' => 'DeliSheetController@addVouchers', 'middleware' => 'roles', 'roles' => ['Admin', 'Finance', 'CustomerService', 'CS', 'Operation', 'OS', 'HQ', 'Agent']]);
    Route::post('deli_sheets/{deli_sheet}/add_scan_voucher', ['uses' => 'DeliSheetController@addScanVouchers', 'middleware' => 'roles', 'roles' => ['Admin', 'Finance', 'CustomerService', 'CS', 'Operation', 'OS', 'HQ', 'Agent']]);
    Route::post('deli_sheets/{deli_sheet}/add_express_scan_voucher', ['uses' => 'DeliSheetController@addExpressScanVouchers', 'middleware' => 'roles', 'roles' => ['Admin', 'Finance', 'CustomerService', 'CS', 'Operation', 'OS', 'HQ', 'Agent']]);
    // Route::apiResource('deli_sheets', ['uses' => 'DeliSheetController', 'middleware' => 'roles', 'roles' => ['Admin','Finance','CustomerService','CS','Operation','OS', 'HQ']]);

    Route::get('/deli_sheets', ['uses' => 'DeliSheetController@index', 'middleware' => 'roles', 'roles' => ['Admin', 'Finance', 'CustomerService', 'CS', 'Operation', 'OS', 'HQ', 'Agent']]);
    Route::post('/deli_sheets', ['uses' => 'DeliSheetController@store', 'middleware' => 'roles', 'roles' => ['Admin', 'Finance', 'CustomerService', 'CS', 'Operation', 'OS', 'HQ', 'Agent']]);
    Route::delete('/deli_sheets/{deli_sheet}', ['uses' => 'DeliSheetController@destroy', 'middleware' => 'roles', 'roles' => ['Admin']]);
    Route::put('/deli_sheets/{deli_sheet}', ['uses' => 'DeliSheetController@update', 'middleware' => 'roles', 'roles' => ['Admin', 'Finance', 'CustomerService', 'CS', 'Operation', 'OS', 'HQ', 'Agent']]);
    Route::get('/deli_sheets/{deli_sheet}', ['uses' => 'DeliSheetController@show', 'middleware' => 'roles', 'roles' => ['Admin', 'Finance', 'CustomerService', 'CS', 'Operation', 'OS', 'HQ', 'Agent']]);
    Route::post('/deli_sheets/{deli_sheet}/upload', ['uses' => 'DeliSheetController@upload', 'middleware' => 'roles', 'roles' => ['Admin', 'Finance', 'CustomerService', 'CS', 'Operation', 'OS', 'HQ', 'Agent']]);
   //Delisheet transaction token for receive payment
    Route::post('/deli_sheets/{deli_sheet}/generate_token', ['uses' => 'DeliSheetController@generateToken', 'middleware' => 'roles', 'roles' => ['Admin', 'Finance', 'CustomerService', 'CS', 'Operation', 'OS', 'HQ', 'Agent']]);

    /**
     * Waybill
     */
    // Route::apiResource('waybills', 'WaybillController');

    Route::get('/waybills', ['uses' => 'WaybillController@index', 'middleware' => 'roles', 'roles' => ['Admin', 'Finance', 'CustomerService', 'CS', 'Operation', 'OS', 'HQ', 'Agent']]);
    Route::get('/incoming_waybills', ['uses' => 'WaybillController@incomingWaybills', 'middleware' => 'roles', 'roles' => ['Admin', 'Finance', 'CustomerService', 'CS', 'Operation', 'OS', 'HQ', 'Agent']]);
    Route::get('/outgoing_waybills', ['uses' => 'WaybillController@outgoingWaybills', 'middleware' => 'roles', 'roles' => ['Admin', 'Finance', 'CustomerService', 'CS', 'Operation', 'OS', 'HQ', 'Agent']]);
    Route::get('/all_agent_waybills', ['uses' => 'WaybillController@allAgentWaybills', 'middleware' => 'roles', 'roles' => ['Admin', 'Finance', 'CustomerService', 'CS', 'Operation', 'OS', 'HQ', 'Agent']]);
    Route::post('/waybills', ['uses' => 'WaybillController@store', 'middleware' => 'roles', 'roles' => ['Admin', 'Finance', 'CustomerService', 'CS', 'Operation', 'OS', 'HQ', 'Agent']]);
    Route::delete('/waybills/{waybill}', ['uses' => 'WaybillController@destroy', 'middleware' => 'roles', 'roles' => ['Admin']]);
    Route::put('/waybills/{waybill}', ['uses' => 'WaybillController@update', 'middleware' => 'roles', 'roles' => ['Admin', 'Finance', 'CustomerService', 'CS', 'Operation', 'OS', 'HQ', 'Agent']]);
    Route::get('/waybills/{waybill}', ['uses' => 'WaybillController@show', 'middleware' => 'roles', 'roles' => ['Admin', 'Finance', 'CustomerService', 'CS', 'Operation', 'OS', 'HQ', 'Agent']]);

    Route::post('/waybills/{waybill}/agent_confirm', ['uses' => 'WaybillController@agent_confirm', 'middleware' => 'roles', 'roles' => ['Admin', 'Finance', 'CustomerService', 'CS', 'Operation', 'OS', 'HQ', 'Agent']]);
    Route::post('waybills/{waybill}/remove_vouchers', ['uses' => 'WaybillController@removeVouchers', 'middleware' => 'roles', 'roles' => ['Admin', 'Finance', 'CustomerService', 'CS', 'Operation', 'OS', 'HQ', 'Agent']]);
    Route::post('waybills/{waybill}/add_vouchers', ['uses' => 'WaybillController@addVouchers', 'middleware' => 'roles', 'roles' => ['Admin', 'Finance', 'CustomerService', 'CS', 'Operation', 'OS', 'HQ', 'Agent']]);
    Route::post('waybills/{waybill}/received', ['uses' => 'WaybillController@receivedWaybill', 'middleware' => 'roles', 'roles' => ['Admin', 'Finance', 'CustomerService', 'CS', 'Operation', 'OS', 'HQ', 'Agent']]);
    Route::post('waybills/{waybill}/confirm', ['uses' => 'WaybillController@confirm_waybill', 'middleware' => 'roles', 'roles' => ['Admin', 'Finance', 'CustomerService', 'CS', 'Operation', 'OS', 'HQ', 'Agent']]);

    Route::post('waybills/{waybill}/add_scan_voucher', ['uses' => 'WaybillController@addScanVouchers', 'middleware' => 'roles', 'roles' => ['Admin', 'Finance', 'CustomerService', 'CS', 'Operation', 'OS', 'HQ', 'Agent']]);
    Route::post('waybills/{waybill}/add_express_scan_voucher', ['uses' => 'WaybillController@addExpressScanVouchers', 'middleware' => 'roles', 'roles' => ['Admin', 'Finance', 'CustomerService', 'CS', 'Operation', 'OS', 'HQ', 'Agent']]);

    //Image Upload
    Route::post('/waybills/{waybill}/upload', ['uses' => 'WaybillController@upload', 'middleware' => 'roles', 'roles' => ['Admin', 'Finance', 'CustomerService', 'CS', 'Operation', 'OS', 'HQ']]);
    
    /**
     * BusSheet
     */
    // Route::apiResource('bus_sheets', 'BusSheetController');

    Route::get('/bus_sheets', ['uses' => 'BusSheetController@index', 'middleware' => 'roles', 'roles' => ['Admin', 'Finance', 'CustomerService', 'CS', 'Operation', 'OS', 'HQ']]);
    Route::post('/bus_sheets', ['uses' => 'BusSheetController@store', 'middleware' => 'roles', 'roles' => ['Admin', 'Finance', 'CustomerService', 'CS', 'Operation', 'OS', 'HQ']]);
    Route::delete('/bus_sheets/{bus_sheet}', ['uses' => 'BusSheetController@destroy', 'middleware' => 'roles', 'roles' => ['Admin', 'HQ', 'Operation']]);
    Route::put('/bus_sheets/{bus_sheet}', ['uses' => 'BusSheetController@update', 'middleware' => 'roles', 'roles' => ['Admin', 'Finance', 'CustomerService', 'CS', 'Operation', 'OS', 'HQ']]);
    Route::get('/bus_sheets/{bus_sheet}', ['uses' => 'BusSheetController@show', 'middleware' => 'roles', 'roles' => ['Admin', 'Finance', 'CustomerService', 'CS', 'Operation', 'OS', 'HQ']]);

    Route::post('bus_sheets/{bus_sheet}/remove_vouchers', ['uses' => 'BusSheetController@removeVouchers', 'middleware' => 'roles', 'roles' => ['Admin', 'Finance', 'CustomerService', 'CS', 'Operation', 'OS', 'HQ']]);
    Route::post('bus_sheets/{bus_sheet}/add_vouchers', ['uses' => 'BusSheetController@addVouchers', 'middleware' => 'roles', 'roles' => ['Admin', 'Finance', 'CustomerService', 'CS', 'Operation', 'OS', 'HQ']]);

    /**
     * MerchantSheet
     */
    // Route::apiResource('merchant_sheets', 'MerchantSheetController');
    Route::get('/merchant_sheets', ['uses' => 'MerchantSheetController@index', 'middleware' => 'roles', 'roles' => ['Admin', 'Finance', 'CustomerService', 'CS', 'Operation', 'OS', 'HQ', 'Agent']]);
    Route::post('/merchant_sheets', ['uses' => 'MerchantSheetController@store', 'middleware' => 'roles', 'roles' => ['Admin', 'Finance', 'CustomerService', 'CS', 'Operation', 'OS', 'HQ', 'Agent']]);
    Route::delete('/merchant_sheets/{merchant_sheet}', ['uses' => 'MerchantSheetController@destroy', 'middleware' => 'roles', 'roles' => ['Admin', 'HQ', 'Agent', 'Operation']]);
    Route::put('/merchant_sheets/{merchant_sheet}', ['uses' => 'MerchantSheetController@update', 'middleware' => 'roles', 'roles' => ['Admin', 'Finance', 'CustomerService', 'CS', 'Operation', 'OS', 'HQ', 'Agent']]);
    Route::get('/merchant_sheets/{merchant_sheet}', ['uses' => 'MerchantSheetController@show', 'middleware' => 'roles', 'roles' => ['Admin', 'Finance', 'CustomerService', 'CS', 'Operation', 'OS', 'HQ', 'Agent']]);

    // Route::get('merchant_sheet_filter', 'MerchantSheetController@filterVoucher');
    Route::get('voucher_details_by_merchant_sheet/{id}', ['uses' => 'MerchantSheetController@voucherDetails', 'middleware' => 'roles', 'roles' => ['Admin', 'Finance', 'CustomerService', 'CS', 'Operation', 'OS', 'HQ', 'Agent']]);

    Route::post('merchant_sheets/{merchant_sheet}/remove_vouchers', ['uses' => 'MerchantSheetController@removeVouchers', 'middleware' => 'roles', 'roles' => ['Admin', 'Finance', 'CustomerService', 'CS', 'Operation', 'OS', 'HQ', 'Agent']]);
    Route::post('merchant_sheets/{merchant_sheet}/add_vouchers', ['uses' => 'MerchantSheetController@addVouchers', 'middleware' => 'roles', 'roles' => ['Admin', 'Finance', 'CustomerService', 'CS', 'Operation', 'OS', 'HQ', 'Agent']]);

    /**
     * ReturnSheet
     */
    //Route::apiResource('return_sheets', 'ReturnSheetController');
    Route::get('/return_sheets', ['uses' => 'ReturnSheetController@index', 'middleware' => 'roles', 'roles' => ['Admin', 'Finance', 'CustomerService', 'CS', 'Operation', 'OS', 'HQ', 'Agent']]);
    Route::post('/return_sheets', ['uses' => 'ReturnSheetController@store', 'middleware' => 'roles', 'roles' => ['Admin', 'Finance', 'CustomerService', 'CS', 'Operation', 'OS', 'HQ', 'Agent']]);
    Route::delete('/return_sheets/{return_sheet}', ['uses' => 'ReturnSheetController@destroy', 'middleware' => 'roles', 'roles' => ['Admin', 'HQ', 'Operation']]);
    Route::put('/return_sheets/{return_sheet}', ['uses' => 'ReturnSheetController@update', 'middleware' => 'roles', 'roles' => ['Admin', 'Finance', 'CustomerService', 'CS', 'Operation', 'OS', 'HQ', 'Agent']]);
    Route::get('/return_sheets/{return_sheet}', ['uses' => 'ReturnSheetController@show', 'middleware' => 'roles', 'roles' => ['Admin', 'Finance', 'CustomerService', 'CS', 'Operation', 'OS', 'HQ', 'Agent']]);
    Route::post('return_sheets/{return_sheet}/remove_vouchers', ['uses' => 'ReturnSheetController@removeVouchers', 'middleware' => 'roles', 'roles' => ['Admin', 'Finance', 'CustomerService', 'CS', 'Operation', 'OS', 'HQ', 'Agent']]);
    Route::post('return_sheets/{return_sheet}/add_vouchers', ['uses' => 'ReturnSheetController@addVouchers', 'middleware' => 'roles', 'roles' => ['Admin', 'Finance', 'CustomerService', 'CS', 'Operation', 'OS', 'HQ', 'Agent']]);

    Route::post('return_sheets/{return_sheet}/closed', ['uses' => 'ReturnSheetController@closed', 'middleware' => 'roles', 'roles' => ['Admin', 'Finance', 'CustomerService', 'CS', 'Operation', 'OS', 'HQ', 'Agent']]);
    //Manual Close
    Route::post('return_sheets/{return_sheet}/manual_closed', ['uses' => 'ReturnSheetController@manual_closed', 'middleware' => 'roles', 'roles' => ['Admin', 'Finance', 'CustomerService', 'CS', 'Operation', 'OS', 'HQ', 'Agent']]);
    //Upload ReturnSheet Image
    Route::post('return_sheets/{return_sheet}/upload', ['uses' => 'ReturnSheetController@upload', 'middleware' => 'roles', 'roles' => ['Admin', 'Finance', 'CustomerService', 'CS', 'Operation', 'OS', 'HQ', 'Agent']]);
    // change delivery
    Route::post('return_sheets/change_hero', ['uses' => 'ReturnSheetController@change_hero', 'middleware' => 'roles', 'roles' => ['Admin', 'Finance', 'CustomerService', 'CS', 'Operation', 'OS', 'HQ', 'Agent']]);


    /**
     * MerchantSheet
     */
    // Route::apiResource('branch_sheets', 'BranchSheetController');
    Route::get('/branch_sheets', ['uses' => 'BranchSheetController@index', 'middleware' => 'roles', 'roles' => ['Admin', 'Finance', 'CustomerService', 'CS', 'Operation', 'OS', 'HQ']]);
    Route::post('/branch_sheets', ['uses' => 'BranchSheetController@store', 'middleware' => 'roles', 'roles' => ['Admin', 'Finance', 'CustomerService', 'CS', 'Operation', 'OS', 'HQ']]);
    Route::delete('/branch_sheets/{branch_sheet}', ['uses' => 'BranchSheetController@destroy', 'middleware' => 'roles', 'roles' => ['Admin', 'HQ']]);
    Route::put('/branch_sheets/{branch_sheet}', ['uses' => 'BranchSheetController@update', 'middleware' => 'roles', 'roles' => ['Admin', 'Finance', 'CustomerService', 'CS', 'Operation', 'OS', 'HQ']]);
    Route::get('/branch_sheets/{branch_sheet}', ['uses' => 'BranchSheetController@show', 'middleware' => 'roles', 'roles' => ['Admin', 'Finance', 'CustomerService', 'CS', 'Operation', 'OS', 'HQ']]);

    /**
     * Transaction Topup
     */
    // Route::apiResource('transactions', 'TransactionController');

    Route::get('/transactions', ['uses' => 'TransactionController@index', 'middleware' => 'roles', 'roles' => ['Admin', 'Finance', 'CustomerService', 'CS', 'HQ', 'Agent']]);
    Route::get('/hq_balance_transactions', ['uses' => 'TransactionController@hqBalanceLists', 'middleware' => 'roles', 'roles' => ['Admin', 'Finance', 'CustomerService', 'CS', 'HQ', 'Agent']]);
    Route::post('/transactions', ['uses' => 'TransactionController@store', 'middleware' => 'roles', 'roles' => ['Admin', 'Finance', 'CustomerService', 'CS', 'HQ', 'Agent']]);
    Route::delete('/transactions/{transaction}', ['uses' => 'TransactionController@destroy', 'middleware' => 'roles', 'roles' => ['Admin', 'HQ']]);
    Route::put('/transactions/{transaction}', ['uses' => 'TransactionController@update', 'middleware' => 'roles', 'roles' => ['Admin', 'Finance', 'CustomerService', 'CS', 'HQ', 'Agent']]);
    Route::post('/transactions/{transaction}/upload', ['uses' => 'TransactionController@upload', 'middleware' => 'roles', 'roles' => ['Admin', 'Finance', 'CustomerService', 'CS', 'HQ', 'Agent']]);
    Route::get('/transactions/{transaction}', ['uses' => 'TransactionController@show', 'middleware' => 'roles', 'roles' => ['Admin', 'Finance', 'CustomerService', 'CS', 'HQ', 'Agent']]);
    Route::post('/delete_pending_transactions', ['uses' => 'TransactionController@delete_pending_transactions', 'middleware' => 'roles', 'roles' => ['Admin', 'HQ']]);

    Route::post('/transactions/create_withdraw', ['uses' => 'TransactionController@create_withdraw', 'middleware' => 'roles', 'roles' => ['Admin', 'Finance', 'CustomerService', 'CS', 'HQ', 'Agent']]);
    Route::post('/transactions/create_topup', ['uses' => 'TransactionController@create_topup', 'middleware' => 'roles', 'roles' => ['Admin', 'Finance', 'CustomerService', 'CS', 'HQ', 'Agent']]);
    Route::post('/transactions/{transaction}/update_bank_information', ['uses' => 'TransactionController@updateBankInformation', 'middleware' => 'roles', 'roles' => ['Admin', 'Finance', 'CustomerService', 'CS', 'HQ', 'Agent']]);

    Route::post('/transactions/{transaction}/update_journal', ['uses' => 'TransactionController@update_journal', 'middleware' => 'roles', 'roles' => ['Admin', 'Finance', 'CustomerService', 'CS', 'HQ', 'Agent']]);
    Route::post('/transactions/update_null', ['uses' => 'TransactionController@updateNull', 'middleware' => 'roles', 'roles' => ['Admin', 'Finance', 'CustomerService', 'CS', 'HQ', 'Agent']]);

    Route::apiResource('attachments', 'AttachmentController');

    /**
     * AgentFinanceSheet
     */
    // Route::apiResource('agent_finance_sheets', 'AgentFinanceSheetController');


    //Finance Confirm For Delisheet Voucher
    Route::post('delisheet_finance_confirm', ['uses' => 'AccountController@delisheet_financeConfirm', 'middleware' => 'roles', 'roles' => ['Admin', 'Finance', 'CustomerService', 'CS', 'Operation', 'OS', 'HQ']]);
    Route::post('manual_delisheet_finance_confirm', ['uses' => 'AccountController@manual_delisheet_financeConfirm', 'middleware' => 'roles', 'roles' => ['Admin', 'Finance', 'CustomerService', 'CS', 'Operation', 'OS', 'HQ']]);

    //Finance Confirm For Pickup and Prepaid Voucher
    Route::post('pickup_finance_confirm', ['uses' => 'AccountController@pickup_financeConfirm', 'middleware' => 'roles', 'roles' => ['Admin', 'Finance', 'CustomerService', 'CS', 'Operation', 'OS', 'HQ']]);

    //Finance Confirm For Waybill Voucher
    Route::post('waybill_finance_confirm', ['uses' => 'AccountController@waybill_financeConfirm', 'middleware' => 'roles', 'roles' => ['Admin', 'Finance', 'CustomerService', 'CS', 'Operation', 'OS', 'HQ', 'Agent']]);

    //Finance Confirm For Bus Sheet Voucher
    Route::post('bus_sheet_finance_confirm', ['uses' => 'AccountController@bus_sheet_financeConfirm', 'middleware' => 'roles', 'roles' => ['Admin', 'Finance', 'CustomerService', 'CS', 'Operation', 'OS', 'HQ']]);

    //Finance Confirm For Merchant Sheet Voucher
    Route::post('merchant_sheet_finance_confirm', ['uses' => 'AccountController@merchant_sheet_financeConfirm', 'middleware' => 'roles', 'roles' => ['Admin', 'Finance', 'CustomerService', 'CS', 'Operation', 'OS', 'HQ']]);

    //Finance Confirm For Branch Sheet Voucher
    Route::post('branch_sheet_finance_confirm', ['uses' => 'AccountController@branch_sheet_financeConfirm', 'middleware' => 'roles', 'roles' => ['Admin', 'Finance', 'CustomerService', 'CS', 'Operation', 'OS', 'HQ']]);

    //Finance Confirm For Merchant Sheet Voucher
    Route::post('assign_role', ['uses' => 'StaffController@assign_role', 'middleware' => 'roles', 'roles' => ['Admin', 'Finance', 'CustomerService', 'CS', 'Operation', 'OS', 'HQ']]);

    //Unpaid All Sheet by Delivery
    Route::get('delivery/{delivery}/unpiad_sheets_delivery', ['uses' => 'DeliveryController@delivery_unpiad_sheets', 'middleware' => 'roles', 'roles' => ['Admin', 'Finance', 'CustomerService', 'CS', 'Operation', 'OS', 'HQ']]);

    //Unpaid All Sheet by Delivery
    Route::get('delivery/{delivery}/unpiad_sheets', ['uses' => 'DeliveryController@unpiad_sheets', 'middleware' => 'roles', 'roles' => ['Admin', 'Finance', 'CustomerService', 'CS', 'Operation', 'OS', 'HQ']]);

    //Unpaid All Sheet by Delivery
    Route::post('update_balance', ['uses' => 'AccountController@update_balance', 'middleware' => 'roles', 'roles' => ['Admin', 'Finance', 'HQ']]);

    /**
     * Account lists with Balance for agent/Branch/HQ
     */
    Route::get('account_balances', ['uses' => 'AccountController@getAccountBalance', 'middleware' => 'roles', 'roles' => ['Admin', 'Finance', 'HQ', 'Agent']]);
    /**
     * Account lists with Balance for Merchant
     */
    Route::get('merchant_account_balances', ['uses' => 'AccountController@get_merchant_account_balance', 'middleware' => 'roles', 'roles' => ['Admin', 'Finance', 'HQ','Agent', 'CustomerService']]);


    /**
     * Merchant and Agent Transaction lists with Balance for agent/Branch/HQ
     */
    Route::get('merchants/{merchant}/transactions', ['uses' => 'MerchantController@transaction_lists', 'middleware' => 'roles', 'roles' => ['Admin', 'Finance', 'CS', 'HQ', 'Agent', 'CustomerService']]);
    Route::get('merchants/{merchant}/temp_journals', ['uses' => 'MerchantController@temp_journal_lists', 'middleware' => 'roles', 'roles' => ['Admin', 'Finance', 'CS', 'HQ', 'Agent', 'CustomerService']]);
    Route::get('agents/{agent}/transactions', ['uses' => 'AgentController@transaction_lists', 'middleware' => 'roles', 'roles' => ['Admin', 'Finance', 'HQ', 'Agent']]);
    Route::get('branches/{branch}/transactions', ['uses' => 'BranchController@transaction_lists', 'middleware' => 'roles', 'roles' => ['Admin', 'Finance', 'HQ', 'Agent']]);

    //emergency api
    Route::post('routes/{route}/update_price', ['uses' => 'RouteController@update_price', 'middleware' => 'roles', 'roles' => ['Admin', 'Finance', 'HQ', 'Agent']]);

    Route::get('point_logs', ['uses' => 'PointLogController@index', 'middleware' => 'roles', 'roles' => ['Admin', 'Finance', 'CS', 'HQ', 'Agent', 'CustomerService', 'Operation', 'OS']]);
    Route::post('point_deduction', ['uses' => 'PointLogController@pointDeduction', 'middleware' => 'roles', 'roles' => ['Admin', 'Finance', 'CS', 'HQ', 'Agent', 'CustomerService', 'Operation', 'OS']]);

    // Commission Logs
    Route::get('commission_logs', ['uses' => 'CommissionLogController@index', 'middleware' => 'roles', 'roles' => ['Admin', 'Finance', 'CS', 'HQ', 'Agent', 'CustomerService', 'Operation', 'OS']]);

    // Invoices
    Route::apiResource('invoices', 'InvoiceController');
    Route::get('invoices/{invoice}/histories', ['uses' => 'InvoiceController@histories', 'middleware' => 'roles', 'roles' => ['Admin', 'Finance', 'CustomerService', 'CS', 'Operation', 'OS', 'HQ', 'Agent']]);
    Route::post('invoices/{invoice}/remove', ['uses' => 'InvoiceController@removeVoucher', 'middleware' => 'roles', 'roles' => ['Admin', 'Finance', 'CustomerService', 'CS', 'Operation', 'OS', 'HQ', 'Agent']]);
    Route::post('invoices/{invoice}/add', ['uses' => 'InvoiceController@addVoucher', 'middleware' => 'roles', 'roles' => ['Admin', 'Finance', 'CustomerService', 'CS', 'Operation', 'OS', 'HQ', 'Agent']]);
    
    Route::post('invoices/{invoice}/confirm', ['uses' => 'InvoiceController@confirm', 'middleware' => 'roles', 'roles' => ['Admin', 'Finance', 'CustomerService', 'CS', 'Operation', 'OS', 'HQ']]);
    //Upload Pickup Image
    Route::post('invoices/{invoice}/upload', ['uses' => 'InvoiceController@upload', 'middleware' => 'roles', 'roles' => ['Admin', 'Finance', 'CustomerService', 'CS', 'Operation', 'OS', 'HQ']]);
    Route::post('invoice_journals/{invoice_journal}/update', ['uses' => 'InvoiceController@update_adjustment_amount', 'middleware' => 'roles', 'roles' => ['Admin', 'Finance', 'CustomerService', 'CS', 'HQ']]);
});
