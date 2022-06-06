<?php

namespace App\Http\Controllers\Web\Api\v1;

use App\Models\FinanceTax;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use App\Repositories\Web\Api\v1\FinanceTaxRepository;
use App\Http\Resources\FinanceTax\FinanceTaxResource;
use App\Http\Resources\FinanceTax\FinanceTaxCollection;
use App\Http\Requests\FinanceTax\CreateFinanceTaxRequest;
use App\Http\Requests\FinanceTax\UpdateFinanceTaxRequest;

class FinanceTaxController extends Controller
{
    /**
     * @var FinanceTaxRepository
     */
    protected $financeTaxRepository;

    /**
     * FinanceTaxController constructor.
     *
     * @param FinanceTaxRepository $financeTaxRepository
     */
    public function __construct(FinanceTaxRepository $financeTaxRepository)
    {
        $this->financeTaxRepository = $financeTaxRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // $finance_taxes = $this->financeTaxRepository->all();
        $finance_taxes = FinanceTax::all();

        return new FinanceTaxCollection($finance_taxes);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CreateFinanceTaxRequest $request)
    {
        $finance_tax = $this->financeTaxRepository->create($request->all());

        return new FinanceTaxResource($finance_tax);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\FinanceTax  $finance_tax
     * @return \Illuminate\Http\Response
     */
    public function show(FinanceTax $financeTax)
    {
        return new FinanceTaxResource($financeTax);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\FinanceTax  $finance_tax
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateFinanceTaxRequest $request, FinanceTax $financeTax)
    {
        $finance_tax = $this->financeTaxRepository->update($financeTax, $request->all());

        return new FinanceTaxResource($finance_tax);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\FinanceTax  $finance_tax
     * @return \Illuminate\Http\Response
     */
    public function destroy(FinanceTax $finance_tax)
    {
        $this->financeTaxRepository->destroy($finance_tax);

        return response()->json(['status' => 1], Response::HTTP_OK);
    }
}
