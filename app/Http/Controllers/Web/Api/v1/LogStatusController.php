<?php

namespace App\Http\Controllers\Web\Api\v1;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\LogStatus;
use App\Http\Controllers\Controller;
use App\Repositories\Web\Api\v1\LogStatusRepository;
use App\Http\Resources\LogStatus\LogStatusResource;
use App\Http\Resources\LogStatus\LogStatusCollection;
use App\Http\Requests\LogStatus\CreateLogStatusRequest;
use App\Http\Requests\LogStatus\UpdateLogStatusRequest;

class LogStatusController extends Controller
{
    /**
     * @var LogStatusRepository
     */
    protected $logStatusRepository;

    /**
     * LogStatusController constructor.
     *
     * @param LogStatusRepository $logStatusRepository
     */
    public function __construct(LogStatusRepository $logStatusRepository)
    {
        $this->logStatuRepository = $logStatusRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $logStatuses = $this->logStatuRepository->all();

        return new LogStatusCollection($logStatuses);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CreateLogStatusRequest $request)
    {
        $logStatus = $this->logStatuRepository->create($request->all());

        return new LogStatusResource($logStatus);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\LogStatus  $logStatus
     * @return \Illuminate\Http\Response
     */
    public function show(LogStatus $logStatus)
    {
        return new LogStatusResource($logStatus);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\LogStatus  $logStatus
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateLogStatusRequest $request, LogStatus $logStatus)
    {
        $logStatus = $this->logStatuRepository->update($logStatus, $request->all());

        return new LogStatusResource($logStatus);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\LogStatus  $logStatus
     * @return \Illuminate\Http\Response
     */
    public function destroy(LogStatus $logStatus)
    {
        $this->logStatuRepository->destroy($logStatus);

        return response()->json(['status' => 1], Response::HTTP_OK);
    }
}
