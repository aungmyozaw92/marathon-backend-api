<?php

namespace App\Http\Controllers\Web\Api\v1\MerchantDashboard;


use App\Models\DiscountType;
use App\Http\Controllers\Controller;
use App\Http\Resources\DiscountType\DiscountTypeResource;
use App\Http\Resources\DiscountType\DiscountTypeCollection;

class DiscountTypeController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $discount_types = DiscountType::all();

        return new DiscountTypeCollection($discount_types);
    }

    

    /**
     * Display the specified resource.
     *
     * @param \App\DiscountType $discount_type
     *
     * @return \Illuminate\Http\Response
     */
    public function show(DiscountType $discount_type)
    {
        return new DiscountTypeResource($discount_type);
    }

}
