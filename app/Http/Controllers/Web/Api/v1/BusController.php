<?php

namespace App\Http\Controllers\Web\Api\v1;

use App\Models\Bus;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use App\Http\Resources\Bus\BusResource;
use App\Http\Resources\Bus\BusCollection;
use App\Http\Requests\Bus\CreateBusRequest;
use App\Http\Requests\Bus\UpdateBusRequest;
use App\Repositories\Web\Api\v1\BusRepository;

class BusController extends Controller
{
    /**
     * @var BusRepository
     */
    protected $busRepository;

    /**
     * BusController constructor.
     *
     * @param BusRepository $busRepository
     */
    public function __construct(BusRepository $busRepository)
    {
        $this->busRepository = $busRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // $buses = $this->busRepository->all();
        $buses = Bus::all();

        return new BusCollection($buses);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CreateBusRequest $request)
    {
        $bus =$this->busRepository->create($request->all());

        return new BusResource($bus);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Bus  $bus
     * @return \Illuminate\Http\Response
     */
    public function show(Bus $bus)
    {
        return new BusResource($bus);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Bus  $bus
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateBusRequest $request, Bus $bus)
    {
        $bus = $this->busRepository->update($bus, $request->all());

        return new BusResource($bus);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Bus  $bus
     * @return \Illuminate\Http\Response
     */
    public function destroy(Bus $bus)
    {
        $this->busRepository->destroy($bus);

        return response()->json([ 'status' => 1 ], Response::HTTP_OK);
    }
}
