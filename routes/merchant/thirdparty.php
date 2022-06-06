<?php

Route::post('auth/login', 'Auth\AuthController@login');

Route::group(['middleware' => ['jwt.verify', 'jwt.verify:merchant']], function () {
    // Route::get('auth/logout', 'Auth\AuthController@logout');
    Route::get('auth/profile', 'Auth\AuthController@profile');
    Route::post('auth/profile', 'Auth\AuthController@update_profile');
    Route::post('calculate_amount', 'HomeController@calculate_price');
    Route::post('calculate_amount_detail', 'HomeController@calculate_price_detail');
    Route::post('check_route', 'HomeController@check_route');
    Route::get('get_data', 'HomeController@get_master_records');

    Route::apiResource('products', 'ProductController');
    Route::post('products/{product}/upload', 'ProductController@upload');
    Route::delete('products/{product}/attachments/{attachment}', 'ProductController@delete_file');
    Route::apiResource('product_types', 'ProductTypeController');
    Route::apiResource('tags', 'TagController');
    Route::apiResource('product_tags', 'ProductTagController')->only(['store', 'destroy']);
    Route::apiResource('inventories', 'InventoryController')->only(['index', 'store', 'update', 'show']);
    Route::post('inventories/{inventory}/add_qty', 'InventoryController@add_qty');
    Route::apiResource('variation_metas', 'VariationMetaController');
    Route::apiResource('product_variations', 'ProductVariationController')->only(['store', 'destroy']);
    Route::apiResource('product_reviews', 'ProductReviewController')->only(['store', 'show','update','destroy']);
    Route::apiResource('product_discounts', 'ProductDiscountController');

    Route::get('/latest_tracking_vouchers', 'VoucherController@get_latest_tracking_vouchers');
    /**
     * Voucher Routes
     */
    Route::apiResource('vouchers', 'VoucherController')->only(['index', 'store', 'show']);
    Route::apiResource('transactions', 'TransactionController')->only(['index']);
    Route::get('transaction_histories', 'TransactionController@transaction_lists');
    
    Route::apiResource('pickups', 'PickupController')->only(['index', 'store', 'show']);
    Route::post('pickups/{pickup}/add_vouchers', 'PickupController@add_voucher');
    Route::post('pickups/{pickup}/remove_vouchers', 'PickupController@remove_voucher');
    Route::get('vouchers/{voucher}/tracking_status', 'VoucherController@voucher_trackings');
    
});
