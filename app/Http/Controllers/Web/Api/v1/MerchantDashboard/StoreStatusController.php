<?php

namespace App\Http\Controllers\Web\Api\v1\MerchantDashboard;


use App\Models\StoreStatus;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use App\Repositories\Web\Api\v1\StoreStatusRepository;
use App\Http\Resources\StoreStatus\StoreStatusResource;
use App\Http\Resources\StoreStatus\StoreStatusCollection;
use App\Http\Requests\StoreStatus\CreateStoreStatusRequest;
use App\Http\Requests\StoreStatus\UpdateStoreStatusRequest;

class StoreStatusController extends Controller
{
    /**
     * @var StoreStatusRepository
     */
    protected $storeStatusRepository;

    /**
     * StoreStatusController constructor.
     *
     * @param StoreStatusRepository $storeStatusRepository
     */
    public function __construct(StoreStatusRepository $storeStatusRepository)
    {
        $this->storeStatusRepository = $storeStatusRepository;
    }

     /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // $storeStatuses = $this->storeStatusRepository->paginate(10);
        $storeStatuses = StoreStatus::all();

        return new StoreStatusCollection($storeStatuses);

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CreateStoreStatusRequest $request)
    {
        $storeStatus = $this->storeStatusRepository->create($request->all());

        return new StoreStatusResource($storeStatus);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\StoreStatus  $storeStatus
     * @return \Illuminate\Http\Response
     */
    public function show(StoreStatus $storeStatus)
    {
        return new StoreStatusResource($storeStatus);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\StoreStatus  $storeStatus
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateStoreStatusRequest $request, StoreStatus $storeStatus)
    {
        $storeStatus = $this->storeStatusRepository->update($storeStatus, $request->all());

        return new StoreStatusResource($storeStatus);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\StoreStatus  $storeStatus
     * @return \Illuminate\Http\Response
     */
    public function destroy(StoreStatus $storeStatus)
    {
        $this->storeStatusRepository->destroy($storeStatus);

        return response()->json([ 'status' => 1 ], Response::HTTP_OK);
    }
}
