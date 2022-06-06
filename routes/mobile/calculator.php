<?php

Route::post('calculate_amount', 'CalculateAmountController@calculate_price');

/**
 * City Routes
 */
Route::apiResource('cities', 'CityController');
/**
 * Zone Routes
 */
Route::apiResource('zones', 'ZoneController');

/**
 * Gloabal Scale Routes
 */
Route::apiResource('/global_scales', 'GlobalScaleController')->only(['index', 'show']);
