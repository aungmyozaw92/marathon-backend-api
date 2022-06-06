<?php

namespace App\Http\Controllers\Web\Api\v1;

use App\Models\CallStatus;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use App\Http\Resources\CallStatus\CallStatusResource;
use App\Repositories\Web\Api\v1\CallStatusRepository;
use App\Http\Resources\CallStatus\CallStatusCollection;
use App\Http\Requests\CallStatus\CreateCallStatusRequest;
use App\Http\Requests\CallStatus\UpdateCallStatusRequest;

class CallStatusController extends Controller
{
    /**
     * @var CallStatusRepository
     */
    protected $callStatusRepository;

    /**
     * CallStatusController constructor.
     *
     * @param CallStatusRepository $callStatusRepository
     */
    public function __construct(CallStatusRepository $callStatusRepository)
    {
        $this->callStatusRepository = $callStatusRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // $callStatuses = $this->callStatusRepository->paginate(10);
        $callStatuses = CallStatus::all();

        return new CallStatusCollection($callStatuses);

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CreateCallStatusRequest $request)
    {
        $callStatus = $this->callStatusRepository->create($request->all());

        return new CallStatusResource($callStatus);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\CallStatus  $callStatus
     * @return \Illuminate\Http\Response
     */
    public function show(CallStatus $callStatus)
    {
        return new CallStatusResource($callStatus);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\CallStatus  $callStatus
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateCallStatusRequest $request, CallStatus $callStatus)
    {
        $callStatus = $this->callStatusRepository->update($callStatus, $request->all());

        return new callStatusResource($callStatus);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\CallStatus  $callStatus
     * @return \Illuminate\Http\Response
     */
    public function destroy(CallStatus $callStatus)
    {
        $this->callStatusRepository->destroy($callStatus);
       
        return response()->json([ 'status' => 1 ], Response::HTTP_OK);
    }
}
