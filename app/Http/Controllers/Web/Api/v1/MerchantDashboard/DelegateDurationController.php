<?php

namespace App\Http\Controllers\Web\Api\v1\MerchantDashboard;

use App\Models\DelegateDuration;
use App\Http\Controllers\Controller;
use App\Http\Resources\DelegateDuration\DelegateDurationResource;
use App\Http\Resources\DelegateDuration\DelegateDurationCollection;

class DelegateDurationController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $delegate_durations = DelegateDuration::all();

        return new DelegateDurationCollection($delegate_durations);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\DelegateDuration  $delegate_duration
     * @return \Illuminate\Http\Response
     */
    public function show(DelegateDuration $delegate_duration)
    {
        return new DelegateDurationResource($delegate_duration);
    }

}
