<?php

namespace App\Http\Controllers\Web\Api\v1;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\FailureStatus;
use App\Http\Controllers\Controller;
use App\Repositories\Web\Api\v1\FailureStatusRepository;
use App\Http\Resources\FailureStatus\FailureStatusResource;
use App\Http\Resources\FailureStatus\FailureStatusCollection;
use App\Http\Requests\FailureStatuses\CreateFailureStatusRequest;
use App\Http\Requests\FailureStatuses\UpdateFailureStatusRequest;

class FailureStatusController extends Controller
{
    /**
     * @var FailureStatusRepository
     */
    protected $failureStatusRepository;

    /**
     * FailureStatusController constructor.
     *
     * @param FailureStatusRepository $failureStatusRepository
     */
    public function __construct(FailureStatusRepository $failureStatusRepository)
    {
        $this->failureStatusRepository = $failureStatusRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // $failureStatuses = $this->failureStatusRepository->all();
        $failureStatuses = FailureStatus::all();

        return new FailureStatusCollection($failureStatuses);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CreateFailureStatusRequest $request)
    {
        $failureStatus = $this->failureStatusRepository->create($request->all());

        return new FailureStatusResource($failureStatus);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\FailureStatus  $failureStatus
     * @return \Illuminate\Http\Response
     */
    public function show(FailureStatus $failureStatus)
    {
        return new FailureStatusResource($failureStatus);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\FailureStatus  $failureStatus
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateFailureStatusRequest $request, FailureStatus $failureStatus)
    {
        $failureStatus = $this->failureStatusRepository->update($failureStatus, $request->all());

        return new FailureStatusResource($failureStatus);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\FailureStatus  $failureStatus
     * @return \Illuminate\Http\Response
     */
    public function destroy(FailureStatus $failureStatus)
    {
        $this->failureStatusRepository->destroy($failureStatus);

        return response()->json(['status' => 1], Response::HTTP_OK);
    }
}
