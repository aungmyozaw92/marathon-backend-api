<?php

namespace App\Http\Controllers\Web\Api\v1;

use App\Models\Gate;
use App\Models\Route;
use App\Models\BusDropOff;
use App\Models\BusStation;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use App\Http\Resources\BusDropOff\BusDropOffResource;
use App\Repositories\Web\Api\v1\BusDropOffRepository;
use App\Http\Resources\BusDropOff\BusDropOffCollection;
use App\Http\Requests\BusDropOff\CreateBusDropOffRequest;
use App\Http\Requests\BusDropOff\UpdateBusDropOffRequest;

class BusDropOffController extends Controller
{
    /**
     * @var BusDropOffRepository
     */
    protected $busDropOffRepository;

    /**
     * BusDropOffController constructor.
     *
     * @param BusDropOffRepository $busDropOffRepository
     */
    public function __construct(BusDropOffRepository $busDropOffRepository)
    {
        $this->busDropOffRepository = $busDropOffRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // $bus_drop_offs = BusDropOff::paginate(20);
        $bus_drop_offs = BusDropOff::with('route', 'gate', 'global_scale')
                                    ->filter(request()->only(['search']))
                                    ->order(request()->only([
                                        'sortBy', 'orderBy'
                                        ]))
                                    ->paginate(25);

        return new BusDropOffCollection($bus_drop_offs);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CreateBusDropOffRequest $request)
    {
        $gate_name = Gate::findOrFail($request->get('gate_id'))->name;
        $gates = Gate::where('name', $gate_name)->get();
        $bus_stations_id = $gates->pluck('bus_station_id')->toArray();
        $bus_stations_cities_id = [];

        $route = Route::findOrFail($request->get('route_id'));
        $cities_id = [
            $route->origin_id, $route->destination_id
        ];

        foreach ($bus_stations_id as $bus_station_id) {
            $bus_station = BusStation::findOrFail($bus_station_id);
            $bus_stations_cities_id[] = $bus_station->city_id;
        }
        $gates_check_result = array_intersect($bus_stations_cities_id, $cities_id);

        if (count($bus_stations_id) < 2 || count($gates_check_result) < 2) {
            return response()->json([
                    'status' => 2, 'message' => 'Please add two valid gates for corresponding bus stations for each city.'
                ], Response::HTTP_OK);
        }

        $record_check = BusDropOff::where('route_id', $route->id)
                        ->where('gate_id', $request->get('gate_id'))
                        ->where('global_scale_id',$request->get('global_scale_id'))
                        ->first();
        if ($record_check) {
              return response()->json([
                    'status' => 2, 'message' => 'Please add unique route or gate or global scale'
                ], Response::HTTP_OK);

        }

        $bus_drop_off = $this->busDropOffRepository->create($request->all());

        return new BusDropOffResource($bus_drop_off->load(['route', 'gate', 'global_scale']));
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\BusDropOff  $bus_drop_off
     * @return \Illuminate\Http\Response
     */
    public function show(BusDropOff $bus_drop_off)
    {
        return new BusDropOffResource($bus_drop_off->load(['route', 'gate', 'global_scale']));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\BusDropOff  $bus_drop_off
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateBusDropOffRequest $request, BusDropOff $bus_drop_off)
    {
        $bus_drop_off = $this->busDropOffRepository->update($bus_drop_off, $request->all());

        return new BusDropOffResource($bus_drop_off->load(['route', 'gate', 'global_scale']));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\BusDropOff  $bus_drop_off
     * @return \Illuminate\Http\Response
     */
    public function destroy(BusDropOff $bus_drop_off)
    {
        $this->busDropOffRepository->destroy($bus_drop_off);

        return response()->json(['status' => 1], Response::HTTP_OK);
    }
}
