<?php

namespace App\Observers;

use App\Models\Customer;
use App\Models\DeliSheetVoucher;
use App\Models\Pickup;
use App\Models\Voucher;
use App\Models\VoucherHistory;
use App\Models\TrackingVoucher;
use App\Models\City;
use Illuminate\Support\Str;
use App\Services\FirebaseService;
class VoucherObserver
{
    /**
     * Handle the Voucher "created" event.
     *
     * @param  \App\Voucher  $voucher
     * @return void
     */
	private $firebaseService;
	public function __construct(FirebaseService $firebaseService)
	{
		$this->firebaseService = $firebaseService;
	}

    public function created(Voucher $voucher)
    {
        $voucher->voucher_invoice = $voucher->id;
        $voucher->uuid = Str::orderedUuid();
        $voucher->save();
        if ($voucher->pickup) {
            $voucher->pickup->qty = $voucher->pickup->vouchers()->count();
            $voucher->pickup->save();
        }
        $logStatusId = getStatusId('new_voucher');
        $voucher_history = new VoucherHistory([
            'voucher_id' => $voucher->id,
            'log_status_id' => $logStatusId,
        ]);
        auth()->user()->voucher_histories()->save($voucher_history);
        $tracking_status_id = getTrackingStatusId('info_received');
        $voucher->tracking_status()->attach($tracking_status_id, array('city_id' => auth()->user()->city_id, 'created_by' => auth()->user()->id));
    }

    /**
     * Handle the Voucher "updating" event.
     *
     * @param  \App\Voucher  $voucher
     * @return void
     */
    public function updating(Voucher $voucher)
    {
        //
    }

    /**
     * Handle the Voucher "updated" event.
     *
     * @param  \App\Voucher  $voucher
     * @return void
     */
    public function updated(Voucher $voucher)
    {
        if ($voucher->wasChanged('voucher_invoice') && $voucher->getOriginal('voucher_invoice') == null) {
            return;
        }
        $changed_columns = $voucher->getChanges();
        $expected_columns = [
            'receiver_id',
            'phone',
            'address',
            'payment_type_id',
            'call_status_id',
            'delivery_status_id',
            'store_status_id',
            'sender_city_id',
            'receiver_city_id',
            'sender_zone_id',
            'receiver_zone_id',
            'sender_bus_station_id',
            'receiver_bus_station_id',
            'remark',
            'is_closed'
        ];
        $changes = array_only($changed_columns, $expected_columns);
        if (empty($changes)) return;
        $transformedInputs = [];
        foreach ($changes as $key => $value) {
            $previous = $voucher->getOriginal($key);
            $next = $value;
            $transformedInputs[] = transformedVouchersAttribute($key, $previous, $next);
        }
        foreach ($transformedInputs as $key => $value) {
            $logStatusId = getStatusId($value['status']);
            if ((
                    ($value['status'] == 'change_note' || $value['status'] == 'remove_pickup_voucher') && ($value['previous'] != null || $value['next'] != null))
                || ($value['previous'] != null && $value['next'] != null) || $value['status'] == 'close'
            ) {
                $voucher_history = new VoucherHistory([
                    'voucher_id' => $voucher->id,
                    'log_status_id' => $logStatusId,
                    'previous' => $value['previous'],
                    'next' => $value['next'],
                ]);
                auth()->user() ? auth()->user()->voucher_histories()->save($voucher_history) : $voucher->created_by_merchant->voucher_histories()->save($voucher_history);
            }
        }

        if ($voucher->wasChanged('store_status_id') || $voucher->wasChanged('delivery_status_id') || $voucher->wasChanged('delivery_counter') || $voucher->wasChanged('is_return')) {
            $tracking_status_id = voucherTracker($voucher);
            if (!empty($tracking_status_id) || !$tracking_status_id) {
                $voucher->tracking_status()->attach($tracking_status_id, array('city_id' => auth()->user()->city_id, 'created_by' => auth()->user()->id));
            }
        }
		if($voucher->wasChanged('is_complete') && $voucher->receiver_id != null && $voucher->is_complete == 1) {
			$deviceTokens = isset($voucher->created_by_merchant) ? $voucher->created_by_merchant->device_tokens()->where('is_active', 1)->pluck('device_token')->toArray() : [];
			if (!empty($deviceTokens)) {
				$merchant_name = (isset($voucher->pickup) && isset($voucher->pickup->merchant)) && $voucher->pickup->merchant->id ?
					$voucher->pickup->merchant->name
					: (auth()->user() != null ? auth()->user()->name : $voucher->created_by_merchant->name);
				$payload = [
					'receiver' => $merchant_name,
					'device_tokens' => $deviceTokens,
					'type' => 'info_complete',
					'body' => 'ဝယ်ယူသူမှ အချက်အလက်ဖြည့်သွင်းလိုက်သော ပါဆယ် ရှိပါသည်။',
					'invoice' => $voucher->voucher_invoice
				];
				$this->firebaseService->sendNotification($payload);
			}
		}
		// if($voucher->wasChanged('is_closed') && $voucher->is_closed && $voucher->delivery_counter >1) {
		// 	$merchant_name = $voucher->pickup->merchant->name;
		// 	$this->firebaseService->cleanNotification(['receiver'=>$merchant_name,'invoice'=> $voucher->delisheets()->latest()->take(2)->get()[1]->delisheet_invoice]);
		// }
    }

    /**
     * Handle the Voucher "deleted" event.
     *
     * @param  \App\Voucher  $voucher
     * @return void
     */
    public function deleted(Voucher $voucher)
    {
        $logStatusId = getStatusId('delete_voucher');

        $voucher_history = new VoucherHistory([
            'voucher_id' => $voucher->id,
            'log_status_id' => $logStatusId,
            'previous' => $voucher->voucher_invoice,
        ]);
        auth()->user()->voucher_histories()->save($voucher_history);
        $trackingId = getTrackingStatusId('expired');
        $tracking_voucher = new TrackingVoucher([
            'voucher_id'    =>  $voucher->id,
            'tracking_status_id' => $trackingId,
            'created_by' => auth()->user()->id
        ]);
    }

    /**
     * Handle the Voucher "restored" event.
     *
     * @param  \App\Voucher  $voucher
     * @return void
     */
    public function restored(Voucher $voucher)
    {
        //
    }

    /**
     * Handle the Voucher "force deleted" event.
     *
     * @param  \App\Voucher  $voucher
     * @return void
     */
    public function forceDeleted(Voucher $voucher)
    {
        //
    }
}
