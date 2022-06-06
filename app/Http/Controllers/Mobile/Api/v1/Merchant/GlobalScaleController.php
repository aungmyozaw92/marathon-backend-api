<?php

namespace App\Http\Controllers\Mobile\Api\v1\Merchant;

use App\Models\GlobalScale;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use App\Repositories\Mobile\Api\v1\GlobalScaleRepository;
use App\Http\Resources\Mobile\GlobalScale\GlobalScaleResource;
use App\Http\Resources\Mobile\GlobalScale\GlobalScaleCollection;

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
        $globalScales = GlobalScale::orderBy('id', 'ASC')->get();
        // $globalScales = $this->globalScaleRepository->all();

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
