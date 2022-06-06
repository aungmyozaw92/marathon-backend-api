<?php

namespace App\Http\Controllers\Web\Api\v1;

use App\Models\FinanceAssetType;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use App\Repositories\Web\Api\v1\FinanceAssetTypeRepository;
use App\Http\Resources\FinanceAssetType\FinanceAssetTypeResource;
use App\Http\Resources\FinanceAssetType\FinanceAssetTypeCollection;
use App\Http\Requests\FinanceAssetType\CreateFinanceAssetTypeRequest;
use App\Http\Requests\FinanceAssetType\UpdateFinanceAssetTypeRequest;

class FinanceAssetTypeController extends Controller
{
    /**
     * @var FinanceAssetTypeRepository
     */
    protected $financeAssetTypeRepository;

    /**
     * FinanceAssetTypeController constructor.
     *
     * @param FinanceAssetTypeRepository $financeAssetTypeRepository
     */
    public function __construct(FinanceAssetTypeRepository $financeAssetTypeRepository)
    {
        $this->financeAssetTypeRepository = $financeAssetTypeRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // $finance_asset_types = $this->financeAssetTypeRepository->all();
        $finance_asset_types = FinanceAssetType::with('branch','accumulated_depreciation_account','depreciation_expense_account')
                                                ->get();

        return new FinanceAssetTypeCollection($finance_asset_types);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CreateFinanceAssetTypeRequest $request)
    {
        $finance_asset_type = $this->financeAssetTypeRepository->create($request->all());

        return new FinanceAssetTypeResource($finance_asset_type->load(['branch','accumulated_depreciation_account','depreciation_expense_account']));
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\FinanceAssetType  $finance_asset_type
     * @return \Illuminate\Http\Response
     */
    public function show(FinanceAssetType $financeAssetType)
    {
        return new FinanceAssetTypeResource($financeAssetType->load(['branch','accumulated_depreciation_account','depreciation_expense_account']));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\FinanceAssetType  $finance_asset_type
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateFinanceAssetTypeRequest $request, FinanceAssetType $financeAssetType)
    {
        $finance_asset_type = $this->financeAssetTypeRepository->update($financeAssetType, $request->all());

        return new FinanceAssetTypeResource($finance_asset_type->load(['branch','accumulated_depreciation_account','depreciation_expense_account']));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\FinanceAssetType  $finance_asset_type
     * @return \Illuminate\Http\Response
     */
    public function destroy(FinanceAssetType $finance_asset_type)
    {
        $this->financeAssetTypeRepository->destroy($finance_asset_type);

        return response()->json(['status' => 1], Response::HTTP_OK);
    }
}
