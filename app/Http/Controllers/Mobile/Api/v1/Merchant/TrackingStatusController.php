<?php

namespace App\Http\Controllers\Mobile\Api\v1\Merchant;

use App\Models\TrackingStatus;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use App\Http\Resources\Mobile\TrackingStatus\TrackingStatusResource;
use App\Http\Resources\Mobile\TrackingStatus\TrackingStatusCollection;
use App\Repositories\Mobile\Api\v1\TrackingStatusRepository;

class TrackingStatusController extends Controller
{
    /**
     * @var TrackingStatusRepository
     */
    protected $trackingStatusRepository;

    /**
     * ZoneController constructor.
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
        $tracking_status = $this->trackingStatusRepository->all();

        return new TrackingStatusCollection($tracking_status);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\TrackingStatus  $tracking_status
     * @return \Illuminate\Http\Response
     */
    public function show(TrackingStatus $tracking_status)
    {
        return new TrackingStatusResource($tracking_status);
    }
}
