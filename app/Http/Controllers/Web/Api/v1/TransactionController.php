<?php

namespace App\Http\Controllers\Web\Api\v1;

use App\Models\City;
use App\Models\Account;
use App\Models\Merchant;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Exports\TransactionData;
use App\Http\Controllers\Controller;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Requests\Transaction\FileRequest;
use App\Http\Requests\Transaction\CreateTopupRequest;
use App\Repositories\Web\Api\v1\TransactionRepository;
use App\Http\Resources\Transaction\TransactionResource;
use App\Http\Requests\Transaction\CreateWithdrawRequest;
use App\Http\Resources\Transaction\TransactionCollection;
use App\Http\Requests\Transaction\CreateTransactionRequest;
use App\Http\Requests\Transaction\UpdateTransactionRequest;
use App\Http\Resources\HqTransaction\HqTransactionCollection;
use App\Http\Requests\Transaction\UpdateTransactionBankInformationRequest;

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
    public function __construct(TransactionRepository $transactionRepository)
    {
        $this->transactionRepository = $transactionRepository;
    }

    public function index()
    {
        
        if (request()->has('export')) {
            $filename = 'transactions.xlsx';
            Excel::store(new TransactionData, $filename, 'public', null, [
                    'visibility' => 'public',
            ]);
            $file = storage_path('/app/public/transactions.xlsx');
            return response()->download($file)->deleteFileAfterSend();
        }

        $transactions =  Transaction::with([
                                'from_account' => function ($query) {
                                    $query->withTrashed();
                                },
                                'to_account'  => function ($query) {
                                    $query->withTrashed();
                                },
                                'bank'  => function ($query) {
                                    $query->withTrashed();
                                },
                                'account_information'  => function ($query) {
                                    $query->withTrashed();
                                },
                                'account_information.bank'  => function ($query) {
                                    $query->withTrashed();
                                },
                                'attachments','created_by_merchant'
                                ])
                                // ,
                                // 'attachments' => function ($query) {
                                //     $query->withTrashed();
                                // }
                            ->filter(request()->all())
                            ->order(request()->only([
                                'sortBy', 'orderBy'
                            ]));

        if (request()->get('associated_merchant') === "true") {
            $merchants_id = Merchant::where('staff_id', auth()->user()->id)->get()->pluck('id');

            $transactions = $transactions->where(function ($q) use ($merchants_id) {
                $q->whereHas('to_account', function ($qr) use ($merchants_id) {
                    $qr->whereIn('accountable_id', $merchants_id)
                        ->where('accountable_type', 'Merchant');
                })
                ->orWhereHas('from_account', function ($qr) use ($merchants_id) {
                    $qr->whereIn('accountable_id', $merchants_id)
                        ->where('accountable_type', 'Merchant');
                });
            });
        }

        if (request()->has('paginate')) {
            $transactions = $transactions->paginate(request()->get('paginate'));
        } else {
            $transactions = $transactions->get();
        }
        
        return new TransactionCollection($transactions);
    }

    public function hqBalanceLists()
    {
        $transactions = Transaction::with(['from_account','to_account'])->getHqBalanceFilter(request()->only(['start_date', 'end_date']))->get();
        $hq_balance = getHqAccount()->balance;
        $total_branch_balance = Account::where('accountable_type', 'Branch')->sum('balance');
        $total_agent_balance = Account::where('accountable_type', 'Agent')->sum('balance');
        $total_merchant_balance = Account::where('accountable_type', 'Merchant')->sum('balance');
        return response()->json([
            'status' => 1,
            'hq_balance' => $hq_balance,
            'total_branch_balance' => $total_branch_balance,
            'total_agent_balance' => $total_agent_balance,
            'total_merchant_balance' => $total_merchant_balance,
            'transactions' => new HqTransactionCollection($transactions),
        ]);
        //return new HqTransactionCollection($transactions);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CreateTransactionRequest $request)
    {
        $transaction = $this->transactionRepository->create($request->all());
        if ($transaction) {
            if ($transaction['status'] == 1) {
                return new TransactionResource($transaction['data']->load(['from_account','to_account','attachments', 'bank', 'account_information', 'account_information.bank']));
            } else {
                return $transaction;
            }
        } else {
            return response()->json([
                'status' => 2, 'message' => 'Selected city does not have an account'
            ], Response::HTTP_OK);
        }
    }
    
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function create_withdraw(CreateWithdrawRequest $request)
    {
        $transaction = $this->transactionRepository->create_withdraw($request->all());
        if ($transaction['status'] == 1) {
            return new TransactionResource($transaction['data']->load(['from_account','to_account','attachments', 'bank', 'account_information', 'account_information.bank']));
        } else {
            return $transaction;
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function create_topup(CreateTopupRequest $request)
    {
        $transaction = $this->transactionRepository->create_topup($request->all());
        return new TransactionResource($transaction->load(['from_account','to_account','attachments', 'bank', 'account_information', 'account_information.bank']));
    }

    /**
        * Display the specified resource.
        *
        * @param  \App\TrackingStatus  $trackingStatus
        * @return \Illuminate\Http\Response
        */
    public function show(Transaction $transaction)
    {
        return new TransactionResource($transaction->load(['from_account','to_account','attachments', 'bank', 'account_information', 'account_information.bank']));
    }
   
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Transaction  $transaction
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateTransactionRequest $request, Transaction $transaction)
    {
        if (!$transaction->status) {
            $transaction = $this->transactionRepository->update($transaction, $request->all());
            return new TransactionResource($transaction->load(['from_account','to_account','attachments', 'bank', 'account_information', 'account_information.bank']));
        } else {
            return response()->json([
                        'status' => 2, 'message' => 'Transaction is already confirmed.'
                    ], Response::HTTP_OK);
        }
    }

    public function updateBankInformation(UpdateTransactionBankInformationRequest $request, Transaction $transaction)
    {
        if (!$transaction->status) {
            $transaction = $this->transactionRepository->updateBankInformation($transaction, $request->all());
            return new TransactionResource($transaction->load(['from_account','to_account','attachments', 'bank', 'account_information', 'account_information.bank']));
        } else {
            return response()->json([
                        'status' => 2, 'message' => 'Transaction is already confirmed.'
                    ], Response::HTTP_OK);
        }
    }

    public function update_journal(Request $request, Transaction $transaction)
    {
        if ($transaction->journal) {
            return response()->json([
                'status' => 2, 'message' => 'This transaction have already journal.'
            ], Response::HTTP_OK);
        }
        if ($transaction->status) {
            // dd('hi');
            $transaction = $this->transactionRepository->update_journal($transaction, $request->all());
            return response()->json([
                'status' => 1, 'message' => 'Ok Cool!'
            ], Response::HTTP_OK);
        // return new TransactionResource($transaction->load(['from_account','to_account','attachments']));
        } else {
            return response()->json([
                        'status' => 2, 'message' => 'Transaction is not already confirmed.'
                    ], Response::HTTP_OK);
        }
    }

    public function upload(FileRequest $request, Transaction $transaction)
    {
        $transaction = $this->transactionRepository->upload($transaction, $request->all());

        return new TransactionResource($transaction->load(['from_account','to_account','attachments', 'bank', 'account_information', 'account_information.bank']));
    }
    /**
       * Remove the specified resource from storage.
       *
       * @param  \App\Transaction  $transaction
       * @return \Illuminate\Http\Response
       */
    public function destroy(Transaction $transaction)
    {
        if ($transaction->status) {
            return response()->json([ 'status' => 2, 'message'=>'Cannot delete'], Response::HTTP_OK);
        }
        if ($transaction->journal) {
            $transaction->journal->delete();
        }
        $this->transactionRepository->destroy($transaction);
       
        return response()->json([ 'status' => 1 ], Response::HTTP_OK);
    }

    public function delete_pending_transactions(Request $request)
    {
        $count = 0;
       
        // $pending_transactions = Transaction::where('status', 0)->get();
        // dd($request->get('data'));
        foreach($request->get('data') as $id){
            $transaction = Transaction::findOrFail($id);
            if (!$transaction->status) {
                if ($transaction->journal) {
                    $transaction->journal->delete();
                }
                $this->transactionRepository->destroy($transaction);
                $count++;
            }
        }
        return response()->json([ 'status' => 1,'count'=>$count, 'message' => 'Pending transactions have been deleted' ], Response::HTTP_OK);
    }
    public function updateNull(){
        $transaction = Transaction::whereNull('created_by_type')->get();

        foreach ($transaction as $t) {
            $t->created_by_type = 'Staff';
            $t->created_by_id = $t->created_by;
            $t->save();
        }
        dd('ok');
    }
}
