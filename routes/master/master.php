<?php
Route::get('/branches', ['uses' => 'BranchController@index', 'middleware' => 'roles', 'roles' => ['Admin', 'HQ', 'Finance', 'CustomerService', 'CS', 'Operation', 'OS', 'Agent']]);
Route::post('/branches', ['uses' => 'BranchController@store', 'middleware' => 'roles', 'roles' => ['Admin', 'HQ', 'Finance', 'CustomerService', 'CS', 'Operation', 'OS', 'Agent']]);
Route::delete('/branches/{branch}', ['uses' => 'BranchController@destroy', 'middleware' => 'roles', 'roles' => ['Admin', 'HQ']]);
Route::put('/branches/{branch}', ['uses' => 'BranchController@update', 'middleware' => 'roles', 'roles' => ['Admin', 'HQ', 'Finance', 'CustomerService', 'CS', 'Operation', 'OS', 'Agent']]);
Route::get('/branches/{branch}', ['uses' => 'BranchController@show', 'middleware' => 'roles', 'roles' => ['Admin', 'HQ', 'Finance', 'CustomerService', 'CS', 'Operation', 'OS', 'Agent']]);

Route::get('/cities', ['uses' => 'CityController@index', 'middleware' => 'roles', 'roles' => ['Admin', 'HQ', 'Finance', 'CustomerService', 'CS', 'Operation', 'OS', 'Agent']]);
Route::post('/cities', ['uses' => 'CityController@store', 'middleware' => 'roles', 'roles' => ['Admin', 'HQ', 'Finance', 'CustomerService', 'CS', 'Operation', 'OS', 'Agent']]);
Route::delete('/cities/{city}', ['uses' => 'CityController@destroy', 'middleware' => 'roles', 'roles' => ['Admin', 'HQ']]);
Route::put('/cities/{city}', ['uses' => 'CityController@update', 'middleware' => 'roles', 'roles' => ['Admin', 'HQ', 'Finance', 'CustomerService', 'CS', 'Operation', 'OS', 'Agent']]);
Route::get('/cities/{city}', ['uses' => 'CityController@show', 'middleware' => 'roles', 'roles' => ['Admin', 'HQ', 'Finance', 'CustomerService', 'CS', 'Operation', 'OS', 'Agent']]);
Route::post('/cities/update_specify_data', ['uses' => 'CityController@update_specify_data', 'middleware' => 'roles', 'roles' => ['Admin', 'HQ', 'Finance', 'CustomerService', 'CS', 'Operation', 'OS', 'Agent']]);
Route::get('/cities/{city}/get_agents', ['uses' => 'CityController@getAgents', 'middleware' => 'roles', 'roles' => ['Admin', 'HQ', 'Finance', 'CustomerService', 'CS', 'Operation', 'OS', 'Agent']]);

Route::get('/zones', ['uses' => 'ZoneController@index', 'middleware' => 'roles', 'roles' => ['Admin', 'HQ', 'Finance', 'CustomerService', 'CS', 'Operation', 'OS', 'Agent']]);
Route::post('/zones', ['uses' => 'ZoneController@store', 'middleware' => 'roles', 'roles' => ['Admin', 'HQ', 'Finance', 'CustomerService', 'CS', 'Operation', 'OS', 'Agent']]);
Route::delete('/zones/{zone}', ['uses' => 'ZoneController@destroy', 'middleware' => 'roles', 'roles' => ['Admin', 'HQ']]);
Route::put('/zones/{zone}', ['uses' => 'ZoneController@update', 'middleware' => 'roles', 'roles' => ['Admin', 'HQ', 'Finance', 'CustomerService', 'CS', 'Operation', 'OS', 'Agent']]);
Route::get('/zones/{zone}', ['uses' => 'ZoneController@show', 'middleware' => 'roles', 'roles' => ['Admin', 'HQ', 'Finance', 'CustomerService', 'CS', 'Operation', 'OS', 'Agent']]);

Route::get('/buses', ['uses' => 'BusController@index', 'middleware' => 'roles', 'roles' => ['Admin', 'HQ', 'Finance', 'CustomerService', 'CS', 'Operation', 'OS', 'Agent']]);
Route::post('/buses', ['uses' => 'BusController@store', 'middleware' => 'roles', 'roles' => ['Admin', 'HQ', 'Finance', 'CustomerService', 'CS', 'Operation', 'OS', 'Agent']]);
Route::delete('/buses/{bus}', ['uses' => 'BusController@destroy', 'middleware' => 'roles', 'roles' => ['Admin', 'HQ']]);
Route::put('/buses/{bus}', ['uses' => 'BusController@update', 'middleware' => 'roles', 'roles' => ['Admin', 'HQ', 'Finance', 'CustomerService', 'CS', 'Operation', 'OS', 'Agent']]);
Route::get('/buses/{bus}', ['uses' => 'BusController@show', 'middleware' => 'roles', 'roles' => ['Admin', 'HQ', 'Finance', 'CustomerService', 'CS', 'Operation', 'OS', 'Agent']]);

Route::get('/departments', ['uses' => 'DepartmentController@index', 'middleware' => 'roles', 'roles' => ['Admin', 'HQ', 'Finance', 'CustomerService', 'CS', 'Operation', 'OS', 'Agent']]);
Route::post('/departments', ['uses' => 'DepartmentController@store', 'middleware' => 'roles', 'roles' => ['Admin', 'HQ', 'Finance', 'CustomerService', 'CS', 'Operation', 'OS', 'Agent']]);
Route::delete('/departments/{department}', ['uses' => 'DepartmentController@destroy', 'middleware' => 'roles', 'roles' => ['Admin', 'HQ']]);
Route::put('/departments/{department}', ['uses' => 'DepartmentController@update', 'middleware' => 'roles', 'roles' => ['Admin', 'HQ', 'Finance', 'CustomerService', 'CS', 'Operation', 'OS', 'Agent']]);
Route::get('/departments/{department}', ['uses' => 'DepartmentController@show', 'middleware' => 'roles', 'roles' => ['Admin', 'HQ', 'Finance', 'CustomerService', 'CS', 'Operation', 'OS', 'Agent']]);

Route::get('/call_statuses', ['uses' => 'CallStatusController@index', 'middleware' => 'roles', 'roles' => ['Admin', 'HQ', 'Finance', 'CustomerService', 'CS', 'Operation', 'OS', 'Agent']]);
Route::post('/call_statuses', ['uses' => 'CallStatusController@store', 'middleware' => 'roles', 'roles' => ['Admin', 'HQ', 'Finance', 'CustomerService', 'CS', 'Operation', 'OS', 'Agent']]);
Route::delete('/call_statuses/{call_status}', ['uses' => 'CallStatusController@destroy', 'middleware' => 'roles', 'roles' => ['Admin', 'HQ']]);
Route::put('/call_statuses/{call_status}', ['uses' => 'CallStatusController@update', 'middleware' => 'roles', 'roles' => ['Admin', 'HQ', 'Finance', 'CustomerService', 'CS', 'Operation', 'OS', 'Agent']]);
Route::get('/call_statuses/{call_status}', ['uses' => 'CallStatusController@show', 'middleware' => 'roles', 'roles' => ['Admin', 'HQ', 'Finance', 'CustomerService', 'CS', 'Operation', 'OS', 'Agent']]);

Route::get('/tracking_statuses', ['uses' => 'TrackingStatusController@index', 'middleware' => 'roles', 'roles' => ['Admin', 'HQ', 'Finance', 'CustomerService', 'CS', 'Operation', 'OS', 'Agent']]);
Route::post('/tracking_statuses', ['uses' => 'TrackingStatusController@store', 'middleware' => 'roles', 'roles' => ['Admin', 'HQ', 'Finance', 'CustomerService', 'CS', 'Operation', 'OS', 'Agent']]);
Route::delete('/tracking_statuses/{tracking_status}', ['uses' => 'TrackingStatusController@destroy', 'middleware' => 'roles', 'roles' => ['Admin', 'HQ']]);
Route::put('/tracking_statuses/{tracking_status}', ['uses' => 'TrackingStatusController@update', 'middleware' => 'roles', 'roles' => ['Admin', 'HQ', 'Finance', 'CustomerService', 'CS', 'Operation', 'OS', 'Agent']]);
Route::get('/tracking_statuses/{tracking_status}', ['uses' => 'TrackingStatusController@show', 'middleware' => 'roles', 'roles' => ['Admin', 'HQ', 'Finance', 'CustomerService', 'CS', 'Operation', 'OS', 'Agent']]);

Route::get('/delivery_statuses', ['uses' => 'DeliveryStatusController@index', 'middleware' => 'roles', 'roles' => ['Admin', 'HQ', 'Finance', 'CustomerService', 'CS', 'Operation', 'OS', 'Agent']]);
Route::post('/delivery_statuses', ['uses' => 'DeliveryStatusController@store', 'middleware' => 'roles', 'roles' => ['Admin', 'HQ', 'Finance', 'CustomerService', 'CS', 'Operation', 'OS', 'Agent']]);
Route::delete('/delivery_statuses/{delivery_status}', ['uses' => 'DeliveryStatusController@destroy', 'middleware' => 'roles', 'roles' => ['Admin', 'HQ']]);
Route::put('/delivery_statuses/{delivery_status}', ['uses' => 'DeliveryStatusController@update', 'middleware' => 'roles', 'roles' => ['Admin', 'HQ', 'Finance', 'CustomerService', 'CS', 'Operation', 'OS', 'Agent']]);
Route::get('/delivery_statuses/{delivery_status}', ['uses' => 'DeliveryStatusController@show', 'middleware' => 'roles', 'roles' => ['Admin', 'HQ', 'Finance', 'CustomerService', 'CS', 'Operation', 'OS', 'Agent']]);

Route::get('/store_statuses', ['uses' => 'StoreStatusController@index', 'middleware' => 'roles', 'roles' => ['Admin', 'HQ', 'Finance', 'CustomerService', 'CS', 'Operation', 'OS', 'Agent']]);
Route::post('/store_statuses', ['uses' => 'StoreStatusController@store', 'middleware' => 'roles', 'roles' => ['Admin', 'HQ', 'Finance', 'CustomerService', 'CS', 'Operation', 'OS', 'Agent']]);
Route::delete('/store_statuses/{store_status}', ['uses' => 'StoreStatusController@destroy', 'middleware' => 'roles', 'roles' => ['Admin', 'HQ']]);
Route::put('/store_statuses/{store_status}', ['uses' => 'StoreStatusController@update', 'middleware' => 'roles', 'roles' => ['Admin', 'HQ', 'Finance', 'CustomerService', 'CS', 'Operation', 'OS', 'Agent']]);
Route::get('/store_statuses/{store_status}', ['uses' => 'StoreStatusController@show', 'middleware' => 'roles', 'roles' => ['Admin', 'HQ', 'Finance', 'CustomerService', 'CS', 'Operation', 'OS', 'Agent']]);

Route::get('/metas', ['uses' => 'MetaController@index', 'middleware' => 'roles', 'roles' => ['Admin', 'HQ', 'Finance', 'CustomerService', 'CS', 'Operation', 'OS', 'Agent']]);
Route::post('/metas', ['uses' => 'MetaController@store', 'middleware' => 'roles', 'roles' => ['Admin', 'HQ', 'Finance', 'CustomerService', 'CS', 'Operation', 'OS', 'Agent']]);
Route::delete('/metas/{meta}', ['uses' => 'MetaController@destroy', 'middleware' => 'roles', 'roles' => ['Admin', 'HQ']]);
Route::put('/metas/{meta}', ['uses' => 'MetaController@update', 'middleware' => 'roles', 'roles' => ['Admin', 'HQ', 'Finance', 'CustomerService', 'CS', 'Operation', 'OS', 'Agent']]);
Route::get('/metas/{meta}', ['uses' => 'MetaController@show', 'middleware' => 'roles', 'roles' => ['Admin', 'HQ', 'Finance', 'CustomerService', 'CS', 'Operation', 'OS', 'Agent']]);

Route::get('/courier_types', ['uses' => 'CourierTypeController@index', 'middleware' => 'roles', 'roles' => ['Admin', 'HQ', 'Finance', 'CustomerService', 'CS', 'Operation', 'OS', 'Agent']]);
Route::post('/courier_types', ['uses' => 'CourierTypeController@store', 'middleware' => 'roles', 'roles' => ['Admin', 'HQ', 'Finance', 'CustomerService', 'CS', 'Operation', 'OS', 'Agent']]);
Route::delete('/courier_types/{courier_type}', ['uses' => 'CourierTypeController@destroy', 'middleware' => 'roles', 'roles' => ['Admin', 'HQ']]);
Route::put('/courier_types/{courier_type}', ['uses' => 'CourierTypeController@update', 'middleware' => 'roles', 'roles' => ['Admin', 'HQ', 'Finance', 'CustomerService', 'CS', 'Operation', 'OS', 'Agent']]);
Route::get('/courier_types/{courier_type}', ['uses' => 'CourierTypeController@show', 'middleware' => 'roles', 'roles' => ['Admin', 'HQ', 'Finance', 'CustomerService', 'CS', 'Operation', 'OS', 'Agent']]);

Route::get('/staffs', ['uses' => 'StaffController@index', 'middleware' => 'roles', 'roles' => ['Admin', 'HQ', 'Finance', 'CustomerService', 'CS', 'Operation', 'OS', 'Agent']]);
Route::post('/staffs', ['uses' => 'StaffController@store', 'middleware' => 'roles', 'roles' => ['Admin', 'HQ', 'Finance', 'CustomerService', 'CS', 'Operation', 'OS', 'Agent']]);
Route::delete('/staffs/{staff}', ['uses' => 'StaffController@destroy', 'middleware' => 'roles', 'roles' => ['Admin', 'HQ']]);
Route::put('/staffs/{staff}', ['uses' => 'StaffController@update', 'middleware' => 'roles', 'roles' => ['Admin', 'HQ', 'Finance', 'CustomerService', 'CS', 'Operation', 'OS', 'Agent']]);
Route::get('/staffs/{staff}', ['uses' => 'StaffController@show', 'middleware' => 'roles', 'roles' => ['Admin', 'HQ', 'Finance', 'CustomerService', 'CS', 'Operation', 'OS', 'Agent']]);
Route::post('/staffs/{staff}/assign_roles', ['uses' => 'StaffController@assignRoles', 'middleware' => 'roles', 'roles' => ['Admin', 'HQ', 'Finance', 'CustomerService', 'CS', 'Operation', 'OS', 'Agent']]);
Route::get('/staffs/{staff}/reset_point', ['uses' => 'StaffController@reset_point', 'middleware' => 'roles', 'roles' => ['Admin', 'HQ', 'Finance', 'Operation', 'OS', 'Agent']]);
Route::get('/reset_all_points', ['uses' => 'StaffController@reset_points', 'middleware' => 'roles', 'roles' => ['Admin', 'HQ', 'Finance', 'Operation', 'OS', 'Agent']]);
Route::post('/staffs/delete', ['uses' => 'StaffController@destroy_all', 'middleware' => 'roles', 'roles' => ['Admin', 'HQ']]);

Route::get('/bus_stations', ['uses' => 'BusStationController@index', 'middleware' => 'roles', 'roles' => ['Admin', 'HQ', 'Finance', 'CustomerService', 'CS', 'Operation', 'OS', 'Agent']]);
Route::post('/bus_stations', ['uses' => 'BusStationController@store', 'middleware' => 'roles', 'roles' => ['Admin', 'HQ', 'Finance', 'CustomerService', 'CS', 'Operation', 'OS', 'Agent']]);
Route::delete('/bus_stations/{bus_station}', ['uses' => 'BusStationController@destroy', 'middleware' => 'roles', 'roles' => ['Admin', 'HQ']]);
Route::put('/bus_stations/{bus_station}', ['uses' => 'BusStationController@update', 'middleware' => 'roles', 'roles' => ['Admin', 'HQ', 'Finance', 'CustomerService', 'CS', 'Operation', 'OS', 'Agent']]);
Route::get('/bus_stations/{bus_station}', ['uses' => 'BusStationController@show', 'middleware' => 'roles', 'roles' => ['Admin', 'HQ', 'Finance', 'CustomerService', 'CS', 'Operation', 'OS', 'Agent']]);

Route::get('/merchants', ['uses' => 'MerchantController@index', 'middleware' => 'roles', 'roles' => ['Admin', 'HQ', 'Finance', 'CustomerService', 'CS', 'Operation', 'OS', 'Agent']]);
Route::get('/merchants/all', ['uses' => 'MerchantController@all', 'middleware' => 'roles', 'roles' => ['Admin', 'HQ', 'Finance', 'CustomerService', 'CS', 'Operation', 'OS', 'Agent']]);
Route::post('/merchants', ['uses' => 'MerchantController@store', 'middleware' => 'roles', 'roles' => ['Admin', 'HQ', 'Finance', 'CustomerService', 'CS', 'Operation', 'OS', 'Agent']]);
Route::delete('/merchants/{merchant}', ['uses' => 'MerchantController@destroy', 'middleware' => 'roles', 'roles' => ['Admin', 'HQ', 'Agent']]);
Route::put('/merchants/{merchant}', ['uses' => 'MerchantController@update', 'middleware' => 'roles', 'roles' => ['Admin', 'HQ', 'Finance', 'CustomerService', 'CS', 'Operation', 'OS', 'Agent']]);
Route::get('/merchants/{merchant}', ['uses' => 'MerchantController@show', 'middleware' => 'roles', 'roles' => ['Admin', 'HQ', 'Finance', 'CustomerService', 'CS', 'Operation', 'OS', 'Agent']]);
Route::post('/merchants/{merchant}/is_discount', ['uses' => 'MerchantController@update_discount_status', 'middleware' => 'roles', 'roles' => ['Admin', 'HQ', 'Finance', 'CustomerService', 'CS', 'Operation', 'OS', 'Agent']]);
Route::post('/merchants/restore', ['uses' => 'MerchantController@restore', 'middleware' => 'roles', 'roles' => ['Admin', 'HQ', 'Finance', 'CustomerService', 'CS', 'Operation', 'OS', 'Agent']]);

Route::get('merchant_associates', 'MerchantAssociateController@index');
Route::post('merchant_associates', 'MerchantAssociateController@store');
Route::delete('/merchant_associates/{merchant_associate}', ['uses' => 'MerchantAssociateController@destroy', 'middleware' => 'roles', 'roles' => ['Admin', 'HQ', 'Agent']]);

Route::put('merchant_associates/{merchant_associate}', 'MerchantAssociateController@update');
// Route::apiResource('/merchant/{merchant}/merchant_associate', ['uses' => 'MerchantAssociateController@index', 'middleware' => 'roles', 'roles' => ['Admin', 'HQ', 'Finance','CustomerService','CS','Operation','OS']]);
Route::post('add_contact', 'MerchantController@add_contact');


Route::get('/gates', ['uses' => 'GateController@index', 'middleware' => 'roles', 'roles' => ['Admin', 'HQ', 'Finance', 'CustomerService', 'CS', 'Operation', 'OS', 'Agent']]);
Route::post('/gates', ['uses' => 'GateController@store', 'middleware' => 'roles', 'roles' => ['Admin', 'HQ', 'Finance', 'CustomerService', 'CS', 'Operation', 'OS', 'Agent']]);
Route::delete('/gates/{gate}', ['uses' => 'GateController@destroy', 'middleware' => 'roles', 'roles' => ['Admin', 'HQ']]);
Route::put('/gates/{gate}', ['uses' => 'GateController@update', 'middleware' => 'roles', 'roles' => ['Admin', 'HQ', 'Finance', 'CustomerService', 'CS', 'Operation', 'OS', 'Agent']]);
Route::get('/gates/{gate}', ['uses' => 'GateController@show', 'middleware' => 'roles', 'roles' => ['Admin', 'HQ', 'Finance', 'CustomerService', 'CS', 'Operation', 'OS', 'Agent']]);

Route::get('/customers', ['uses' => 'CustomerController@index', 'middleware' => 'roles', 'roles' => ['Admin', 'HQ', 'Finance', 'CustomerService', 'CS', 'Operation', 'OS', 'Agent']]);
Route::post('/customers', ['uses' => 'CustomerController@store', 'middleware' => 'roles', 'roles' => ['Admin', 'HQ', 'Finance', 'CustomerService', 'CS', 'Operation', 'OS', 'Agent']]);
Route::delete('/customers/{customer}', ['uses' => 'CustomerController@destroy', 'middleware' => 'roles', 'roles' => ['Admin', 'HQ']]);
Route::put('/customers/{customer}', ['uses' => 'CustomerController@update', 'middleware' => 'roles', 'roles' => ['Admin', 'HQ', 'Finance', 'CustomerService', 'CS', 'Operation', 'OS','Agent']]);
Route::get('/customers/{customer}', ['uses' => 'CustomerController@show', 'middleware' => 'roles', 'roles' => ['Admin', 'HQ', 'Finance', 'CustomerService', 'CS', 'Operation', 'OS','Agent']]);
Route::post('/customers/update_customer', ['uses' => 'CustomerController@update_customer', 'middleware' => 'roles', 'roles' => ['Admin', 'HQ', 'Finance', 'CustomerService', 'CS', 'Operation', 'OS','Agent']]);

Route::get('/flags', ['uses' => 'FlagController@index', 'middleware' => 'roles', 'roles' => ['Admin', 'HQ', 'Finance', 'CustomerService', 'CS', 'Operation', 'OS', 'Agent']]);
Route::post('/flags', ['uses' => 'FlagController@store', 'middleware' => 'roles', 'roles' => ['Admin', 'HQ', 'Finance', 'CustomerService', 'CS', 'Operation', 'OS', 'Agent']]);
Route::delete('/flags/{flag}', ['uses' => 'FlagController@destroy', 'middleware' => 'roles', 'roles' => ['Admin', 'HQ']]);
Route::put('/flags/{flag}', ['uses' => 'FlagController@update', 'middleware' => 'roles', 'roles' => ['Admin', 'HQ', 'Finance', 'CustomerService', 'CS', 'Operation', 'OS', 'Agent']]);
Route::get('/flags/{flag}', ['uses' => 'FlagController@show', 'middleware' => 'roles', 'roles' => ['Admin', 'HQ', 'Finance', 'CustomerService', 'CS', 'Operation', 'OS', 'Agent']]);

Route::get('/badges', ['uses' => 'BadgeController@index', 'middleware' => 'roles', 'roles' => ['Admin', 'HQ', 'Finance', 'CustomerService', 'CS', 'Operation', 'OS', 'Agent']]);
Route::post('/badges', ['uses' => 'BadgeController@store', 'middleware' => 'roles', 'roles' => ['Admin', 'HQ', 'Finance', 'CustomerService', 'CS', 'Operation', 'OS', 'Agent']]);
Route::delete('/badges/{badge}', ['uses' => 'BadgeController@destroy', 'middleware' => 'roles', 'roles' => ['Admin', 'HQ']]);
Route::put('/badges/{badge}', ['uses' => 'BadgeController@update', 'middleware' => 'roles', 'roles' => ['Admin', 'HQ', 'Finance', 'CustomerService', 'CS', 'Operation', 'OS', 'Agent']]);
Route::get('/badges/{badge}', ['uses' => 'BadgeController@show', 'middleware' => 'roles', 'roles' => ['Admin', 'HQ', 'Finance', 'CustomerService', 'CS', 'Operation', 'OS', 'Agent']]);

Route::get('/log_statuses', ['uses' => 'LogStatusController@index', 'middleware' => 'roles', 'roles' => ['Admin', 'HQ', 'Finance', 'CustomerService', 'CS', 'Operation', 'OS', 'Agent']]);
Route::post('/log_statuses', ['uses' => 'LogStatusController@store', 'middleware' => 'roles', 'roles' => ['Admin', 'HQ', 'Finance', 'CustomerService', 'CS', 'Operation', 'OS', 'Agent']]);
Route::delete('/log_statuses/{log_status}', ['uses' => 'LogStatusController@destroy', 'middleware' => 'roles', 'roles' => ['Admin', 'HQ']]);
Route::put('/log_statuses/{log_status}', ['uses' => 'LogStatusController@update', 'middleware' => 'roles', 'roles' => ['Admin', 'HQ', 'Finance', 'CustomerService', 'CS', 'Operation', 'OS', 'Agent']]);
Route::get('/log_statuses/{log_status}', ['uses' => 'LogStatusController@show', 'middleware' => 'roles', 'roles' => ['Admin', 'HQ', 'Finance', 'CustomerService', 'CS', 'Operation', 'OS', 'Agent']]);

Route::get('/roles', ['uses' => 'RoleController@index', 'middleware' => 'roles', 'roles' => ['Admin', 'HQ', 'Finance', 'CustomerService', 'CS', 'Operation', 'OS', 'Agent']]);
Route::post('/roles', ['uses' => 'RoleController@store', 'middleware' => 'roles', 'roles' => ['Admin', 'HQ', 'Finance', 'CustomerService', 'CS', 'Operation', 'OS', 'Agent']]);
Route::delete('/roles/{role}', ['uses' => 'RoleController@destroy', 'middleware' => 'roles', 'roles' => ['Admin', 'HQ']]);
Route::put('/roles/{role}', ['uses' => 'RoleController@update', 'middleware' => 'roles', 'roles' => ['Admin', 'HQ', 'Finance', 'CustomerService', 'CS', 'Operation', 'OS', 'Agent']]);
Route::get('/roles/{role}', ['uses' => 'RoleController@show', 'middleware' => 'roles', 'roles' => ['Admin', 'HQ', 'Finance', 'CustomerService', 'CS', 'Operation', 'OS', 'Agent']]);

Route::get('/discount_types', ['uses' => 'DiscountTypeController@index', 'middleware' => 'roles', 'roles' => ['Admin', 'HQ', 'Finance', 'CustomerService', 'CS', 'Operation', 'OS', 'Agent']]);
Route::post('/discount_types', ['uses' => 'DiscountTypeController@store', 'middleware' => 'roles', 'roles' => ['Admin', 'HQ', 'Finance', 'CustomerService', 'CS', 'Operation', 'OS', 'Agent']]);
Route::delete('/discount_types/{discount_type}', ['uses' => 'DiscountTypeController@destroy', 'middleware' => 'roles', 'roles' => ['Admin', 'HQ']]);
Route::put('/discount_types/{discount_type}', ['uses' => 'DiscountTypeController@update', 'middleware' => 'roles', 'roles' => ['Admin', 'HQ', 'Finance', 'CustomerService', 'CS', 'Operation', 'OS', 'Agent']]);
Route::get('/discount_types/{discount_type}', ['uses' => 'DiscountTypeController@show', 'middleware' => 'roles', 'roles' => ['Admin', 'HQ', 'Finance', 'CustomerService', 'CS', 'Operation', 'OS', 'Agent']]);

Route::get('/global_scales', ['uses' => 'GlobalScaleController@index', 'middleware' => 'roles', 'roles' => ['Admin', 'HQ', 'Finance', 'CustomerService', 'CS', 'Operation', 'OS', 'Agent']]);
Route::post('/global_scales', ['uses' => 'GlobalScaleController@store', 'middleware' => 'roles', 'roles' => ['Admin', 'HQ', 'Finance', 'CustomerService', 'CS', 'Operation', 'OS', 'Agent']]);
Route::delete('/global_scales/{global_scale}', ['uses' => 'GlobalScaleController@destroy', 'middleware' => 'roles', 'roles' => ['Admin', 'HQ']]);
Route::put('/global_scales/{global_scale}', ['uses' => 'GlobalScaleController@update', 'middleware' => 'roles', 'roles' => ['Admin', 'HQ', 'Finance', 'CustomerService', 'CS', 'Operation', 'OS', 'Agent']]);
Route::get('/global_scales/{global_scale}', ['uses' => 'GlobalScaleController@show', 'middleware' => 'roles', 'roles' => ['Admin', 'HQ', 'Finance', 'CustomerService', 'CS', 'Operation', 'OS', 'Agent']]);

Route::get('/payment_types', ['uses' => 'PaymentTypeController@index', 'middleware' => 'roles', 'roles' => ['Admin', 'HQ', 'Finance', 'CustomerService', 'CS', 'Operation', 'OS', 'Agent']]);
Route::post('/payment_types', ['uses' => 'PaymentTypeController@store', 'middleware' => 'roles', 'roles' => ['Admin', 'HQ', 'Finance', 'CustomerService', 'CS', 'Operation', 'OS', 'Agent']]);
Route::delete('/payment_types/{payment_type}', ['uses' => 'PaymentTypeController@destroy', 'middleware' => 'roles', 'roles' => ['Admin', 'HQ']]);
Route::put('/payment_types/{payment_type}', ['uses' => 'PaymentTypeController@update', 'middleware' => 'roles', 'roles' => ['Admin', 'HQ', 'Finance', 'CustomerService', 'CS', 'Operation', 'OS', 'Agent']]);
Route::get('/payment_types/{payment_type}', ['uses' => 'PaymentTypeController@show', 'middleware' => 'roles', 'roles' => ['Admin', 'HQ', 'Finance', 'CustomerService', 'CS', 'Operation', 'OS', 'Agent']]);

Route::get('/payment_statuses', ['uses' => 'PaymentStatusController@index', 'middleware' => 'roles', 'roles' => ['Admin', 'HQ', 'Finance', 'CustomerService', 'CS', 'Operation', 'OS', 'Agent']]);
Route::post('/payment_statuses', ['uses' => 'PaymentStatusController@store', 'middleware' => 'roles', 'roles' => ['Admin', 'HQ', 'Finance', 'CustomerService', 'CS', 'Operation', 'OS', 'Agent']]);
Route::delete('/payment_statuses/{payment_status}', ['uses' => 'PaymentStatusController@destroy', 'middleware' => 'roles', 'roles' => ['Admin', 'HQ']]);
Route::put('/payment_statuses/{payment_status}', ['uses' => 'PaymentStatusController@update', 'middleware' => 'roles', 'roles' => ['Admin', 'HQ', 'Finance', 'CustomerService', 'CS', 'Operation', 'OS', 'Agent']]);
Route::get('/payment_statuses/{payment_status}', ['uses' => 'PaymentStatusController@show', 'middleware' => 'roles', 'roles' => ['Admin', 'HQ', 'Finance', 'CustomerService', 'CS', 'Operation', 'OS', 'Agent']]);

Route::get('/merchant_discounts', ['uses' => 'MerchantDiscountController@index', 'middleware' => 'roles', 'roles' => ['Admin', 'HQ', 'Finance', 'CustomerService', 'CS', 'Operation', 'OS', 'Agent']]);
Route::post('/merchant_discounts', ['uses' => 'MerchantDiscountController@store', 'middleware' => 'roles', 'roles' => ['Admin', 'HQ', 'Finance', 'CustomerService', 'CS', 'Operation', 'OS', 'Agent']]);
Route::delete('/merchant_discounts/{merchant_discount}', ['uses' => 'MerchantDiscountController@destroy', 'middleware' => 'roles', 'roles' => ['Admin', 'HQ']]);
Route::put('/merchant_discounts/{merchant_discount}', ['uses' => 'MerchantDiscountController@update', 'middleware' => 'roles', 'roles' => ['Admin', 'HQ', 'Finance', 'CustomerService', 'CS', 'Operation', 'OS', 'Agent']]);
Route::get('/merchant_discounts/{merchant_discount}', ['uses' => 'MerchantDiscountController@show', 'middleware' => 'roles', 'roles' => ['Admin', 'HQ', 'Finance', 'CustomerService', 'CS', 'Operation', 'OS', 'Agent']]);

Route::get('/coupons', ['uses' => 'CouponController@index', 'middleware' => 'roles', 'roles' => ['Admin', 'HQ', 'Finance', 'CustomerService', 'CS', 'Operation', 'OS', 'Agent']]);
Route::post('/coupons', ['uses' => 'CouponController@store', 'middleware' => 'roles', 'roles' => ['Admin', 'HQ', 'Finance', 'CustomerService', 'CS', 'Operation', 'OS', 'Agent']]);
Route::delete('/coupons/{coupon}', ['uses' => 'CouponController@destroy', 'middleware' => 'roles', 'roles' => ['Admin', 'HQ']]);
Route::put('/coupons/{coupon}', ['uses' => 'CouponController@update', 'middleware' => 'roles', 'roles' => ['Admin', 'HQ', 'Finance', 'CustomerService', 'CS', 'Operation', 'OS', 'Agent']]);
Route::get('/coupons/{coupon}', ['uses' => 'CouponController@show', 'middleware' => 'roles', 'roles' => ['Admin', 'HQ', 'Finance', 'CustomerService', 'CS', 'Operation', 'OS', 'Agent']]);
Route::get('/check_coupon_code', ['uses' => 'CouponController@check_coupon_code', 'middleware' => 'roles', 'roles' => ['Admin', 'HQ', 'Finance', 'CustomerService', 'CS', 'Operation', 'OS', 'Agent']]);

Route::get('/routes', ['uses' => 'RouteController@index', 'middleware' => 'roles', 'roles' => ['Admin', 'HQ', 'Finance', 'CustomerService', 'CS', 'Operation', 'OS', 'Agent']]);
Route::post('/routes', ['uses' => 'RouteController@store', 'middleware' => 'roles', 'roles' => ['Admin', 'HQ', 'Finance', 'CustomerService', 'CS', 'Operation', 'OS', 'Agent']]);
Route::delete('/routes/{route}', ['uses' => 'RouteController@destroy', 'middleware' => 'roles', 'roles' => ['Admin', 'HQ']]);
Route::put('/routes/{route}', ['uses' => 'RouteController@update', 'middleware' => 'roles', 'roles' => ['Admin', 'HQ', 'Finance', 'CustomerService', 'CS', 'Operation', 'OS', 'Agent']]);
Route::get('/routes/{route}', ['uses' => 'RouteController@show', 'middleware' => 'roles', 'roles' => ['Admin', 'HQ', 'Finance', 'CustomerService', 'CS', 'Operation', 'OS', 'Agent']]);

Route::get('/delegate_durations', ['uses' => 'DelegateDurationController@index', 'middleware' => 'roles', 'roles' => ['Admin', 'HQ', 'Finance', 'CustomerService', 'CS', 'Operation', 'OS', 'Agent']]);
Route::post('/delegate_durations', ['uses' => 'DelegateDurationController@store', 'middleware' => 'roles', 'roles' => ['Admin', 'HQ', 'Finance', 'CustomerService', 'CS', 'Operation', 'OS', 'Agent']]);
Route::delete('/delegate_durations/{delegate_duration}', ['uses' => 'DelegateDurationController@destroy', 'middleware' => 'roles', 'roles' => ['Admin', 'HQ']]);
Route::put('/delegate_durations/{delegate_duration}', ['uses' => 'DelegateDurationController@update', 'middleware' => 'roles', 'roles' => ['Admin', 'HQ', 'Finance', 'CustomerService', 'CS', 'Operation', 'OS', 'Agent']]);
Route::get('/delegate_durations/{delegate_duration}', ['uses' => 'DelegateDurationController@show', 'middleware' => 'roles', 'roles' => ['Admin', 'HQ', 'Finance', 'CustomerService', 'CS', 'Operation', 'OS', 'Agent']]);

Route::get('/door_to_doors', ['uses' => 'DoorToDoorController@index', 'middleware' => 'roles', 'roles' => ['Admin', 'HQ', 'Finance', 'CustomerService', 'CS', 'Operation', 'OS', 'Agent']]);
Route::post('/door_to_doors', ['uses' => 'DoorToDoorController@store', 'middleware' => 'roles', 'roles' => ['Admin', 'HQ', 'Finance', 'CustomerService', 'CS', 'Operation', 'OS', 'Agent']]);
Route::post('/door_to_doors/create_all', ['uses' => 'DoorToDoorController@create_all', 'middleware' => 'roles', 'roles' => ['Admin', 'HQ', 'Finance', 'CustomerService', 'CS', 'Operation', 'OS', 'Agent']]);
Route::delete('/door_to_doors/{door_to_door}', ['uses' => 'DoorToDoorController@destroy', 'middleware' => 'roles', 'roles' => ['Admin', 'HQ']]);
Route::put('/door_to_doors/{door_to_door}', ['uses' => 'DoorToDoorController@update', 'middleware' => 'roles', 'roles' => ['Admin', 'HQ', 'Finance', 'CustomerService', 'CS', 'Operation', 'OS', 'Agent']]);
Route::get('/door_to_doors/{door_to_door}', ['uses' => 'DoorToDoorController@show', 'middleware' => 'roles', 'roles' => ['Admin', 'HQ', 'Finance', 'CustomerService', 'CS', 'Operation', 'OS', 'Agent']]);

Route::get('/bus_drop_offs', ['uses' => 'BusDropOffController@index', 'middleware' => 'roles', 'roles' => ['Admin', 'HQ', 'Finance', 'CustomerService', 'CS', 'Operation', 'OS', 'Agent']]);
Route::post('/bus_drop_offs', ['uses' => 'BusDropOffController@store', 'middleware' => 'roles', 'roles' => ['Admin', 'HQ', 'Finance', 'CustomerService', 'CS', 'Operation', 'OS', 'Agent']]);
Route::delete('/bus_drop_offs/{bus_drop_off}', ['uses' => 'BusDropOffController@destroy', 'middleware' => 'roles', 'roles' => ['Admin', 'HQ']]);
Route::put('/bus_drop_offs/{bus_drop_off}', ['uses' => 'BusDropOffController@update', 'middleware' => 'roles', 'roles' => ['Admin', 'HQ', 'Finance', 'CustomerService', 'CS', 'Operation', 'OS', 'Agent']]);
Route::get('/bus_drop_offs/{bus_drop_off}', ['uses' => 'BusDropOffController@show', 'middleware' => 'roles', 'roles' => ['Admin', 'HQ', 'Finance', 'CustomerService', 'CS', 'Operation', 'OS', 'Agent']]);

Route::get('/agents', ['uses' => 'AgentController@index', 'middleware' => 'roles', 'roles' => ['Admin', 'HQ', 'Finance', 'CustomerService', 'CS', 'Operation', 'OS', 'Agent']]);
Route::post('/agents', ['uses' => 'AgentController@store', 'middleware' => 'roles', 'roles' => ['Admin', 'HQ', 'Finance', 'CustomerService', 'CS', 'Operation', 'OS', 'Agent']]);
Route::delete('/agents/{agent}', ['uses' => 'AgentController@destroy', 'middleware' => 'roles', 'roles' => ['Admin', 'HQ']]);
Route::put('/agents/{agent}', ['uses' => 'AgentController@update', 'middleware' => 'roles', 'roles' => ['Admin', 'HQ', 'Finance', 'CustomerService', 'CS', 'Operation', 'OS', 'Agent']]);
Route::get('/agents/{agent}', ['uses' => 'AgentController@show', 'middleware' => 'roles', 'roles' => ['Admin', 'HQ', 'Finance', 'CustomerService', 'CS', 'Operation', 'OS', 'Agent']]);

Route::get('/account_informations', ['uses' => 'AccountInformationController@index', 'middleware' => 'roles', 'roles' => ['Admin', 'HQ', 'Finance', 'CustomerService', 'CS', 'Operation', 'OS', 'Agent']]);
Route::post('/account_informations', ['uses' => 'AccountInformationController@store', 'middleware' => 'roles', 'roles' => ['Admin', 'HQ', 'Finance', 'CustomerService', 'CS', 'Operation', 'OS', 'Agent']]);
Route::delete('/account_informations/{account_information}', ['uses' => 'AccountInformationController@destroy', 'middleware' => 'roles', 'roles' => ['Admin', 'HQ']]);
Route::put('/account_informations/{account_information}', ['uses' => 'AccountInformationController@update', 'middleware' => 'roles', 'roles' => ['Admin', 'HQ', 'Finance', 'CustomerService', 'CS', 'Operation', 'OS', 'Agent']]);
Route::get('/account_informations/{account_information}', ['uses' => 'AccountInformationController@show', 'middleware' => 'roles', 'roles' => ['Admin', 'HQ', 'Finance', 'CustomerService', 'CS', 'Operation', 'OS', 'Agent']]);

Route::get('/failure_statuses', ['uses' => 'FailureStatusController@index', 'middleware' => 'roles', 'roles' => ['Admin', 'HQ', 'Finance', 'CustomerService', 'CS', 'Operation', 'OS', 'Agent']]);
Route::post('/failure_statuses', ['uses' => 'FailureStatusController@store', 'middleware' => 'roles', 'roles' => ['Admin', 'HQ', 'Finance', 'CustomerService', 'CS', 'Operation', 'OS', 'Agent']]);
Route::delete('/failure_statuses/{failure_status}', ['uses' => 'FailureStatusController@destroy', 'middleware' => 'roles', 'roles' => ['Admin', 'HQ']]);
Route::put('/failure_statuses/{failure_status}', ['uses' => 'FailureStatusController@update', 'middleware' => 'roles', 'roles' => ['Admin', 'HQ', 'Finance', 'CustomerService', 'CS', 'Operation', 'OS', 'Agent']]);
Route::get('/failure_statuses/{failure_status}', ['uses' => 'FailureStatusController@show', 'middleware' => 'roles', 'roles' => ['Admin', 'HQ', 'Finance', 'CustomerService', 'CS', 'Operation', 'OS', 'Agent']]);

Route::get('/agent_badges', ['uses' => 'AgentBadgeController@index', 'middleware' => 'roles', 'roles' => ['Admin', 'HQ', 'Finance', 'CustomerService', 'CS', 'Operation', 'OS', 'Agent']]);
Route::post('/agent_badges', ['uses' => 'AgentBadgeController@store', 'middleware' => 'roles', 'roles' => ['Admin', 'HQ', 'Finance', 'CustomerService', 'CS', 'Operation', 'OS', 'Agent']]);
Route::delete('/agent_badges/{agent_badge}', ['uses' => 'AgentBadgeController@destroy', 'middleware' => 'roles', 'roles' => ['Admin', 'HQ']]);
Route::put('/agent_badges/{agent_badge}', ['uses' => 'AgentBadgeController@update', 'middleware' => 'roles', 'roles' => ['Admin', 'HQ', 'Finance', 'CustomerService', 'CS', 'Operation', 'OS', 'Agent']]);
Route::get('/agent_badges/{agent_badge}', ['uses' => 'AgentBadgeController@show', 'middleware' => 'roles', 'roles' => ['Admin', 'HQ', 'Finance', 'CustomerService', 'CS', 'Operation', 'OS', 'Agent']]);

Route::get('/banks', ['uses' => 'BankController@index', 'middleware' => 'roles', 'roles' => ['Admin', 'HQ', 'Finance', 'CustomerService', 'CS', 'Operation', 'OS', 'Agent']]);
Route::post('/banks', ['uses' => 'BankController@store', 'middleware' => 'roles', 'roles' => ['Admin', 'HQ', 'Finance', 'CustomerService', 'CS', 'Operation', 'OS', 'Agent']]);
Route::delete('/banks/{bank}', ['uses' => 'BankController@destroy', 'middleware' => 'roles', 'roles' => ['Admin', 'HQ']]);
Route::put('/banks/{bank}', ['uses' => 'BankController@update', 'middleware' => 'roles', 'roles' => ['Admin', 'HQ', 'Finance', 'CustomerService', 'CS', 'Operation', 'OS', 'Agent']]);
Route::get('/banks/{bank}', ['uses' => 'BankController@show', 'middleware' => 'roles', 'roles' => ['Admin', 'HQ', 'Finance', 'CustomerService', 'CS', 'Operation', 'OS', 'Agent']]);

Route::get('/hero_badges', ['uses' => 'HeroBadgeController@index', 'middleware' => 'roles', 'roles' => ['Admin', 'HQ', 'Finance', 'CustomerService', 'CS', 'Operation', 'OS', 'Agent']]);
Route::post('/hero_badges', ['uses' => 'HeroBadgeController@store', 'middleware' => 'roles', 'roles' => ['Admin', 'HQ', 'Finance', 'CustomerService', 'CS', 'Operation', 'OS', 'Agent']]);
Route::delete('/hero_badges/{hero_badge}', ['uses' => 'HeroBadgeController@destroy', 'middleware' => 'roles', 'roles' => ['Admin', 'HQ']]);
Route::put('/hero_badges/{hero_badge}', ['uses' => 'HeroBadgeController@update', 'middleware' => 'roles', 'roles' => ['Admin', 'HQ', 'Finance', 'CustomerService', 'CS', 'Operation', 'OS', 'Agent']]);
Route::get('/hero_badges/{hero_badge}', ['uses' => 'HeroBadgeController@show', 'middleware' => 'roles', 'roles' => ['Admin', 'HQ', 'Finance', 'CustomerService', 'CS', 'Operation', 'OS', 'Agent']]);

Route::get('/deductions', ['uses' => 'DeductionController@index', 'middleware' => 'roles', 'roles' => ['Admin', 'HQ', 'Finance', 'CustomerService', 'CS', 'Operation', 'OS', 'Agent']]);
Route::post('/deductions', ['uses' => 'DeductionController@store', 'middleware' => 'roles', 'roles' => ['Admin', 'HQ', 'Finance', 'CustomerService', 'CS', 'Operation', 'OS', 'Agent']]);
Route::delete('/deductions/{deduction}', ['uses' => 'DeductionController@destroy', 'middleware' => 'roles', 'roles' => ['Admin', 'HQ']]);
Route::put('/deductions/{deduction}', ['uses' => 'DeductionController@update', 'middleware' => 'roles', 'roles' => ['Admin', 'HQ', 'Finance', 'CustomerService', 'CS', 'Operation', 'OS', 'Agent']]);
Route::get('/deductions/{deduction}', ['uses' => 'DeductionController@show', 'middleware' => 'roles', 'roles' => ['Admin', 'HQ', 'Finance', 'CustomerService', 'CS', 'Operation', 'OS', 'Agent']]);

Route::get('/product_types', ['uses' => 'ProductTypeController@index', 'middleware' => 'roles', 'roles' => ['Admin', 'HQ', 'Finance', 'CustomerService', 'CS', 'Operation', 'OS', 'Agent']]);
Route::post('/product_types', ['uses' => 'ProductTypeController@store', 'middleware' => 'roles', 'roles' => ['Admin', 'HQ', 'Finance', 'CustomerService', 'CS', 'Operation', 'OS', 'Agent']]);
Route::delete('/product_types/{product_type}', ['uses' => 'ProductTypeController@destroy', 'middleware' => 'roles', 'roles' => ['Admin', 'HQ']]);
Route::put('/product_types/{product_type}', ['uses' => 'ProductTypeController@update', 'middleware' => 'roles', 'roles' => ['Admin', 'HQ', 'Finance', 'CustomerService', 'CS', 'Operation', 'OS', 'Agent']]);
Route::get('/product_types/{product_type}', ['uses' => 'ProductTypeController@show', 'middleware' => 'roles', 'roles' => ['Admin', 'HQ', 'Finance', 'CustomerService', 'CS', 'Operation', 'OS', 'Agent']]);

Route::get('/products', ['uses' => 'ProductController@index', 'middleware' => 'roles', 'roles' => ['Admin', 'HQ', 'Finance', 'CustomerService', 'CS', 'Operation', 'OS', 'Agent']]);
Route::post('/products', ['uses' => 'ProductController@store', 'middleware' => 'roles', 'roles' => ['Admin', 'HQ', 'Finance', 'CustomerService', 'CS', 'Operation', 'OS', 'Agent']]);
Route::delete('/products/{product}', ['uses' => 'ProductController@destroy', 'middleware' => 'roles', 'roles' => ['Admin', 'HQ']]);
Route::put('/products/{product}', ['uses' => 'ProductController@update', 'middleware' => 'roles', 'roles' => ['Admin', 'HQ', 'Finance', 'CustomerService', 'CS', 'Operation', 'OS', 'Agent']]);
Route::get('/products/{product}', ['uses' => 'ProductController@show', 'middleware' => 'roles', 'roles' => ['Admin', 'HQ', 'Finance', 'CustomerService', 'CS', 'Operation', 'OS', 'Agent']]);

Route::get('/tags', ['uses' => 'TagController@index', 'middleware' => 'roles', 'roles' => ['Admin', 'HQ', 'Finance', 'CustomerService', 'CS', 'Operation', 'OS', 'Agent']]);
Route::post('/tags', ['uses' => 'TagController@store', 'middleware' => 'roles', 'roles' => ['Admin', 'HQ', 'Finance', 'CustomerService', 'CS', 'Operation', 'OS', 'Agent']]);
Route::delete('/tags/{tag}', ['uses' => 'TagController@destroy', 'middleware' => 'roles', 'roles' => ['Admin', 'HQ']]);
Route::put('/tags/{tag}', ['uses' => 'TagController@update', 'middleware' => 'roles', 'roles' => ['Admin', 'HQ', 'Finance', 'CustomerService', 'CS', 'Operation', 'OS', 'Agent']]);
Route::get('/tags/{tag}', ['uses' => 'TagController@show', 'middleware' => 'roles', 'roles' => ['Admin', 'HQ', 'Finance', 'CustomerService', 'CS', 'Operation', 'OS', 'Agent']]);

Route::get('/product_tags', ['uses' => 'ProductTagController@index', 'middleware' => 'roles', 'roles' => ['Admin', 'HQ', 'Finance', 'CustomerService', 'CS', 'Operation', 'OS', 'Agent']]);
Route::post('/product_tags', ['uses' => 'ProductTagController@store', 'middleware' => 'roles', 'roles' => ['Admin', 'HQ', 'Finance', 'CustomerService', 'CS', 'Operation', 'OS', 'Agent']]);
Route::delete('/product_tags/{product_tag}', ['uses' => 'ProductTagController@destroy', 'middleware' => 'roles', 'roles' => ['Admin', 'HQ']]);
Route::put('/product_tags/{product_tag}', ['uses' => 'ProductTagController@update', 'middleware' => 'roles', 'roles' => ['Admin', 'HQ', 'Finance', 'CustomerService', 'CS', 'Operation', 'OS', 'Agent']]);
Route::get('/product_tags/{product_tag}', ['uses' => 'ProductTagController@show', 'middleware' => 'roles', 'roles' => ['Admin', 'HQ', 'Finance', 'CustomerService', 'CS', 'Operation', 'OS', 'Agent']]);

// Route::get('/inventories', ['uses' => 'InventoryController@index', 'middleware' => 'roles', 'roles' => ['Admin', 'HQ', 'Finance', 'CustomerService', 'CS', 'Operation', 'OS', 'Agent']]);
// Route::post('/inventories', ['uses' => 'InventoryController@store', 'middleware' => 'roles', 'roles' => ['Admin', 'HQ', 'Finance', 'CustomerService', 'CS', 'Operation', 'OS', 'Agent']]);
// Route::delete('/inventories/{inventory}', ['uses' => 'InventoryController@destroy', 'middleware' => 'roles', 'roles' => ['Admin', 'HQ']]);
// Route::put('/inventories/{inventory}', ['uses' => 'InventoryController@update', 'middleware' => 'roles', 'roles' => ['Admin', 'HQ', 'Finance', 'CustomerService', 'CS', 'Operation', 'OS', 'Agent']]);
// Route::get('/inventories/{inventory}', ['uses' => 'InventoryController@show', 'middleware' => 'roles', 'roles' => ['Admin', 'HQ', 'Finance', 'CustomerService', 'CS', 'Operation', 'OS', 'Agent']]);

Route::get('/finance_account_types', ['uses' => 'FinanceAccountTypeController@index', 'middleware' => 'roles', 'roles' => ['Admin', 'HQ', 'Finance', 'CustomerService', 'CS', 'Operation', 'OS', 'Agent']]);
Route::post('/finance_account_types', ['uses' => 'FinanceAccountTypeController@store', 'middleware' => 'roles', 'roles' => ['Admin', 'HQ', 'Finance', 'CustomerService', 'CS', 'Operation', 'OS', 'Agent']]);
Route::delete('/finance_account_types/{finance_account_type}', ['uses' => 'FinanceAccountTypeController@destroy', 'middleware' => 'roles', 'roles' => ['Admin', 'HQ']]);
Route::put('/finance_account_types/{finance_account_type}', ['uses' => 'FinanceAccountTypeController@update', 'middleware' => 'roles', 'roles' => ['Admin', 'HQ', 'Finance', 'CustomerService', 'CS', 'Operation', 'OS', 'Agent']]);
Route::get('/finance_account_types/{finance_account_type}', ['uses' => 'FinanceAccountTypeController@show', 'middleware' => 'roles', 'roles' => ['Admin', 'HQ', 'Finance', 'CustomerService', 'CS', 'Operation', 'OS', 'Agent']]);

Route::get('/finance_master_types', ['uses' => 'FinanceMasterTypeController@index', 'middleware' => 'roles', 'roles' => ['Admin', 'HQ', 'Finance', 'CustomerService', 'CS', 'Operation', 'OS', 'Agent']]);
Route::post('/finance_master_types', ['uses' => 'FinanceMasterTypeController@store', 'middleware' => 'roles', 'roles' => ['Admin', 'HQ', 'Finance', 'CustomerService', 'CS', 'Operation', 'OS', 'Agent']]);
Route::delete('/finance_master_types/{finance_master_type}', ['uses' => 'FinanceMasterTypeController@destroy', 'middleware' => 'roles', 'roles' => ['Admin', 'HQ']]);
Route::put('/finance_master_types/{finance_master_type}', ['uses' => 'FinanceMasterTypeController@update', 'middleware' => 'roles', 'roles' => ['Admin', 'HQ', 'Finance', 'CustomerService', 'CS', 'Operation', 'OS', 'Agent']]);
Route::get('/finance_master_types/{finance_master_type}', ['uses' => 'FinanceMasterTypeController@show', 'middleware' => 'roles', 'roles' => ['Admin', 'HQ', 'Finance', 'CustomerService', 'CS', 'Operation', 'OS', 'Agent']]);

Route::get('/finance_groups', ['uses' => 'FinanceGroupController@index', 'middleware' => 'roles', 'roles' => ['Admin', 'HQ', 'Finance', 'CustomerService', 'CS', 'Operation', 'OS', 'Agent']]);
Route::post('/finance_groups', ['uses' => 'FinanceGroupController@store', 'middleware' => 'roles', 'roles' => ['Admin', 'HQ', 'Finance', 'CustomerService', 'CS', 'Operation', 'OS', 'Agent']]);
Route::delete('/finance_groups/{finance_group}', ['uses' => 'FinanceGroupController@destroy', 'middleware' => 'roles', 'roles' => ['Admin', 'HQ']]);
Route::put('/finance_groups/{finance_group}', ['uses' => 'FinanceGroupController@update', 'middleware' => 'roles', 'roles' => ['Admin', 'HQ', 'Finance', 'CustomerService', 'CS', 'Operation', 'OS', 'Agent']]);
Route::get('/finance_groups/{finance_group}', ['uses' => 'FinanceGroupController@show', 'middleware' => 'roles', 'roles' => ['Admin', 'HQ', 'Finance', 'CustomerService', 'CS', 'Operation', 'OS', 'Agent']]);

Route::get('/finance_natures', ['uses' => 'FinanceNatureController@index', 'middleware' => 'roles', 'roles' => ['Admin', 'HQ', 'Finance', 'CustomerService', 'CS', 'Operation', 'OS', 'Agent']]);
Route::post('/finance_natures', ['uses' => 'FinanceNatureController@store', 'middleware' => 'roles', 'roles' => ['Admin', 'HQ', 'Finance', 'CustomerService', 'CS', 'Operation', 'OS', 'Agent']]);
Route::delete('/finance_natures/{finance_nature}', ['uses' => 'FinanceNatureController@destroy', 'middleware' => 'roles', 'roles' => ['Admin', 'HQ']]);
Route::put('/finance_natures/{finance_nature}', ['uses' => 'FinanceNatureController@update', 'middleware' => 'roles', 'roles' => ['Admin', 'HQ', 'Finance', 'CustomerService', 'CS', 'Operation', 'OS', 'Agent']]);
Route::get('/finance_natures/{finance_nature}', ['uses' => 'FinanceNatureController@show', 'middleware' => 'roles', 'roles' => ['Admin', 'HQ', 'Finance', 'CustomerService', 'CS', 'Operation', 'OS', 'Agent']]);

Route::get('/finance_taxes', ['uses' => 'FinanceTaxController@index', 'middleware' => 'roles', 'roles' => ['Admin', 'HQ', 'Finance', 'CustomerService', 'CS', 'Operation', 'OS', 'Agent']]);
Route::post('/finance_taxes', ['uses' => 'FinanceTaxController@store', 'middleware' => 'roles', 'roles' => ['Admin', 'HQ', 'Finance', 'CustomerService', 'CS', 'Operation', 'OS', 'Agent']]);
Route::delete('/finance_taxes/{finance_tax}', ['uses' => 'FinanceTaxController@destroy', 'middleware' => 'roles', 'roles' => ['Admin', 'HQ']]);
Route::put('/finance_taxes/{finance_tax}', ['uses' => 'FinanceTaxController@update', 'middleware' => 'roles', 'roles' => ['Admin', 'HQ', 'Finance', 'CustomerService', 'CS', 'Operation', 'OS', 'Agent']]);
Route::get('/finance_taxes/{finance_tax}', ['uses' => 'FinanceTaxController@show', 'middleware' => 'roles', 'roles' => ['Admin', 'HQ', 'Finance', 'CustomerService', 'CS', 'Operation', 'OS', 'Agent']]);

Route::get('/finance_codes', ['uses' => 'FinanceCodeController@index', 'middleware' => 'roles', 'roles' => ['Admin', 'HQ', 'Finance', 'CustomerService', 'CS', 'Operation', 'OS', 'Agent']]);
Route::post('/finance_codes', ['uses' => 'FinanceCodeController@store', 'middleware' => 'roles', 'roles' => ['Admin', 'HQ', 'Finance', 'CustomerService', 'CS', 'Operation', 'OS', 'Agent']]);
Route::delete('/finance_codes/{finance_code}', ['uses' => 'FinanceCodeController@destroy', 'middleware' => 'roles', 'roles' => ['Admin', 'HQ']]);
Route::put('/finance_codes/{finance_code}', ['uses' => 'FinanceCodeController@update', 'middleware' => 'roles', 'roles' => ['Admin', 'HQ', 'Finance', 'CustomerService', 'CS', 'Operation', 'OS', 'Agent']]);
Route::get('/finance_codes/{finance_code}', ['uses' => 'FinanceCodeController@show', 'middleware' => 'roles', 'roles' => ['Admin', 'HQ', 'Finance', 'CustomerService', 'CS', 'Operation', 'OS', 'Agent']]);

Route::get('/finance_accounts', ['uses' => 'FinanceAccountController@index', 'middleware' => 'roles', 'roles' => ['Admin', 'HQ', 'Finance', 'CustomerService', 'CS', 'Operation', 'OS', 'Agent']]);
Route::post('/finance_accounts', ['uses' => 'FinanceAccountController@store', 'middleware' => 'roles', 'roles' => ['Admin', 'HQ', 'Finance', 'CustomerService', 'CS', 'Operation', 'OS', 'Agent']]);
Route::delete('/finance_accounts/{finance_account}', ['uses' => 'FinanceAccountController@destroy', 'middleware' => 'roles', 'roles' => ['Admin', 'HQ']]);
Route::put('/finance_accounts/{finance_account}', ['uses' => 'FinanceAccountController@update', 'middleware' => 'roles', 'roles' => ['Admin', 'HQ', 'Finance', 'CustomerService', 'CS', 'Operation', 'OS', 'Agent']]);
Route::get('/finance_accounts/{finance_account}', ['uses' => 'FinanceAccountController@show', 'middleware' => 'roles', 'roles' => ['Admin', 'HQ', 'Finance', 'CustomerService', 'CS', 'Operation', 'OS', 'Agent']]);

Route::get('/finance_asset_types', ['uses' => 'FinanceAssetTypeController@index', 'middleware' => 'roles', 'roles' => ['Admin', 'HQ', 'Finance', 'CustomerService', 'CS', 'Operation', 'OS', 'Agent']]);
Route::post('/finance_asset_types', ['uses' => 'FinanceAssetTypeController@store', 'middleware' => 'roles', 'roles' => ['Admin', 'HQ', 'Finance', 'CustomerService', 'CS', 'Operation', 'OS', 'Agent']]);
Route::delete('/finance_asset_types/{finance_asset_type}', ['uses' => 'FinanceAssetTypeController@destroy', 'middleware' => 'roles', 'roles' => ['Admin', 'HQ']]);
Route::put('/finance_asset_types/{finance_asset_type}', ['uses' => 'FinanceAssetTypeController@update', 'middleware' => 'roles', 'roles' => ['Admin', 'HQ', 'Finance', 'CustomerService', 'CS', 'Operation', 'OS', 'Agent']]);
Route::get('/finance_asset_types/{finance_asset_type}', ['uses' => 'FinanceAssetTypeController@show', 'middleware' => 'roles', 'roles' => ['Admin', 'HQ', 'Finance', 'CustomerService', 'CS', 'Operation', 'OS', 'Agent']]);

Route::get('/finance_assets', ['uses' => 'FinanceAssetController@index', 'middleware' => 'roles', 'roles' => ['Admin', 'HQ', 'Finance', 'CustomerService', 'CS', 'Operation', 'OS', 'Agent']]);
Route::post('/finance_assets', ['uses' => 'FinanceAssetController@store', 'middleware' => 'roles', 'roles' => ['Admin', 'HQ', 'Finance', 'CustomerService', 'CS', 'Operation', 'OS', 'Agent']]);
Route::delete('/finance_assets/{finance_asset}', ['uses' => 'FinanceAssetController@destroy', 'middleware' => 'roles', 'roles' => ['Admin', 'HQ']]);
Route::put('/finance_assets/{finance_asset}', ['uses' => 'FinanceAssetController@update', 'middleware' => 'roles', 'roles' => ['Admin', 'HQ', 'Finance', 'CustomerService', 'CS', 'Operation', 'OS', 'Agent']]);
Route::get('/finance_assets/{finance_asset}', ['uses' => 'FinanceAssetController@show', 'middleware' => 'roles', 'roles' => ['Admin', 'HQ', 'Finance', 'CustomerService', 'CS', 'Operation', 'OS', 'Agent']]);

Route::get('/finance_expenses', ['uses' => 'FinanceExpenseController@index', 'middleware' => 'roles', 'roles' => ['Admin', 'HQ', 'Finance', 'CustomerService', 'CS', 'Operation', 'OS', 'Agent']]);
Route::post('/finance_expenses', ['uses' => 'FinanceExpenseController@store', 'middleware' => 'roles', 'roles' => ['Admin', 'HQ', 'Finance', 'CustomerService', 'CS', 'Operation', 'OS', 'Agent']]);
Route::delete('/finance_expenses/{finance_expense}', ['uses' => 'FinanceExpenseController@destroy', 'middleware' => 'roles', 'roles' => ['Admin', 'HQ']]);
Route::put('/finance_expenses/{finance_expense}', ['uses' => 'FinanceExpenseController@update', 'middleware' => 'roles', 'roles' => ['Admin', 'HQ', 'Finance', 'CustomerService', 'CS', 'Operation', 'OS', 'Agent']]);
Route::get('/finance_expenses/{finance_expense}', ['uses' => 'FinanceExpenseController@show', 'middleware' => 'roles', 'roles' => ['Admin', 'HQ', 'Finance', 'CustomerService', 'CS', 'Operation', 'OS', 'Agent']]);
Route::post('/finance_expenses/{finance_expense}/upload', ['uses' => 'FinanceExpenseController@upload', 'middleware' => 'roles', 'roles' => ['Admin', 'HQ', 'Finance', 'CustomerService', 'CS', 'Operation', 'OS', 'Agent']]);

Route::get('/finance_expense_items', ['uses' => 'FinanceExpenseItemController@index', 'middleware' => 'roles', 'roles' => ['Admin', 'HQ', 'Finance', 'CustomerService', 'CS', 'Operation', 'OS', 'Agent']]);
Route::post('/finance_expense_items', ['uses' => 'FinanceExpenseItemController@store', 'middleware' => 'roles', 'roles' => ['Admin', 'HQ', 'Finance', 'CustomerService', 'CS', 'Operation', 'OS', 'Agent']]);
Route::delete('/finance_expense_items/{finance_expense_item}', ['uses' => 'FinanceExpenseItemController@destroy', 'middleware' => 'roles', 'roles' => ['Admin', 'HQ']]);
Route::put('/finance_expense_items/{finance_expense_item}', ['uses' => 'FinanceExpenseItemController@update', 'middleware' => 'roles', 'roles' => ['Admin', 'HQ', 'Finance', 'CustomerService', 'CS', 'Operation', 'OS', 'Agent']]);
Route::get('/finance_expense_items/{finance_expense_item}', ['uses' => 'FinanceExpenseItemController@show', 'middleware' => 'roles', 'roles' => ['Admin', 'HQ', 'Finance', 'CustomerService', 'CS', 'Operation', 'OS', 'Agent']]);

Route::get('/finance_advances', ['uses' => 'FinanceAdvanceController@index', 'middleware' => 'roles', 'roles' => ['Admin', 'HQ', 'Finance', 'CustomerService', 'CS', 'Operation', 'OS', 'Agent']]);
Route::post('/finance_advances', ['uses' => 'FinanceAdvanceController@store', 'middleware' => 'roles', 'roles' => ['Admin', 'HQ', 'Finance', 'CustomerService', 'CS', 'Operation', 'OS', 'Agent']]);
Route::delete('/finance_advances/{finance_advance}', ['uses' => 'FinanceAdvanceController@destroy', 'middleware' => 'roles', 'roles' => ['Admin', 'HQ']]);
Route::put('/finance_advances/{finance_advance}', ['uses' => 'FinanceAdvanceController@update', 'middleware' => 'roles', 'roles' => ['Admin', 'HQ', 'Finance', 'CustomerService', 'CS', 'Operation', 'OS', 'Agent']]);
Route::get('/finance_advances/{finance_advance}', ['uses' => 'FinanceAdvanceController@show', 'middleware' => 'roles', 'roles' => ['Admin', 'HQ', 'Finance', 'CustomerService', 'CS', 'Operation', 'OS', 'Agent']]);
Route::post('/finance_advances/{finance_advance}/upload', ['uses' => 'FinanceAdvanceController@upload', 'middleware' => 'roles', 'roles' => ['Admin', 'HQ', 'Finance', 'CustomerService', 'CS', 'Operation', 'OS', 'Agent']]);
Route::post('/finance_advances/{finance_advance}/confirm', ['uses' => 'FinanceAdvanceController@confirm', 'middleware' => 'roles', 'roles' => ['Admin', 'HQ', 'Finance', 'CustomerService', 'CS', 'Operation', 'OS', 'Agent']]);

Route::get('/finance_postings', ['uses' => 'FinancePostingController@index', 'middleware' => 'roles', 'roles' => ['Admin', 'HQ', 'Finance', 'CustomerService', 'CS', 'Operation', 'OS', 'Agent']]);
Route::post('/finance_postings', ['uses' => 'FinancePostingController@store', 'middleware' => 'roles', 'roles' => ['Admin', 'HQ', 'Finance', 'CustomerService', 'CS', 'Operation', 'OS', 'Agent']]);
Route::delete('/finance_postings/{finance_posting}', ['uses' => 'FinancePostingController@destroy', 'middleware' => 'roles', 'roles' => ['Admin', 'HQ']]);
Route::put('/finance_postings/{finance_posting}', ['uses' => 'FinancePostingController@update', 'middleware' => 'roles', 'roles' => ['Admin', 'HQ', 'Finance', 'CustomerService', 'CS', 'Operation', 'OS', 'Agent']]);
Route::get('/finance_postings/{finance_posting}', ['uses' => 'FinancePostingController@show', 'middleware' => 'roles', 'roles' => ['Admin', 'HQ', 'Finance', 'CustomerService', 'CS', 'Operation', 'OS', 'Agent']]);

Route::get('/finance_configs', ['uses' => 'FinanceConfigController@index', 'middleware' => 'roles', 'roles' => ['Admin', 'HQ', 'Finance', 'CustomerService', 'CS', 'Operation', 'OS', 'Agent']]);
Route::post('/finance_configs', ['uses' => 'FinanceConfigController@store', 'middleware' => 'roles', 'roles' => ['Admin', 'HQ', 'Finance', 'CustomerService', 'CS', 'Operation', 'OS', 'Agent']]);
Route::delete('/finance_configs/{finance_config}', ['uses' => 'FinanceConfigController@destroy', 'middleware' => 'roles', 'roles' => ['Admin', 'HQ']]);
Route::put('/finance_configs/{finance_config}', ['uses' => 'FinanceConfigController@update', 'middleware' => 'roles', 'roles' => ['Admin', 'HQ', 'Finance', 'CustomerService', 'CS', 'Operation', 'OS', 'Agent']]);
Route::get('/finance_configs/{finance_config}', ['uses' => 'FinanceConfigController@show', 'middleware' => 'roles', 'roles' => ['Admin', 'HQ', 'Finance', 'CustomerService', 'CS', 'Operation', 'OS', 'Agent']]);

Route::get('/finance_metas', ['uses' => 'FinanceMetaController@index', 'middleware' => 'roles', 'roles' => ['Admin', 'HQ', 'Finance', 'CustomerService', 'CS', 'Operation', 'OS', 'Agent']]);
Route::post('/finance_metas', ['uses' => 'FinanceMetaController@store', 'middleware' => 'roles', 'roles' => ['Admin', 'HQ', 'Finance', 'CustomerService', 'CS', 'Operation', 'OS', 'Agent']]);
Route::delete('/finance_metas/{finance_meta}', ['uses' => 'FinanceMetaController@destroy', 'middleware' => 'roles', 'roles' => ['Admin', 'HQ']]);
Route::put('/finance_metas/{finance_meta}', ['uses' => 'FinanceMetaController@update', 'middleware' => 'roles', 'roles' => ['Admin', 'HQ', 'Finance', 'CustomerService', 'CS', 'Operation', 'OS', 'Agent']]);
Route::get('/finance_metas/{finance_meta}', ['uses' => 'FinanceMetaController@show', 'middleware' => 'roles', 'roles' => ['Admin', 'HQ', 'Finance', 'CustomerService', 'CS', 'Operation', 'OS', 'Agent']]);

Route::get('/finance_table_of_authorities', ['uses' => 'FinanceTableOfAuthorityController@index', 'middleware' => 'roles', 'roles' => ['Admin', 'HQ', 'Finance', 'CustomerService', 'CS', 'Operation', 'OS', 'Agent']]);
Route::post('/finance_table_of_authorities', ['uses' => 'FinanceTableOfAuthorityController@store', 'middleware' => 'roles', 'roles' => ['Admin', 'HQ', 'Finance', 'CustomerService', 'CS', 'Operation', 'OS', 'Agent']]);
Route::delete('/finance_table_of_authorities/{finance_table_of_authority}', ['uses' => 'FinanceTableOfAuthorityController@destroy', 'middleware' => 'roles', 'roles' => ['Admin', 'HQ']]);
Route::put('/finance_table_of_authorities/{finance_table_of_authority}', ['uses' => 'FinanceTableOfAuthorityController@update', 'middleware' => 'roles', 'roles' => ['Admin', 'HQ', 'Finance', 'CustomerService', 'CS', 'Operation', 'OS', 'Agent']]);
Route::get('/finance_table_of_authorities/{finance_table_of_authority}', ['uses' => 'FinanceTableOfAuthorityController@show', 'middleware' => 'roles', 'roles' => ['Admin', 'HQ', 'Finance', 'CustomerService', 'CS', 'Operation', 'OS', 'Agent']]);

Route::get('/finance_petty_cashes', ['uses' => 'FinancePettyCashController@index', 'middleware' => 'roles', 'roles' => ['Admin', 'HQ', 'Finance', 'CustomerService', 'CS', 'Operation', 'OS', 'Agent']]);
Route::post('/finance_petty_cashes', ['uses' => 'FinancePettyCashController@store', 'middleware' => 'roles', 'roles' => ['Admin', 'HQ', 'Finance', 'CustomerService', 'CS', 'Operation', 'OS', 'Agent']]);
Route::delete('/finance_petty_cashes/{finance_petty_cash}', ['uses' => 'FinancePettyCashController@destroy', 'middleware' => 'roles', 'roles' => ['Admin', 'HQ']]);
Route::put('/finance_petty_cashes/{finance_petty_cash}', ['uses' => 'FinancePettyCashController@update', 'middleware' => 'roles', 'roles' => ['Admin', 'HQ', 'Finance', 'CustomerService', 'CS', 'Operation', 'OS', 'Agent']]);
Route::get('/finance_petty_cashes/{finance_petty_cash}', ['uses' => 'FinancePettyCashController@show', 'middleware' => 'roles', 'roles' => ['Admin', 'HQ', 'Finance', 'CustomerService', 'CS', 'Operation', 'OS', 'Agent']]);


Route::get('/merchant_rate_cards', ['uses' => 'MerchantRateCardController@index', 'middleware' => 'roles', 'roles' => ['Admin', 'HQ', 'Finance', 'CustomerService', 'CS', 'Operation', 'OS', 'Agent']]);
Route::post('/merchant_rate_cards', ['uses' => 'MerchantRateCardController@store', 'middleware' => 'roles', 'roles' => ['Admin', 'HQ', 'Finance', 'CustomerService', 'CS', 'Operation', 'OS', 'Agent']]);
Route::delete('/merchant_rate_cards/{merchant_rate_card}', ['uses' => 'MerchantRateCardController@destroy', 'middleware' => 'roles', 'roles' => ['Admin', 'HQ']]);
Route::put('/merchant_rate_cards/{merchant_rate_card}', ['uses' => 'MerchantRateCardController@update', 'middleware' => 'roles', 'roles' => ['Admin', 'HQ', 'Finance', 'CustomerService', 'CS', 'Operation', 'OS', 'Agent']]);
Route::get('/merchant_rate_cards/{merchant_rate_card}', ['uses' => 'MerchantRateCardController@show', 'middleware' => 'roles', 'roles' => ['Admin', 'HQ', 'Finance', 'CustomerService', 'CS', 'Operation', 'OS', 'Agent']]);

Route::post('/import_rate_card', ['uses' => 'MerchantRateCardController@import', 'middleware' => 'roles', 'roles' => ['Admin', 'HQ', 'Agent', 'Operation']]);