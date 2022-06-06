<?php

namespace App\Http\Controllers\Web\Api\v1;

use App\Models\FinanceConfig;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use App\Repositories\Web\Api\v1\FinanceConfigRepository;
use App\Http\Resources\FinanceConfig\FinanceConfigResource;
use App\Http\Resources\FinanceConfig\FinanceConfigCollection;
use App\Http\Requests\FinanceConfig\CreateFinanceConfigRequest;
use App\Http\Requests\FinanceConfig\UpdateFinanceConfigRequest;

class FinanceConfigController extends Controller
{
    /**
     * @var FinanceConfigRepository
     */
    protected $financeConfigRepository;

    /**
     * FinanceConfigController constructor.
     *
     * @param FinanceConfigRepository $financeConfigRepository
     */
    public function __construct(FinanceConfigRepository $financeConfigRepository)
    {
        $this->financeConfigRepository = $financeConfigRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // $finance_config = $this->financeConfigRepository->all();
        $finance_config = FinanceConfig::all();

        return new FinanceConfigCollection($finance_config->load('finance_account','to_finance_account','branch'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CreateFinanceConfigRequest $request)
    {
        $finance_config = $this->financeConfigRepository->create($request->all());

        return new FinanceConfigResource($finance_config->load('finance_account','to_finance_account','branch'));
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\FinanceConfig  $finance_config
     * @return \Illuminate\Http\Response
     */
    public function show(FinanceConfig $financeConfig)
    {
        return new FinanceConfigResource($financeConfig->load('finance_account','to_finance_account','branch'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\FinanceConfig  $finance_config
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateFinanceConfigRequest $request, FinanceConfig $financeConfig)
    {
        $finance_config = $this->financeConfigRepository->update($financeConfig, $request->all());

        return new FinanceConfigResource($finance_config->load('finance_account','to_finance_account','branch'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\FinanceConfig  $finance_config
     * @return \Illuminate\Http\Response
     */
    public function destroy(FinanceConfig $finance_config)
    {
        $this->financeConfigRepository->destroy($finance_config);

        return response()->json(['status' => 1], Response::HTTP_OK);
    }
}
