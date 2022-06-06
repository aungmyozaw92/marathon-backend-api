<?php

namespace App\Http\Controllers\Mobile\Api\v1\Agent;

use App\Models\Journal;
use App\Models\Voucher;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use App\Repositories\Web\Api\v1\JournalRepository;
use App\Http\Resources\Mobile\Agent\Journal\JournalCollection;
use App\Repositories\Mobile\Api\v1\Agent\TransactionRepository;
use App\Http\Resources\Mobile\Agent\Transaction\TransactionResource;
use App\Http\Resources\Mobile\Agent\Transaction\TransactionCollection;

class TransactionController extends Controller
{

    /**
     * @var transactionRepository
     */
    protected $transactionRepository;

    /**
     * AgentController constructor.
     *
     * @param transactionRepository $transactionRepository
     */
    public function __construct(
        TransactionRepository $transactionRepository,
        JournalRepository $journalRepository
    ) {
        $this->journalRepository = $journalRepository;
        $this->transactionRepository = $transactionRepository;
    }

    public function index()
    {
        $account_id = auth()->user()->account->id;
        $transactions =  Transaction::where('from_account_id', $account_id)
                                    ->orWhere('to_account_id', $account_id)
                                    ->filter(request()->all())
                                    ->paginate(20);
        
        return new TransactionCollection($transactions);
    }
    /**
            * Display the specified resource.
            *
            * @param  \App\TrackingStatus  $trackingStatus
            * @return \Illuminate\Http\Response
            */
    public function show(Transaction $transaction)
    {
        return new TransactionResource($transaction->load(['from_account','to_account','attachments']));
    }

    public function commission_history()
    {
        $journals =  Journal::with(['resourceable','credit_account','debit_account'])
                              ->filterAgentCommission(request()->only([
                                    'start_date', 'end_date', 'commission' 
                                ]))->paginate(25);
                          
        return new JournalCollection($journals);
    }

    public function finance_detail()
    {
        $uncollected_amount = Voucher::agentWaybillJobVoucher(request()->only(['start_date', 'end_date']))
                        ->sum('total_amount_to_collect');
        $commission_amount = Journal::filterAgentCreditLists(request()->only([
                                         'start_date', 'end_date' 
                                    ]))->sum('amount');
        $collected_amount = Journal::filterAgentDebitLists(request()->only([
                                         'start_date', 'end_date' 
                                    ]))->sum('amount');
        
                          
       return response()->json([
                'status' => 1,
                'data' => [
                    'commission_amount' => $commission_amount,
                    'collected_amount' => $collected_amount,
                    'uncollected_amount' => $uncollected_amount,
                    'bonus' => 0
                ],
            ], 200);
    }


}
