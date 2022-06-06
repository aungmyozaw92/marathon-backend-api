<?php

namespace App\Http\Controllers\Mobile\Api\v1\Delivery;

use App\Models\BusStation;
use App\Http\Controllers\Controller;
use App\Repositories\Mobile\Api\v1\Delivery\BusStationRepository;
use App\Http\Resources\Mobile\Delivery\BusStation\BusStationResource;
use App\Http\Resources\Mobile\Delivery\BusStation\BusStationCollection;

class BusStationController extends Controller
{
    /**
     * @var BusStationRepository
     */
    protected $busStationRepository;

    /**
     * BusStationController constructor.
     *
     * @param BusStationRepository $busStationRepository
     */
    public function __construct(BusStationRepository $busStationRepository)
    {
        $this->BusStationRepository = $busStationRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // $busStations = BusStation::with(['city', 'zone', 'gates'])->get();
        $busStations = BusStation::all();

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
        return new BusStationResource($busStation);
    }
}
