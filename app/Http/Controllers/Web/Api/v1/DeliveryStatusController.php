<?php

namespace App\Http\Controllers\Web\Api\v1;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\DeliveryStatus;
use App\Http\Controllers\Controller;
use App\Repositories\Web\Api\v1\DeliveryStatusRepository;
use App\Http\Resources\DeliveryStatus\DeliveryStatusResource;
use App\Http\Resources\DeliveryStatus\DeliveryStatusCollection;
use App\Http\Requests\DeliveryStatus\CreateDeliveryStatusRequest;
use App\Http\Requests\DeliveryStatus\UpdateDeliveryStatusRequest;

class DeliveryStatusController extends Controller
{
    /**
     * @var DeliveryStatusRepository
     */
    protected $deliveryStatusRepository;

    /**
     * DeliveryStatusController constructor.
     *
     * @param DeliveryStatusRepository $deliveryStatusRepository
     */
    public function __construct(DeliveryStatusRepository $deliveryStatusRepository)
    {
        $this->deliveryStatuRepository = $deliveryStatusRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // $deliveryStatuses = $this->deliveryStatuRepository->paginate(10);
        // $deliveryStatuses = DeliveryStatus::all();
        $deliveryStatuses = DeliveryStatus::orderBy('id', 'asc')->get();

        return new DeliveryStatusCollection($deliveryStatuses);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CreateDeliveryStatusRequest $request)
    {
        $deliveryStatus = $this->deliveryStatuRepository->create($request->all());

        return new DeliveryStatusResource($deliveryStatus);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\DeliveryStatus  $deliveryStatus
     * @return \Illuminate\Http\Response
     */
    public function show(DeliveryStatus $deliveryStatus)
    {
        return new DeliveryStatusResource($deliveryStatus);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\DeliveryStatus  $deliveryStatus
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateDeliveryStatusRequest $request, DeliveryStatus $deliveryStatus)
    {
        $deliveryStatus = $this->deliveryStatuRepository->update($deliveryStatus, $request->all());

        return new DeliveryStatusResource($deliveryStatus);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\DeliveryStatus  $deliveryStatus
     * @return \Illuminate\Http\Response
     */
    public function destroy(DeliveryStatus $deliveryStatus)
    {
        $this->deliveryStatuRepository->destroy($deliveryStatus);

        return response()->json([ 'status' => 1 ], Response::HTTP_OK);
    }
}
