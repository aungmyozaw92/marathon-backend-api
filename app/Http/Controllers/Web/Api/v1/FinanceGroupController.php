<?php

namespace App\Http\Controllers\Web\Api\v1;

use App\Models\FinanceGroup;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use App\Repositories\Web\Api\v1\FinanceGroupRepository;
use App\Http\Resources\FinanceGroup\FinanceGroupResource;
use App\Http\Resources\FinanceGroup\FinanceGroupCollection;
use App\Http\Requests\FinanceGroup\CreateFinanceGroupRequest;
use App\Http\Requests\FinanceGroup\UpdateFinanceGroupRequest;

class FinanceGroupController extends Controller
{
    /**
     * @var FinanceGroupRepository
     */
    protected $financeGroupRepository;

    /**
     * FinanceGroupController constructor.
     *
     * @param FinanceGroupRepository $financeGroupRepository
     */
    public function __construct(FinanceGroupRepository $financeGroupRepository)
    {
        $this->financeGroupRepository = $financeGroupRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // $accountTypes = $this->financeGroupRepository->all();
        $accountTypes = FinanceGroup::all();

        return new FinanceGroupCollection($accountTypes);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CreateFinanceGroupRequest $request)
    {
        $accountType = $this->financeGroupRepository->create($request->all());

        return new FinanceGroupResource($accountType);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\FinanceGroup  $accountType
     * @return \Illuminate\Http\Response
     */
    public function show(FinanceGroup $financeGroup)
    {
        return new FinanceGroupResource($financeGroup);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\FinanceGroup  $accountType
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateFinanceGroupRequest $request, FinanceGroup $financeGroup)
    {
        $accountType = $this->financeGroupRepository->update($financeGroup, $request->all());

        return new FinanceGroupResource($accountType);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\FinanceGroup  $accountType
     * @return \Illuminate\Http\Response
     */
    public function destroy(FinanceGroup $financeGroup)
    {
        $this->financeGroupRepository->destroy($financeGroup);

        return response()->json(['status' => 1], Response::HTTP_OK);
    }
}
