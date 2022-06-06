<?php

namespace App\Http\Controllers\Web\Api\v1;

use App\Models\DelegateDuration;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use App\Http\Resources\DelegateDuration\DelegateDurationResource;
use App\Http\Resources\DelegateDuration\DelegateDurationCollection;
use App\Http\Requests\DelegateDuration\CreateDelegateDurationRequest;
use App\Http\Requests\DelegateDuration\UpdateDelegateDurationRequest;
use App\Repositories\Web\Api\v1\DelegateDurationRepository;

class DelegateDurationController extends Controller
{
    /**
     * @var DelegateDurationRepository
     */
    protected $delegateDurationRepository;

    /**
     * DelegateDurationController constructor.
     *
     * @param DelegateDurationRepository $delegateDurationRepository
     */
    public function __construct(DelegateDurationRepository $delegateDurationRepository)
    {
        $this->delegateDurationRepository = $delegateDurationRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $delegate_durations =$this->delegateDurationRepository->all();

        return new DelegateDurationCollection($delegate_durations);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CreateDelegateDurationRequest $request)
    {
        $delegate_duration =$this->delegateDurationRepository->create($request->all());

        return new DelegateDurationResource($delegate_duration);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\DelegateDuration  $delegate_duration
     * @return \Illuminate\Http\Response
     */
    public function show(DelegateDuration $delegate_duration)
    {
        return new DelegateDurationResource($delegate_duration);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\DelegateDuration  $delegate_duration
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateDelegateDurationRequest $request, DelegateDuration $delegate_duration)
    {
        $delegate_duration =$this->delegateDurationRepository->update($delegate_duration, $request->all());

        return new DelegateDurationResource($delegate_duration);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\DelegateDuration  $delegate_duration
     * @return \Illuminate\Http\Response
     */
    public function destroy(DelegateDuration $delegate_duration)
    {
        $this->delegateDurationRepository->destroy($delegate_duration);

        return response()->json([ 'status' => 1 ], Response::HTTP_OK);
    }
}
