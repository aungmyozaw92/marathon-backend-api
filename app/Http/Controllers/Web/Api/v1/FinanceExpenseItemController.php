<?php

namespace App\Http\Controllers\Web\Api\v1;

use App\Models\FinanceExpenseItem;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use App\Repositories\Web\Api\v1\FinanceExpenseItemRepository;
use App\Http\Resources\FinanceExpenseItem\FinanceExpenseItemResource;
use App\Http\Resources\FinanceExpenseItem\FinanceExpenseItemCollection;
use App\Http\Requests\FinanceExpenseItem\CreateFinanceExpenseItemRequest;
use App\Http\Requests\FinanceExpenseItem\UpdateFinanceExpenseItemRequest;

class FinanceExpenseItemController extends Controller
{
    /**
     * @var FinanceExpenseItemRepository
     */
    protected $financeExpenseItemRepository;

    /**
     * FinanceExpenseItemController constructor.
     *
     * @param FinanceExpenseItemRepository $financeExpenseItemRepository
     */
    public function __construct(FinanceExpenseItemRepository $financeExpenseItemRepository)
    {
        $this->financeExpenseItemRepository = $financeExpenseItemRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // $finance_expenses = $this->financeExpenseItemRepository->all();
        $finance_expenses = FinanceExpenseItem::all();

        return new FinanceExpenseItemCollection($finance_expenses->load(['finance_account','finance_expense']));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CreateFinanceExpenseItemRequest $request)
    {
        $finance_expense = $this->financeExpenseItemRepository->create($request->all());

        return new FinanceExpenseItemResource($finance_expense->load(['finance_account','finance_expense']));
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\FinanceExpense  $finance_expense
     * @return \Illuminate\Http\Response
     */
    public function show(FinanceExpenseItem $financeExpenseItem)
    {
        return new FinanceExpenseItemResource($financeExpenseItem->load(['finance_account','finance_expense']));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\FinanceExpense  $finance_expense
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateFinanceExpenseItemRequest $request, FinanceExpenseItem $financeExpenseItem)
    {
        $finance_expense = $this->financeExpenseItemRepository->update($financeExpenseItem, $request->all());

        return new FinanceExpenseItemResource($finance_expense->load(['finance_account','finance_expense']));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\FinanceExpenseItem  $finance_expense_item
     * @return \Illuminate\Http\Response
     */
    public function destroy(FinanceExpenseItem $finance_expense_item)
    {
        $this->financeExpenseItemRepository->destroy($finance_expense_item);

        return response()->json(['status' => 1], Response::HTTP_OK);
    }
}
