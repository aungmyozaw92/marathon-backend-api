<?php

namespace App\Http\Controllers\Web\Api\v1\MerchantDashboard;

use App\Models\FailureStatus;
use App\Http\Controllers\Controller;
use App\Http\Resources\FailureStatus\FailureStatusResource;
use App\Http\Resources\FailureStatus\FailureStatusCollection;

class FailureStatusController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $failureStatuses = FailureStatus::all();

        return new FailureStatusCollection($failureStatuses);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\FailureStatus  $FailureStatus
     * @return \Illuminate\Http\Response
     */
    public function show(FailureStatus $failureStatus)
    {
        return new FailureStatusResource($failureStatus);
    }
}
