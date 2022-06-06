<?php

namespace App\Http\Controllers\Web\Api\v1\MerchantDashboard;


use App\Models\CallStatus;
use App\Http\Controllers\Controller;
use App\Http\Resources\CallStatus\CallStatusResource;
use App\Http\Resources\CallStatus\CallStatusCollection;

class CallStatusController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // $callStatuses = $this->callStatusRepository->paginate(10);
        $callStatuses = CallStatus::all();

        return new CallStatusCollection($callStatuses);

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\CallStatus  $callStatus
     * @return \Illuminate\Http\Response
     */
    public function show(CallStatus $callStatus)
    {
        return new CallStatusResource($callStatus);
    }

}
