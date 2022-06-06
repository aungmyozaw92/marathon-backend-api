<?php

namespace App\Http\Controllers\Web\Api\v1;

use App\Models\Gate;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use App\Http\Resources\Gate\GateResource;
use App\Http\Resources\Gate\GateCollection;
use App\Http\Requests\Gate\CreateGateRequest;
use App\Http\Requests\Gate\UpdateGateRequest;
use App\Repositories\Web\Api\v1\GateRepository;

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
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CreateGateRequest $request)
    {
        $gate = $this->gateRepository->create($request->all());

        return new GateResource($gate->load(['bus_station', 'bus']));
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

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Gate  $gate
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateGateRequest $request, Gate $gate)
    {
        $gate = $this->gateRepository->update($gate, $request->all());

        return new GateResource($gate->load(['bus_station', 'bus']));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Gate  $gate
     * @return \Illuminate\Http\Response
     */
    public function destroy(Gate $gate)
    {
        $this->gateRepository->destroy($gate);

        return response()->json([ 'status' => 1 ], Response::HTTP_OK);
    }
}
