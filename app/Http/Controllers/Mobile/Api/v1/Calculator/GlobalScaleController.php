<?php

namespace App\Http\Controllers\Mobile\Api\v1\Calculator;

use App\Models\GlobalScale;
use App\Http\Controllers\Controller;
use App\Http\Resources\Mobile\Calculator\GlobalScale\GlobalScaleResource;
use App\Http\Resources\Mobile\Calculator\GlobalScale\GlobalScaleCollection;

use App\Repositories\Mobile\Api\v1\Calculator\GlobalScaleRepository;

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
        $globalScales = GlobalScale::orderBy('id', 'asc')->get();
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
