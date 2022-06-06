<?php

namespace App\Http\Controllers\Web\Api\v1\ThirdParty;

use App\Models\Journal;
use App\Models\Transaction;
use App\Http\Controllers\Controller;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\MerchantTransactionHistorySheet;
use App\Exports\MerchantTransactionHistoryForThirdpartySheet;
use App\Http\Resources\ThirdParty\Transaction\TransactionCollection;
use App\Http\Resources\ThirdParty\TransactionJournal\TransactionJournalCollection;

class TransactionController extends Controller
{
    public function index()
    {
        
        $account_id = auth()->user()->account->id;
        $transactions =  Transaction::with('attachments')->where('from_account_id', $account_id)
                                    ->orWhere('to_account_id', $account_id);
        
        if (request()->get('paginate') && is_numeric(request()->get('paginate'))) {
            $transactions = $transactions->paginate(25);
        } else {
            $transactions = $transactions->get();
        }
        
        return new TransactionCollection($transactions);
    }

    public function transaction_lists()
    {
        $account_id = auth()->user()->account->id;
        if (request()->has('export')) {
            $filename = 'merchant_transaction.xlsx';
            Excel::store(new MerchantTransactionHistoryForThirdpartySheet($account_id), $filename, 'public', null, [
                    'visibility' => 'public',
            ]);
            $file = storage_path('/app/public/merchant_transaction.xlsx');
            return response()->download($file)->deleteFileAfterSend();
        }
        
        $journals =  Journal::with(['resourceable',
                                'resourceable.payment_type',
                                'resourceable.delivery_status',
                                'resourceable.receiver',
                                'resourceable.receiver_city',
                                'resourceable.sender_city',
                                'resourceable.pickup.sender',
                                'credit_account','debit_account'
                                ])
                            ->getTransactionJournal($account_id, request()->only([
                                'start_date', 'end_date',
                            ]));
        if (request()->get('paginate') && is_numeric(request()->get('paginate'))) {
            $journals = $journals->paginate(20);
        } else {
            $journals = $journals->get();
        }
        return new TransactionJournalCollection($journals);
    }
}
