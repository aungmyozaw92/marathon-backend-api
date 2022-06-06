<?php

namespace App\Observers;

use App\Models\DeliSheetHistory;
use App\Models\DeliSheet;
use App\Models\Pickup;
use App\Services\FirebaseService;
class DeliSheetObserver
{
    /**
     * Handle the deli sheet "created" event.
     *
     * @param  \App\DeliSheet  $deliSheet
     * @return void
     */
	private $firebaseService;
	public function __construct(FirebaseService $firebaseService)
	{
		$this->firebaseService = $firebaseService;
	}
    public function created(DeliSheet $deliSheet)
    {
        $deliSheet->delisheet_invoice = $deliSheet->id;
        $deliSheet->save();
        $logStatusId = getStatusId('new_delisheet');
        DeliSheetHistory::create([
            'delisheet_id' => $deliSheet->id,
            'log_status_id' => $logStatusId,
            'created_by' => isset(auth()->user()->id) ? auth()->user()->id : 1
        ]);
    }

    /**
     * Handle the deli sheet "updated" event.
     *
     * @param  \App\DeliSheet  $deliSheet
     * @return void
     */
    public function updated(DeliSheet $deliSheet)
    {
        $changes = $deliSheet->getChanges();
        $expected_columns = [
            'delivery_id',
            'is_closed',
            'is_paid',
            'note',
            'date'
        ];
        $changes = array_only($changes, $expected_columns);
        $transformedInputs = [];
        foreach ($changes as $key => $value) {
            $previous = $deliSheet->getOriginal($key);
            $next = $value;
			$transformedInputs[] = transformedDelisheetsAttribute($key, $previous, $next);
			$vouchers = $deliSheet->vouchers()->whereNotIn('delivery_status_id',[8])->pluck('vouchers.id');
			if ($key == 'is_closed' && $deliSheet->getOriginal('is_closed') == 0 && $deliSheet->is_closed == 1) {
				$pickups = Pickup::whereHas('vouchers', function ($voucherQuery) use ($vouchers) {
					$voucherQuery->where('sender_type', 'Merchant')->whereIn('id', $vouchers);
				})
					->with(['merchant' => function ($pickupQuery) {
						return $pickupQuery->whereHas('device_tokens')
							->with(['device_tokens' => function ($query) {
								return $query->where('is_active', 1)->select('referable_id', 'device_token')->get();
							}])
							->select('id', 'firestore_document', 'name')
							->get()->toArray();
					}])
					->addSelect('sender_id')
					->get()
					->toArray();
				foreach ($pickups as $pickup) {
					if (!empty($pickup['merchant']['device_tokens'])) {
						$deviceTokens = array_column($pickup['merchant']['device_tokens'], 'device_token');
						$payload = [
							'receiver' => $pickup['merchant']['name'],
							'device_tokens' => $deviceTokens,
							'type' => "failed_attempt",
							'body' => 'သင်ပို့ဆောင်လိုက်သော ပါဆယ်များမှ ပို့မရသော ပါဆယ် ရှိပါသည်။',
							'invoice' => $deliSheet->delisheet_invoice
						];
						$this->firebaseService->sendNotification($payload);
					}
				}
			}
        }

        foreach ($transformedInputs as $key => $value) {

            $logStatusId = getStatusId($value['status']);
            if (
                (
                    ($value['status'] == 'change_note' || $value['status'] == 'change_delivery_man' || $value['status'] == 'change_deliver_date')
                    &&
                    ($value['next'] != null))
                ||
                ($value['previous'] != null && $value['next'] != null)
                ||
                $value['status'] == 'close' || $value['status'] == 'receive_payment'
            ) {
                // if (is_bool($value['previous'])) {
                //     $value['previous'] = null;
                //     $value['next'] = null;
                // }
                DeliSheetHistory::create([
                    'delisheet_id' => $deliSheet->id,
                    'log_status_id' => $logStatusId,
                    'previous' => $value['previous'],
                    'next' => $value['next'],
                    'created_by' => auth()->user()->id
                ]);
            }
        }
    }

    /**
     * Handle the deli sheet "deleted" event.
     *
     * @param  \App\DeliSheet  $deliSheet
     * @return void
     */
    public function deleted(DeliSheet $deliSheet)
    {
        $logStatusId = getStatusId('delete_delisheet');

        DeliSheetHistory::create([
            'delisheet_id' => $deliSheet->id,
            'log_status_id' => $logStatusId,
            'previous' => $deliSheet->delisheet_invoice,
            'created_by' => auth()->user()->id
        ]);
    }

    /**
     * Handle the deli sheet "restored" event.
     *
     * @param  \App\DeliSheet  $deliSheet
     * @return void
     */
    public function restored(DeliSheet $deliSheet)
    {
        //
    }

    /**
     * Handle the deli sheet "force deleted" event.
     *
     * @param  \App\DeliSheet  $deliSheet
     * @return void
     */
    public function forceDeleted(DeliSheet $deliSheet)
    {
        //
    }
}
