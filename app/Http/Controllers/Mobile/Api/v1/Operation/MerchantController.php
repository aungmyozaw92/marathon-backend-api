<?php

namespace App\Http\Controllers\Mobile\Api\v1\Operation;

use App\Models\Merchant;
use App\Http\Controllers\Controller;
use App\Http\Resources\Mobile\Operation\Merchant\MerchantResource;
use App\Http\Resources\Mobile\Operation\Merchant\MerchantCollection;

class MerchantController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $merchants = Merchant::with(
            'merchant_associates',
            'merchant_associates.phones',
            'merchant_associates.emails',
            'merchant_associates.city',
            'merchant_associates.zone',
            'city',
            'staff'
        )->filter(request()->only(['search']))
            ->get();

        return new MerchantCollection($merchants);
    }


    /**
     * Display the specified resource.
     *
     * @param  \App\Merchant  $merchant
     * @return \Illuminate\Http\Response
     */
    public function show(Merchant $merchant)
    {
        return new MerchantResource($merchant->load(
            'merchant_associates',
            'merchant_associates.phones',
            'merchant_associates.emails',
            'merchant_associates.city',
            'merchant_associates.zone',
            'city',
            'staff'
        ));
    }
}
