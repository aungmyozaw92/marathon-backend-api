<?php

namespace App\Repositories\Mobile\Api\v1\Agent;

use App\Models\Transaction;
use App\Repositories\BaseRepository;
use App\Repositories\Web\Api\v1\AttachmentRepository;

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
    public function create(array $data) : Transaction
    {
        $hq_account = getHqAccount();
        // $city = auth()->user()->city;

        // $account = $city->agent->account;
        $account = auth()->user()->account;


        if ($account) {             
            $to_account_id = $hq_account->id;
            $from_account_id = $account->id;                 
            $transaction_no = $this->get_transaction_id();

            $transaction = Transaction::create([
                'transaction_no' => $transaction_no,
                'from_account_id' => $from_account_id,
                'to_account_id' => $to_account_id,
                'amount' => $data['amount'],
                'type' => 'Topup',
                'note' => isset($data['note'])? $data['note']:null,
                'created_by' => auth()->user()->id,
            ]);
            $this->create_journal($transaction);

            if (isset($data['file']) && $data['file'] && $transaction) {
                $attachmentRepository = new AttachmentRepository();
                $attachmentRepository->create_attachment($transaction, $data['file']);
            }

            return $transaction;
        }
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
