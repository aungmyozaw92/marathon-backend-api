<?php

namespace App\Http\Controllers\Web\Api\v1\MerchantDashboard;


use App\Models\BusStation;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use App\Http\Resources\BusStation\BusStationResource;
use App\Repositories\Web\Api\v1\BusStationRepository;
use App\Http\Resources\BusStation\BusStationCollection;
use App\Http\Requests\BusStation\CreateBusStationRequest;
use App\Http\Requests\BusStation\UpdateBusStationRequest;

class BusStationController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $busStations = BusStation::with(['city', 'zone', 'gates','gates.bus'])->get();

        return new BusStationCollection($busStations);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\BusStation  $busStation
     * @return \Illuminate\Http\Response
     */
    public function show(BusStation $busStation)
    {
        return new BusStationResource($busStation->load(['city', 'zone', 'gates','gates.bus']));
    }

}
