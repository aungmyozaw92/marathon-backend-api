<?php

namespace App\Repositories\Web\Api\v1\MerchantDashboard;

use App\Models\Branch;
use App\Models\Merchant;
use App\Models\Transaction;
use App\Models\AccountInformation;
use App\Repositories\BaseRepository;
use Illuminate\Support\Facades\Hash;
use App\Repositories\Web\Api\v1\JournalRepository;

class TransactionRepository extends BaseRepository
{
    /**
     * @return string
     */
    public function model()
    {
        return Transaction::class;
    }

    public function create_withdraw(array $data)
    {
        $merchant = Merchant::find(auth()->user()->id);
        if (!$merchant) {
            return $responses = ['status' => 2, 'message' => 'Merchant not exist.'];
        }
        $to_account = $merchant->account;
        $to_account_id = $to_account->id;
        if ($data['amount'] > $to_account->balance + $merchant->pending_balance()) {
            return $responses = ['status' => 2, 'message' => 'Insufficient amount.'];
        }
        $branch =  Branch::where('city_id', $merchant->city_id)->first();
        if ($branch) {
            $data['from_account_type'] = 'Branch';
            $from_account_id = $branch->account->id;
        } else {
            $data['from_account_type'] = 'HQ';
            $from_account_id = getHqAccount()->id;
        }

        $transaction_no = $this->get_transaction_id();
        $data['transaction_no'] = $transaction_no;
        $data['from_account_id'] = $from_account_id;
        $data['to_account_id'] = $to_account_id;
        $data['type'] = 'Withdraw';
        $data['account_information_id'] =  isset($data['account_information_id']) ? $data['account_information_id'] : null;
        if (isset($data['account_information_id']) && $account_information_id = $data['account_information_id']) {
            $accountInformation = AccountInformation::findOrFail($data['account_information_id']);
            $data['account_name'] = $accountInformation->account_name;
            $data['account_no'] = $accountInformation->account_no;
            $data['bank_id'] = $accountInformation->bank_id;
        }
        $transaction = $this->create_transaction($data);

        $this->create_journal($transaction);

        $responses = ['status' => 1, 'message' => 'Successfully Requested to Withdraw!'];
        return $responses;
    }

    public function create_transaction($data)
    {
        if (isset($data['account_information_id']) && $account_information_id = $data['account_information_id']) {
            $accountInformation = AccountInformation::findOrFail($data['account_information_id']);
            $data['account_name'] = $accountInformation->account_name;
            $data['account_no'] = $accountInformation->account_no;
            $data['bank_id'] = $accountInformation->bank_id;
        }

        return Transaction::create([
            'transaction_no' => $data['transaction_no'],
            'from_account_id' => $data['from_account_id'],
            'to_account_id' => $data['to_account_id'],
            'amount' => (int) $data['amount'],
            'type' => $data['type'],
            'note' => isset($data['note']) ? $data['note'] : null,
            'status' => isset($data['status']) ? $data['status'] : 0,
            'extra_amount' => isset($data['extra_amount']) ? $data['extra_amount'] : 0,
            'account_information_id' =>  isset($data['account_information_id']) ? $data['account_information_id'] : null,
            'account_name' => isset($data['account_name']) ? $data['account_name'] : null,
            'account_no' => isset($data['account_no']) ? $data['account_no'] : null,
            'bank_id' => isset($data['bank_id']) ? $data['bank_id'] : null,
            'created_by' => auth()->user() ? auth()->user()->id : null,
            'created_by_id' => auth()->user() ? auth()->user()->id : null,
            'created_by_type' => 'Merchant'
        ]);
    }

    public function create_journal($transaction)
    {
        $journalRepository = new JournalRepository();
        $journal_data = [
            'debit_account_id' => $transaction->from_account_id,
            'credit_account_id' => $transaction->to_account_id,
            'type' => 'Transaction',
            'resourceable_id' => $transaction->id,
            'amount' => $transaction->amount,
            'status' => $transaction->status ? $transaction->status : 0,
        ];
        $journal = $journalRepository->create_journal($journal_data);

        return $journal->refresh();
    }

    public function get_transaction_id()
    {
        $transaction_id = 0;
        if (Transaction::count()) {
            $transaction_id  = Transaction::latest()->orderBy('id', 'desc')->withTrashed()->first()->id;
        }
        $transaction_id += 1;
        return 'TN' . str_pad($transaction_id, 6, '0', STR_PAD_LEFT);
    }
}
