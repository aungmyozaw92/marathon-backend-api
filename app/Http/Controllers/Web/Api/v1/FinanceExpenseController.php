<?php

namespace App\Http\Controllers\Web\Api\v1;

use App\Models\FinanceExpense;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use App\Repositories\Web\Api\v1\FinanceExpenseRepository;
use App\Http\Resources\FinanceExpense\FinanceExpenseResource;
use App\Http\Resources\FinanceExpense\FinanceExpenseCollection;
use App\Http\Requests\FinanceExpense\CreateFinanceExpenseRequest;
use App\Http\Requests\FinanceExpense\UpdateFinanceExpenseRequest;
use App\Http\Requests\Transaction\FileRequest;
use App\Http\Resources\Attachment\AttachmentCollection;

class FinanceExpenseController extends Controller
{
    /**
     * @var FinanceExpenseRepository
     */
    protected $financeExpenseRepository;

    /**
     * FinanceExpenseController constructor.
     *
     * @param FinanceExpenseRepository $financeExpenseRepository
     */
    public function __construct(FinanceExpenseRepository $financeExpenseRepository)
    {
        $this->financeExpenseRepository = $financeExpenseRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $finance_expenses = FinanceExpense::with([
            'branch', 'staff', 'finance_expense_items',
            'finance_expense_items.from_finance_account',
            'finance_expense_items.to_finance_account'
        ])->filter(request()->only(['expense_invoice', 'start_date', 'end_date', 'issuer']))
            ->where('branch_id', auth()->user()->city->branch->id)->orderBy('id', 'desc');
        if (request()->has('paginate')) {
            $finance_expenses = $finance_expenses->paginate(request()->get('paginate'));
        } else {
            $finance_expenses = $finance_expenses->get();
        }
        // return new FinanceExpenseCollection($finance_expenses->load(['branch', 'staff', 'finance_expense_items', 'finance_expense_items.from_finance_account', 'finance_expense_items.to_finance_account']));
        return new FinanceExpenseCollection($finance_expenses);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CreateFinanceExpenseRequest $request)
    {
        // dd($request->all());
        $finance_expense = $this->financeExpenseRepository->create($request->all());

        return new FinanceExpenseResource($finance_expense->load(['branch', 'staff', 'finance_expense_items', 'finance_expense_items.from_finance_account', 'finance_expense_items.to_finance_account']));
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\FinanceExpense  $finance_expense
     * @return \Illuminate\Http\Response
     */
    public function show(FinanceExpense $financeExpense)
    {
        return new AttachmentCollection($financeExpense->attachments);
        // return new FinanceExpenseResource($financeExpense->load(['branch', 'finance_expense_items']));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\FinanceExpense  $finance_expense
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateFinanceExpenseRequest $request, FinanceExpense $financeExpense)
    {
        $finance_expense = $this->financeExpenseRepository->update($financeExpense, $request->all());

        return new FinanceExpenseResource($finance_expense->load(['branch', 'staff', 'finance_expense_items', 'finance_expense_items.from_finance_account', 'finance_expense_items.to_finance_account']));
    }

    public function upload(FileRequest $request, FinanceExpense $financeExpense)
    {
        $finance_expense = $this->financeExpenseRepository->upload($financeExpense, $request->all());
        return new AttachmentCollection($financeExpense->attachments);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\FinanceExpense  $finance_expense
     * @return \Illuminate\Http\Response
     */
    public function destroy(FinanceExpense $finance_expense)
    {
        $this->financeExpenseRepository->destroy($finance_expense);

        return response()->json(['status' => 1], Response::HTTP_OK);
    }
}
