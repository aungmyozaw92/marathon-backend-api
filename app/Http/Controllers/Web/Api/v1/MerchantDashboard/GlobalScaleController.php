<?php

namespace App\Http\Controllers\Web\Api\v1\MerchantDashboard;


use App\Models\GlobalScale;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use App\Http\Resources\GlobalScale\GlobalScaleResource;
use App\Http\Resources\GlobalScale\GlobalScaleCollection;
use App\Http\Requests\GlobalScale\CreateGlobalScaleRequest;
use App\Http\Requests\GlobalScale\UpdateGlobalScaleRequest;
use App\Repositories\Web\Api\v1\GlobalScaleRepository;

class GlobalScaleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // $globalScales = $this->globalScaleRepository->all();
        $globalScales = GlobalScale::all();

        return new GlobalScaleCollection($globalScales);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\GlobalScale  $globalScale
     * @return \Illuminate\Http\Response
     */
    public function show(GlobalScale $globalScale)
    {
        return new GlobalScaleResource($globalScale);
    }

}
