<?php

namespace App\Http\Controllers\Web\Api\v1;

use App\Models\FinanceCode;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use App\Repositories\Web\Api\v1\FinanceCodeRepository;
use App\Http\Resources\FinanceCode\FinanceCodeResource;
use App\Http\Resources\FinanceCode\FinanceCodeCollection;
use App\Http\Requests\FinanceCode\CreateFinanceCodeRequest;
use App\Http\Requests\FinanceCode\UpdateFinanceCodeRequest;

class FinanceCodeController extends Controller
{
    /**
     * @var FinanceCodeRepository
     */
    protected $financeCodeRepository;

    /**
     * FinanceCodeController constructor.
     *
     * @param FinanceCodeRepository $financeCodeRepository
     */
    public function __construct(FinanceCodeRepository $financeCodeRepository)
    {
        $this->financeCodeRepository = $financeCodeRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // $finance_code = $this->financeCodeRepository->all();
        $finance_code = FinanceCode::all();

        return new FinanceCodeCollection($finance_code);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CreateFinanceCodeRequest $request)
    {
        $finance_code = $this->financeCodeRepository->create($request->all());

        return new FinanceCodeResource($finance_code);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\FinanceCode  $finance_code
     * @return \Illuminate\Http\Response
     */
    public function show(FinanceCode $financeCode)
    {
        return new FinanceCodeResource($financeCode);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\FinanceCode  $finance_code
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateFinanceCodeRequest $request, FinanceCode $financeCode)
    {
        $finance_code = $this->financeCodeRepository->update($financeCode, $request->all());

        return new FinanceCodeResource($finance_code);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\FinanceCode  $finance_code
     * @return \Illuminate\Http\Response
     */
    public function destroy(FinanceCode $finance_code)
    {
        $this->financeCodeRepository->destroy($finance_code);

        return response()->json(['status' => 1], Response::HTTP_OK);
    }
}
