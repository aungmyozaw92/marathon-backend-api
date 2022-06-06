<?php

namespace App\Http\Controllers\Web\Api\v1\MerchantDashboard;

use App\Models\DeliveryStatus;
use App\Http\Controllers\Controller;
use App\Http\Resources\DeliveryStatus\DeliveryStatusResource;
use App\Http\Resources\DeliveryStatus\DeliveryStatusCollection;

class DeliveryStatusController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // $deliveryStatuses = $this->deliveryStatuRepository->paginate(10);
        $deliveryStatuses = DeliveryStatus::all();

        return new DeliveryStatusCollection($deliveryStatuses);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\DeliveryStatus  $deliveryStatus
     * @return \Illuminate\Http\Response
     */
    public function show(DeliveryStatus $deliveryStatus)
    {
        return new DeliveryStatusResource($deliveryStatus);
    }
}
