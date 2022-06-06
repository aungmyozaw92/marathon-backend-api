<?php

namespace App\Http\Controllers\Mobile\Api\v1\Operation;

use App\Models\Gate;
use App\Http\Controllers\Controller;
use App\Http\Requests\Mobile\Gate\GateByCityRequest;
use App\Http\Resources\Mobile\Operation\Gate\GateResource;
use App\Http\Resources\Mobile\Operation\Gate\GateCollection;
use App\Repositories\Mobile\Api\v1\Operation\GateRepository;

class GateController extends Controller
{
    /**
     * @var GateRepository
     */
    protected $gateRepository;

    /**
     * GateController constructor.
     *
     * @param GateRepository $gateRepository
     */
    public function __construct(GateRepository $gateRepository)
    {
        $this->gateRepository = $gateRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $gates = Gate::all();
        // $gates = Gate::with('bus_station')
        //             ->filter(request()->only([
        //                 'year', 'month', 'day', 'name', 'delivery_rate', 'bus_station_name', 'search'
        //                 ]))
        //             ->order(request()->only([
        //                 'sortBy', 'orderBy'
        //                 ]))
        //             ->get();

        return new GateCollection($gates);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Gate  $gate
     * @return \Illuminate\Http\Response
     */
    public function show(Gate $gate)
    {
        return new GateResource($gate);
    }

    public function gate_by_city(GateByCityRequest $request)
    {
        $gate = $this->gateRepository->getGateByCity($request->all());
        return new GateCollection($gate);
    }
}
