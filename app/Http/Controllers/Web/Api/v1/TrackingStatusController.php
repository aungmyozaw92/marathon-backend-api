<?php

namespace App\Http\Controllers\Web\Api\v1;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\TrackingStatus;
use App\Http\Controllers\Controller;
use App\Repositories\Web\Api\v1\TrackingStatusRepository;
use App\Http\Resources\TrackingStatus\TrackingStatusResource;
use App\Http\Resources\TrackingStatus\TrackingStatusCollection;
use App\Http\Requests\TrackingStatus\CreateTrackingStatusRequest;
use App\Http\Requests\TrackingStatus\UpdateTrackingStatusRequest;

class TrackingStatusController extends Controller
{
    /**
     * @var TrackingStatusRepository
     */
    protected $trackingStatusRepository;

    /**
     * TrackingStatusController constructor.
     *
     * @param TrackingStatusRepository $trackingStatusRepository
     */
    public function __construct(TrackingStatusRepository $trackingStatusRepository)
    {
        $this->trackingStatusRepository = $trackingStatusRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // $trackingStatuses = $this->trackingStatusRepository->paginate(10);
        $trackingStatuses = TrackingStatus::all();

        return new TrackingStatusCollection($trackingStatuses);

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CreateTrackingStatusRequest $request)
    {
        $trackingStatus = $this->trackingStatusRepository->create($request->all());

        return new TrackingStatusResource($trackingStatus);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\TrackingStatus  $trackingStatus
     * @return \Illuminate\Http\Response
     */
    public function show(TrackingStatus $trackingStatus)
    {
        return new TrackingStatusResource($trackingStatus);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\TrackingStatus  $trackingStatus
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateTrackingStatusRequest $request, TrackingStatus $trackingStatus)
    {
        $trackingStatus = $this->trackingStatusRepository->update($trackingStatus, $request->all());

        return new trackingStatusResource($trackingStatus);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\TrackingStatus  $trackingStatus
     * @return \Illuminate\Http\Response
     */
    public function destroy(TrackingStatus $trackingStatus)
    {
        $this->trackingStatusRepository->destroy($trackingStatus);
       
        return response()->json([ 'status' => 1 ], Response::HTTP_OK);
    }
}
