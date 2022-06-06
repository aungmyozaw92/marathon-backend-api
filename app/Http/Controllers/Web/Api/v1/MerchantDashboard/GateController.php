<?php

namespace App\Http\Controllers\Web\Api\v1\MerchantDashboard;

use App\Models\Gate;
use App\Http\Controllers\Controller;
use App\Http\Resources\Gate\GateResource;
use App\Http\Resources\Gate\GateCollection;

class GateController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // $gates = $this->gateRepository->all();
        $gates = Gate::with('bus_station', 'bus')
                    ->filter(request()->only([
                        'year', 'month', 'day', 'name', 'delivery_rate', 'bus_station_name', 'search'
                        ]))
                    ->order(request()->only([
                        'sortBy', 'orderBy'
                        ]))
                    ->get();

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
        return new GateResource($gate->load(['bus_station', 'bus']));
    }

}
