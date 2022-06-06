<?php

namespace App\Http\Controllers\Web\Api\v1;

use App\Models\FinanceAccount;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use App\Repositories\Web\Api\v1\FinanceAccountRepository;
use App\Http\Resources\FinanceAccount\FinanceAccountResource;
use App\Http\Resources\FinanceAccount\FinanceAccountCollection;
use App\Http\Requests\FinanceAccount\CreateFinanceAccountRequest;
use App\Http\Requests\FinanceAccount\UpdateFinanceAccountRequest;

class FinanceAccountController extends Controller
{
    /**
     * @var FinanceAccountRepository
     */
    protected $financeAccountRepository;

    /**
     * FinanceAccountController constructor.
     *
     * @param FinanceAccountRepository $financeAccountRepository
     */
    public function __construct(FinanceAccountRepository $financeAccountRepository)
    {
        $this->financeAccountRepository = $financeAccountRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // $finance_account = $this->financeAccountRepository->all();
        $finance_accounts = FinanceAccount::with('finance_nature','finance_master_type','finance_account_type',
                                            'finance_group','branch','finance_tax','finance_code','actorable'
                                            )->get();

        return new FinanceAccountCollection($finance_accounts);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CreateFinanceAccountRequest $request)
    {
        $finance_account = $this->financeAccountRepository->create($request->all());

        return new FinanceAccountResource($finance_account->load(['finance_nature','finance_master_type','finance_account_type',
        'finance_group','branch','finance_tax','finance_code']));
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\FinanceAccount  $finance_account
     * @return \Illuminate\Http\Response
     */
    public function show(FinanceAccount $financeAccount)
    {
        return new FinanceAccountResource($financeAccount->load(['finance_nature','finance_master_type','finance_account_type',
        'finance_group','branch','finance_tax','finance_code']));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\FinanceAccount  $finance_account
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateFinanceAccountRequest $request, FinanceAccount $financeAccount)
    {
        $finance_account = $this->financeAccountRepository->update($financeAccount, $request->all());

        return new FinanceAccountResource($finance_account->load(['finance_nature','finance_master_type','finance_account_type',
        'finance_group','branch','finance_tax','finance_code']));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\FinanceAccount  $finance_account
     * @return \Illuminate\Http\Response
     */
    public function destroy(FinanceAccount $finance_account)
    {
        $this->financeAccountRepository->destroy($finance_account);

        return response()->json(['status' => 1], Response::HTTP_OK);
    }
}
