<?php

namespace App\Http\Controllers\Web\Api\v1;

use App\Models\FinanceAsset;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use App\Repositories\Web\Api\v1\FinanceAssetRepository;
use App\Http\Resources\FinanceAsset\FinanceAssetResource;
use App\Http\Resources\FinanceAsset\FinanceAssetCollection;
use App\Http\Requests\FinanceAsset\CreateFinanceAssetRequest;
use App\Http\Requests\FinanceAsset\UpdateFinanceAssetRequest;

class FinanceAssetController extends Controller
{
    /**
     * @var FinanceAssetRepository
     */
    protected $financeAssetRepository;

    /**
     * FinanceAssetController constructor.
     *
     * @param FinanceAssetRepository $financeAssetRepository
     */
    public function __construct(FinanceAssetRepository $financeAssetRepository)
    {
        $this->financeAssetRepository = $financeAssetRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // $finance_asset = $this->financeAssetRepository->all();
        $finance_assets = FinanceAsset::with('branch','finance_asset_type','depreciation_expense_account','accumulated_depreciation_account')->get();

        return new FinanceAssetCollection($finance_assets);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CreateFinanceAssetRequest $request)
    {
        // $asset = FinanceAsset::where('finance_code_id',$request->input('finance_code_id'))
        //                             ->where('branch_id',$request->input('branch_id'))->first();
        // if($asset){
        //     return response()->json(['status' => 1,'message'=>'Branch and code are already exit'], Response::HTTP_OK);
        // }
        $finance_asset = $this->financeAssetRepository->create($request->all());

        return new FinanceAssetResource($finance_asset->load(['branch','finance_asset_type','depreciation_expense_account','accumulated_depreciation_account']));
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\FinanceAsset  $finance_asset
     * @return \Illuminate\Http\Response
     */
    public function show(FinanceAsset $financeAsset)
    {
        return new FinanceAssetResource($financeAsset->load(['branch','finance_asset_type','depreciation_expense_account','accumulated_depreciation_account']));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\FinanceAsset  $finance_asset
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateFinanceAssetRequest $request, FinanceAsset $financeAsset)
    {
        $finance_asset = $this->financeAssetRepository->update($financeAsset, $request->all());

        return new FinanceAssetResource($finance_asset->load(['branch','finance_asset_type','depreciation_expense_account','accumulated_depreciation_account']));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\FinanceAsset  $finance_asset
     * @return \Illuminate\Http\Response
     */
    public function destroy(FinanceAsset $finance_asset)
    {
        $this->financeAssetRepository->destroy($finance_asset);

        return response()->json(['status' => 1], Response::HTTP_OK);
    }
}
