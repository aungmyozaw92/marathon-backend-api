<?php

namespace App\Http\Controllers\Mobile\Api\v1\Operation;

use App\Models\GlobalScale;
use App\Http\Controllers\Controller;
use App\Http\Resources\GlobalScale\GlobalScaleResource;
use App\Http\Resources\GlobalScale\GlobalScaleCollection;

use App\Repositories\Mobile\Api\v1\Operation\GlobalScaleRepository;

class GlobalScaleController extends Controller
{
    /**
     * @var GlobalScaleRepository
     */
    protected $globalScaleRepository;

    /**
     * GlobalScaleController constructor.
     *
     * @param GlobalScaleRepository $globalScaleRepository
     */
    public function __construct(GlobalScaleRepository $globalScaleRepository)
    {
        $this->globalScaleRepository = $globalScaleRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $globalScales = $this->globalScaleRepository->all();
        //$globalScales = GlobalScale::all();

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
