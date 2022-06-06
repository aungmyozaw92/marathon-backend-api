<?php

namespace App\Http\Controllers\Mobile\Api\v1\Merchant;

use App\Models\Gate;
use App\Http\Controllers\Controller;
use App\Http\Resources\Mobile\Gate\GateCollection;
use App\Repositories\Mobile\Api\v1\GateRepository;
use App\Http\Requests\Mobile\Gate\GateByCityRequest;

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

    public function gate_by_city(GateByCityRequest $request)
    {
        $gate = $this->gateRepository->getGateByCity($request->all());
        return new GateCollection($gate);
    }
}
