<?php

namespace App\Http\Controllers\Web\Api\v1;

use App\Models\FinanceMeta;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use App\Repositories\Web\Api\v1\FinanceMetaRepository;
use App\Http\Resources\FinanceMeta\FinanceMetaResource;
use App\Http\Resources\FinanceMeta\FinanceMetaCollection;
use App\Http\Requests\FinanceMeta\CreateFinanceMetaRequest;
use App\Http\Requests\FinanceMeta\UpdateFinanceMetaRequest;

class FinanceMetaController extends Controller
{
    /**
     * @var FinanceMetaRepository
     */
    protected $financeMetaRepository;

    /**
     * FinanceMetaController constructor.
     *
     * @param FinanceMetaRepository $financeMetaRepository
     */
    public function __construct(FinanceMetaRepository $financeMetaRepository)
    {
        $this->financeMetaRepository = $financeMetaRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // $finance_meta = $this->financeMetaRepository->all();
        $finance_meta = FinanceMeta::all();

        return new FinanceMetaCollection($finance_meta);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CreateFinanceMetaRequest $request)
    {
        $finance_meta = $this->financeMetaRepository->create($request->all());

        return new FinanceMetaResource($finance_meta);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\FinanceMeta  $finance_meta
     * @return \Illuminate\Http\Response
     */
    public function show(FinanceMeta $financeMeta)
    {
        return new FinanceMetaResource($financeMeta);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\FinanceMeta  $finance_meta
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateFinanceMetaRequest $request, FinanceMeta $financeMeta)
    {
        $finance_meta = $this->financeMetaRepository->update($financeMeta, $request->all());

        return new FinanceMetaResource($finance_meta);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\FinanceMeta  $finance_meta
     * @return \Illuminate\Http\Response
     */
    public function destroy(FinanceMeta $finance_meta)
    {
        $this->financeMetaRepository->destroy($finance_meta);

        return response()->json(['status' => 1], Response::HTTP_OK);
    }
}
