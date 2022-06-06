<?php

namespace App\Http\Controllers\Web\Api\v1;

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
        // $globalScales = $this->globalScaleRepository->all();
        $globalScales = GlobalScale::all();

        return new GlobalScaleCollection($globalScales);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CreateGlobalScaleRequest $request)
    {
        $globalScale = $this->globalScaleRepository->create($request->all());

        return new GlobalScaleResource($globalScale);
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

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\GlobalScale  $globalScale
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateGlobalScaleRequest $request, GlobalScale $globalScale)
    {
        $globalScale = $this->globalScaleRepository->update($globalScale, $request->all());

        return new GlobalScaleResource($globalScale);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\GlobalScale  $globalScale
     * @return \Illuminate\Http\Response
     */
    public function destroy(GlobalScale $globalScale)
    {
        $this->globalScaleRepository->destroy($globalScale);

        return response()->json(['status' => 1], Response::HTTP_OK);
    }
}
