<?php

namespace App\Observers;

use App\Models\Transaction;
use App\Services\FirebaseService;
class TransactionObserver
{
    /**
     * Handle the transaction "created" event.
     *
     * @param  \App\Models\Transaction  $transaction
     * @return void
     */
	private $firebaseService;
	public function __construct(FirebaseService $firebaseService)
	{
		$this->firebaseService = $firebaseService;
	}
    public function created(Transaction $transaction)
    {
        //
    }

    /**
     * Handle the transaction "updated" event.
     *
     * @param  \App\Models\Transaction  $transaction
     * @return void
     */
    public function updated(Transaction $transaction)
    {
		//
		if ($transaction->type == 'Withdraw' && !empty($transaction->to_account->merchant)) {
			$merchant = $transaction->to_account->merchant;
		} else {
			return;
		}
		if ($transaction->wasChanged('status') && $transaction->status == true) {
			$deviceTokens = (isset($merchant->device_tokens)) ? $merchant->device_tokens()->where('is_active', 1)->pluck('device_token')->toArray() : [];
			if (!empty($deviceTokens)) {
				$payload = [
					'receiver' => $merchant->name,
					'device_tokens' => $deviceTokens,
					'type' => 'confirmed_withdraw',
					'body' => 'သင့်ငွေထုတ်ယူရန် အတည်ပြုပါသည်။သတ်မှတ်ထားသော ငွေလက်ခံသည့် အမျိုးအစားမှ ထုတ်ယူနိုင်ပါသည်။',
					'invoice' => $transaction->transaction_invoice
				];
				$this->firebaseService->sendNotification($payload);
			}
		}
    }

    /**
     * Handle the transaction "deleted" event.
     *
     * @param  \App\Transaction  $transaction
     * @return void
     */
    public function deleted(Transaction $transaction)
    {
        //
    }

    /**
     * Handle the transaction "restored" event.
     *
     * @param  \App\Models\Transaction  $transaction
     * @return void
     */
    public function restored(Transaction $transaction)
    {
        //
    }

    /**
     * Handle the transaction "force deleted" event.
     *
     * @param  \App\Models\Transaction  $transaction
     * @return void
     */
    public function forceDeleted(Transaction $transaction)
    {
        //
    }
}
