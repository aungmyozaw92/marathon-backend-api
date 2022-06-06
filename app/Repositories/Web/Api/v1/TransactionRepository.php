<?php

namespace App\Repositories\Web\Api\v1;

use App\Models\City;
use App\Models\Agent;
use App\Models\Branch;
use App\Models\Merchant;
use App\Models\Attachment;
use App\Models\Transaction;
use App\Models\AccountInformation;
use App\Repositories\BaseRepository;
use Illuminate\Support\Facades\Storage;
use App\Repositories\Web\Api\v1\AttachmentRepository;
use App\Services\SmsService;

class TransactionRepository extends BaseRepository
{
    /**
     * @return string
     */
    public function model()
    {
        return Transaction::class;
    }

    /**
     * @param array $data
     *
     * @return Transaction
     */
    public function create(array $data)
    {
        $hq_account = getHqAccount();
        $city = City::findOrFail($data['city_id']);

        if ($city->branch) {
            $account = $city->branch->account;
            $total_balance = $account->balance;
        } else {
            if (isset($data['agent_id']) && $data['agent_id']) {
                $agent = Agent::findOrFail($data['agent_id']);
                $account = $agent->account;
                $total_balance = $account->balance + $agent->pending_balance();
            }else{
                $account = $city->agent->account;
                $total_balance = $account->balance + $city->agent->pending_balance();
            }
            
        }

        if ($account) {
            if ($data['type'] == 'Topup') {
                $to_account_id = $hq_account->id;
                $from_account_id = $account->id;
            } else {
                $from_account_id = $hq_account->id;
                $to_account_id = $account->id;
             
                if ($data['amount'] > $total_balance) {
                    return $responses = [
                        'status' => 2,
                        'message' => 'Insufficient amount.'
                    ];
                }
            }
            $transaction_no = $this->get_transaction_id();
            $data['transaction_no'] = $transaction_no;
            $data['from_account_id'] = $from_account_id;
            $data['to_account_id'] = $to_account_id;
            $data['account_information_id'] =  isset($data['account_information_id']) ? $data['account_information_id'] : null;
            if (isset($data['account_information_id']) && $account_information_id = $data['account_information_id']) {
                $accountInformation = AccountInformation::findOrFail($data['account_information_id']);
                $data['account_name'] = $accountInformation->account_name;
                $data['account_no'] = $accountInformation->account_no;
                $data['bank_id'] = $accountInformation->bank_id;
            }
            $transaction = $this->create_transaction($data);

            $this->create_journal($transaction);

            $responses = [
                'status' => 1,
                'data' => $transaction
            ];

            return $responses;
        }
    }
    /**
     * @param array $data
     *
     * @return Transaction
     */
    public function create_topup(array $data) : Transaction
    {
        $from_account_id = Merchant::findOrFail($data['from_account_id'])->account->id;
        if ($data['to_account_type'] == 'HQ') {
            $to_account_id = getHqAccount()->id;
        } elseif ($data['to_account_type'] == 'Branch') {
            $to_account_id =  Branch::find($data['to_account_id'])->account->id;
        } else {
            $to_account_id =  Agent::find($data['to_account_id'])->account->id;
        }

        $transaction_no = $this->get_transaction_id();
        $data['transaction_no'] = $transaction_no;
        $data['from_account_id'] = $from_account_id;
        $data['to_account_id'] = $to_account_id;
        $data['type'] = 'Topup';
        $data['account_information_id'] =  isset($data['account_information_id']) ? $data['account_information_id'] : null;
        if (isset($data['account_information_id']) && $account_information_id = $data['account_information_id']) {
            $accountInformation = AccountInformation::findOrFail($data['account_information_id']);
            $data['account_name'] = $accountInformation->account_name;
            $data['account_no'] = $accountInformation->account_no;
            $data['bank_id'] = $accountInformation->bank_id;
        }
        $transaction = $this->create_transaction($data);

        $this->create_journal($transaction);

        return $transaction;
    }
    /**
     * @param array $data
     *
     * @return Transaction
     */
    public function create_withdraw(array $data)
    {
        $merchant = Merchant::find($data['to_account_id']);
        $to_account = $merchant->account;
        $to_account_id = $to_account->id;
        
        // if ($merchant->id === 142) {
            if ($data['amount'] > $to_account->balance + $merchant->pending_balance()) {
                return $responses = ['status' => 2, 'message' => 'Insufficient amount.'];
            }
        // }else{
        //     if ($data['amount'] != $to_account->balance + $merchant->pending_balance()) {
        //         return $responses = ['status' => 2, 'message' => 'Insufficient amount.'];
        //     }
        // }

        if ($data['from_account_type'] == 'HQ') {
            $from_account_id = getHqAccount()->id;
        } elseif ($data['from_account_type'] == 'Branch') {
            $from_account_id =  Branch::find($data['from_account_id'])->account->id;
        } else {
            $from_account_id =  Agent::find($data['from_account_id'])->account->id;
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

        $responses = [
                'status' => 1,
                'data' => $transaction
            ];
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
            'note' => isset($data['note'])? $data['note']:null,
            'status' => isset($data['status'])? $data['status']:0,
            'extra_amount' => isset($data['extra_amount'])? $data['extra_amount']:0,
            'account_information_id' =>  isset($data['account_information_id']) ? $data['account_information_id'] : null,
            'account_name' => isset($data['account_name']) ? $data['account_name'] : null,
            'account_no' => isset($data['account_no']) ? $data['account_no'] : null,
            'bank_id' => isset($data['bank_id']) ? $data['bank_id'] : null,
            'created_by' => auth()->user() ? auth()->user()->id : 1,
            'created_by_id' => auth()->user() ? auth()->user()->id : 1,
            'created_by_type' => 'Staff'
        ]);
    }

    /**
     * @param Transaction $transaction
     * @param array $data
     *
     * @return mixed
     */
    public function update(Transaction $transaction, array $data) : Transaction
    {
        if($transaction->type != 'Agent Reward'){
            $transaction->status = $data['status'];
            $transaction->hq_balance = getHqAccount()->balance;
            if ($transaction->from_account->accountable_type != 'HQ') {
                $transaction->other_account_balance = $transaction->from_account->balance;
            } elseif ($transaction->to_account->accountable_type != 'HQ') {
                $transaction->other_account_balance = $transaction->to_account->balance;
            }
            if ($transaction->isDirty()) {
                $transaction->updated_by = auth()->user()->id;
                $transaction->save();
            }
        }

        // $journalRepository = new JournalRepository();
        if (($transaction->from_account->accountable_type == 'Merchant' &&
            ($transaction->to_account->accountable_type == 'Branch' ||$transaction->to_account->accountable_type == 'Agent')) ||
            (($transaction->from_account->accountable_type == 'Branch' ||
             $transaction->from_account->accountable_type == 'Agent') &&
             $transaction->to_account->accountable_type == 'Merchant')) {
            $transaction->to_account->balance -= $transaction->amount+$transaction->extra_amount;
            $transaction->to_account->save();
            $transaction->from_account->balance += $transaction->amount+$transaction->extra_amount;
            $transaction->from_account->save();
        } else {
            if ($transaction->type == 'Topup') {
                $transaction->to_account->balance += $transaction->amount+$transaction->extra_amount;
                $transaction->to_account->save();
                $transaction->from_account->balance += $transaction->amount+$transaction->extra_amount;
                $transaction->from_account->save();
            } elseif($transaction->type == 'Withdraw') {
                $transaction->to_account->balance -= $transaction->amount+$transaction->extra_amount;
                $transaction->to_account->save();
                $transaction->from_account->balance -= $transaction->amount+$transaction->extra_amount;
                $transaction->from_account->save();
            } elseif($transaction->type == 'Agent Reward') {
                $transaction->to_account->balance += $transaction->amount+$transaction->extra_amount;
                $transaction->to_account->save();
            }
        }

        $journal = $transaction->journal;
        if ($journal) {
            $journal->status = 1;
            $journal->balance_status = 1;
            $journal->timestamps = false;
            $journal->save();
        } else {
            $journalRepository = new JournalRepository();
            $journal_data = [
                'debit_account_id' => $transaction->from_account_id,
                'credit_account_id' => $transaction->to_account_id,
                'type' => 'Transaction',
                'resourceable_id' => $transaction->id,
                'amount' => $transaction->amount,
                'status' => 1,
            ];
            $journal = $journalRepository->create_journal($journal_data);
        }
        
        if (($transaction->from_account->accountable_type == 'Merchant' || $transaction->to_account->accountable_type == 'Merchant') 
            && $transaction->type == 'Withdraw') {
        
            if ($transaction->from_account->accountable_type == 'Merchant') {
            $default_branch = $transaction->from_account->merchant->merchant_associates->where('is_default', 1)->first();
            
            }elseif($transaction->to_account->accountable_type == 'Merchant'){
                $default_branch = $transaction->to_account->merchant->merchant_associates->where('is_default', 1)->first();
            }

            $sms_service = new SmsService();
            $phone = $default_branch->phones[0]->value;
            $sms_service->sendSmsRequest($phone,$transaction,'transaction_confirm');
        }

        return $transaction->refresh();
    }

    public function updateBankInformation(Transaction $transaction, array $data) : Transaction
    {
        if (isset($data['account_information_id']) && $account_information_id = $data['account_information_id']) {
            $accountInformation = AccountInformation::findOrFail($data['account_information_id']);
            $data['account_name'] = $accountInformation->account_name;
            $data['account_no'] = $accountInformation->account_no;
            $data['bank_id'] = $accountInformation->bank_id;
        }

        $transaction->note = isset($data['note']) ? $data['note'] : null;
        $transaction->account_information_id = isset($data['account_information_id']) ? $data['account_information_id'] : null;
        $transaction->account_name = isset($data['account_name']) ? $data['account_name'] : null;
        $transaction->account_no = isset($data['account_no']) ? $data['account_no'] : null;
        $transaction->bank_id = isset($data['bank_id']) ? $data['bank_id'] : null;
        $transaction->save();

        return $transaction->refresh();
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

    public function upload(Transaction $transaction, array $data) : Transaction
    {
        if (isset($data['file']) && $data['file']) {
            $attachmentRepository = new AttachmentRepository();
            $attachmentRepository->create_attachment($transaction, $data['file']);
        }

        return $transaction->refresh();
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

    //
    public function update_journal(Transaction $transaction, array $data) : Transaction
    {
        $journalRepository = new JournalRepository();
        $journal_data = [
                'debit_account_id' => $transaction->from_account_id,
                'credit_account_id' => $transaction->to_account_id,
                'type' => 'Transaction',
                'resourceable_id' => $transaction->id,
                'amount' => $transaction->amount,
                'status' => 1,
                'created_at' => $transaction->updated_at
            ];
            
        $journal = $journalRepository->create_journal($journal_data);
        $journal->created_at =$transaction->updated_at;
        $journal->save();
        
        return $transaction->refresh();
    }

    /**
     * @param Transaction $transaction
     */
    public function destroy(Transaction $transaction)
    {
        $deleted = $this->deleteById($transaction->id);

        if ($deleted) {
            $transaction->deleted_by = auth()->user()->id;
            $transaction->save();
        }
    }
}
