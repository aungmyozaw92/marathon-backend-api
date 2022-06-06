<?php

namespace App\Http\Controllers\Web\Api\v1;

use App\Models\FinancePettyCash;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use App\Repositories\Web\Api\v1\FinancePettyCashRepository;
use App\Http\Resources\FinancePettyCash\FinancePettyCashResource;
use App\Http\Resources\FinancePettyCash\FinancePettyCashCollection;
use App\Http\Requests\FinancePettyCash\CreateFinancePettyCashRequest;
use App\Http\Requests\FinancePettyCash\UpdateFinancePettyCashRequest;

class FinancePettyCashController extends Controller
{
    /**
     * @var FinancePettyCashRepository
     */
    protected $finance_petty_cashRepository;

    /**
     * FinancePettyCashController constructor.
     *
     * @param FinancePettyCashRepository $finance_petty_cashRepository
     */
    public function __construct(FinancePettyCashRepository $finance_petty_cashRepository)
    {
        $this->finance_petty_cashRepository = $finance_petty_cashRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // $finance_petty_cash = $this->finance_petty_cashRepository->all();
        $finance_petty_cash = FinancePettyCash::with(['staff','branch','actor_by',
                                                'finance_petty_cash_items','finance_petty_cash_items.from_finance_account',
                                                'finance_petty_cash_items.to_finance_account'])->get();

        return new FinancePettyCashCollection($finance_petty_cash);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CreateFinancePettyCashRequest $request)
    {

        $finance_petty_cash = $this->finance_petty_cashRepository->create($request->all());

        return new FinancePettyCashResource($finance_petty_cash->load(['staff','branch','actor_by',
                                                'finance_petty_cash_items','finance_petty_cash_items.from_finance_account',
                                                'finance_petty_cash_items.to_finance_account']));
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\FinancePettyCash  $finance_petty_cash
     * @return \Illuminate\Http\Response
     */
    public function show(FinancePettyCash $finance_petty_cash)
    {
        return new FinancePettyCashResource($finance_petty_cash->load(['staff','branch','actor_by',
                                                'finance_petty_cash_items','finance_petty_cash_items.from_finance_account',
                                                'finance_petty_cash_items.to_finance_account']));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\FinancePettyCash  $finance_petty_cash
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateFinancePettyCashRequest $request, FinancePettyCash $finance_petty_cash)
    {
        $finance_petty_cash = $this->finance_petty_cashRepository->update($finance_petty_cash, $request->all());

        return new FinancePettyCashResource($finance_petty_cash);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\FinancePettyCash  $finance_petty_cash
     * @return \Illuminate\Http\Response
     */
    public function destroy(FinancePettyCash $finance_petty_cash)
    {
        $this->finance_petty_cashRepository->destroy($finance_petty_cash);

        return response()->json(['status' => 1], Response::HTTP_OK);
    }
}
