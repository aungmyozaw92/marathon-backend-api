<?php

namespace App\Http\Controllers\Web\Api\v1\MerchantDashboard;

use App\Models\LogStatus;
use App\Http\Controllers\Controller;
use App\Http\Resources\LogStatus\LogStatusResource;
use App\Http\Resources\LogStatus\LogStatusCollection;

class LogStatusController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $logStatuses = LogStatus::all();

        return new LogStatusCollection($logStatuses);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\LogStatus  $logStatus
     * @return \Illuminate\Http\Response
     */
    public function show(LogStatus $logStatus)
    {
        return new LogStatusResource($logStatus);
    }

    
}
