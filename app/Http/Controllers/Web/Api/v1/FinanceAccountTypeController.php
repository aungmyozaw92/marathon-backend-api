<?php

namespace App\Http\Controllers\Web\Api\v1;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\FinanceAccountType;
use App\Http\Controllers\Controller;
use App\Repositories\Web\Api\v1\FinanceAccountTypeRepository;
use App\Http\Resources\FinanceAccountType\FinanceAccountTypeResource;
use App\Http\Resources\FinanceAccountType\FinanceAccountTypeCollection;
use App\Http\Requests\FinanceAccountType\CreateFinanceAccountTypeRequest;
use App\Http\Requests\FinanceAccountType\UpdateFinanceAccountTypeRequest;

class FinanceAccountTypeController extends Controller
{
    /**
     * @var FinanceAccountTypeRepository
     */
    protected $financeAccountTypeRepository;

    /**
     * FinanceAccountTypeController constructor.
     *
     * @param FinanceAccountTypeRepository $financeAccountTypeRepository
     */
    public function __construct(FinanceAccountTypeRepository $financeAccountTypeRepository)
    {
        $this->financeAccountTypeRepository = $financeAccountTypeRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // $accountTypes = $this->financeAccountTypeRepository->all();
        $accountTypes = FinanceAccountType::all();

        return new FinanceAccountTypeCollection($accountTypes);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CreateFinanceAccountTypeRequest $request)
    {
        $accountType = $this->financeAccountTypeRepository->create($request->all());

        return new FinanceAccountTypeResource($accountType);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\FinanceAccountType  $accountType
     * @return \Illuminate\Http\Response
     */
    public function show(FinanceAccountType $financeAccountType)
    {
        return new FinanceAccountTypeResource($financeAccountType);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\FinanceAccountType  $accountType
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateFinanceAccountTypeRequest $request, FinanceAccountType $financeAccountType)
    {
        $accountType = $this->financeAccountTypeRepository->update($financeAccountType, $request->all());

        return new FinanceAccountTypeResource($accountType);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\FinanceAccountType  $accountType
     * @return \Illuminate\Http\Response
     */
    public function destroy(FinanceAccountType $financeAccountType)
    {
        $this->financeAccountTypeRepository->destroy($financeAccountType);

        return response()->json(['status' => 1], Response::HTTP_OK);
    }
}
