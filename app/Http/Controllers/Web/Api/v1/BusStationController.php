<?php

namespace App\Http\Controllers\Web\Api\v1;

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
        $this->busStationRepository = $busStationRepository;
    }

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
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CreateBusStationRequest $request)
    {
        $busStation = $this->busStationRepository->create($request->all());

        return new BusStationResource($busStation->load(['city', 'zone', 'gates','gates.bus']));
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

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\BusStation  $busStation
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateBusStationRequest $request, BusStation $busStation)
    {
        $busStation = $this->busStationRepository->update($busStation, $request->all());

        return new BusStationResource($busStation->load(['city', 'zone', 'gates','gates.bus']));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\BusStation  $busStation
     * @return \Illuminate\Http\Response
     */
    public function destroy(BusStation $busStation)
    {
        // dd($busStation);
        $this->busStationRepository->destroy($busStation);
        
        return response()->json([ 'status' => 1 ], Response::HTTP_OK);
    }
}
