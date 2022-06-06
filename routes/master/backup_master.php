<?php

Route::group(['middleware' => 'roles', 'roles' => ['Admin','Finance','CustomerService','CS','Operation','OS']], function () {

    Route::apiResource('cities', 'CityController');

    Route::apiResource('zones', 'ZoneController');

    Route::apiResource('buses', 'BusController');

    Route::apiResource('departments', 'DepartmentController');

    Route::apiResource('call_statuses', 'CallStatusController');

    Route::apiResource('tracking_statuses', 'TrackingStatusController');

    Route::apiResource('delivery_statuses', 'DeliveryStatusController');

    Route::apiResource('store_statuses', 'StoreStatusController');

    Route::apiResource('metas', 'MetaController');

    Route::apiResource('courier_types', 'CourierTypeController');

    Route::post('/staffs/{staff}/assign_roles', 'StaffController@assignRoles');

    Route::apiResource('staffs', 'StaffController');

    Route::apiResource('bus_stations', 'BusStationController');

    Route::apiResource('merchants', 'MerchantController');

    Route::post('merchant_associates', 'MerchantAssociateController@store');
    Route::put('merchant_associates/{merchant_associate}', 'MerchantAssociateController@update');
    Route::delete('merchant_associates/{merchant_associate}', 'MerchantAssociateController@destroy');
    // Route::apiResource('/merchant/{merchant}/merchant_associate', ['uses' => 'MerchantAssociateController@index', 'middleware' => 'roles', 'roles' => ['Admin','Finance','CustomerService','CS','Operation','OS']]);

    Route::apiResource('gates', 'GateController');

    Route::apiResource('customers', 'CustomerController');

    Route::apiResource('flags', 'FlagController');

    Route::apiResource('badges', 'BadgeController');

    Route::apiResource('log_statuses', 'LogStatusController');

    Route::apiResource('roles', 'RoleController');

    Route::apiResource('discount_types', 'DiscountTypeController');

    Route::apiResource('global_scales', 'GlobalScaleController');

    Route::apiResource('payment_types', 'PaymentTypeController');

    Route::apiResource('payment_statuses', 'PaymentStatusController');

    Route::apiResource('merchant_discounts', 'MerchantDiscountController');

    Route::get('/check_coupon_code', 'CouponController@check_coupon_code');

    Route::apiResource('coupons', 'CouponController');

    Route::apiResource('routes', 'RouteController');

    Route::apiResource('delegate_durations', 'DelegateDurationController');

    Route::apiResource('door_to_doors', 'DoorToDoorController');

    Route::apiResource('bus_drop_offs', 'BusDropOffController');

    Route::apiResource('agents', 'AgentController');

});