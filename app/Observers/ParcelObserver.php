<?php

namespace App\Observers;

use App\Models\Parcel;
use App\Models\VoucherHistory;
use App\Models\CouponAssociate;

class ParcelObserver
{
    /**
     * Handle the Parcel "created" event.
     *
     * @param  \App\Parcel  $parcel
     * @return void
     */

    public function created(Parcel $parcel)
    {
        $voucherId = (getVoucherId()) ? getVoucherId() : 1;
        $logStatusId = getStatusId('new_parcel');
        $voucher_history = new VoucherHistory([
            'voucher_id' => $voucherId,
            'log_status_id' => $logStatusId,
        ]);
        auth()->user()->voucher_histories()->save($voucher_history);
        clearVoucherId();
    }

    /**
     * Handle the Parcel "updating" event.
     *
     * @param  \App\Parcel  $parcel
     * @return void
     */
    public function updating(Parcel $parcel)
    {
        //
    }

    /**
     * Handle the Voucher "updated" event.
     *
     * @param  \App\Parcel  $parcel
     * @return void
     */
    public function updated(Parcel $parcel)
    {
        $changes = $parcel->getChanges();
		$changes = array_only($changes, ['weight', 'global_scale_id', 'seller_discount']);
        if (empty($changes)) return;
        $transformedInputs = [];
        $voucherId = (getVoucherId()) ? getVoucherId() : 1;
        $fs_update_payload = [];
        foreach ($changes as $key => $value) {
            $previous = $parcel->getOriginal($key);
            $next = $value;
            if ((string)$key != 'seller_discount') {
                $transformedInputs[] = transformedVouchersAttribute($key, $previous, $next);
            }             
        }
        foreach ($transformedInputs as $key => $value) {
            $logStatusId = getStatusId($value['status']);
            if ($value['previous'] != null && $value['next'] != null) {
                $voucher_history = new VoucherHistory([
                    'voucher_id' => $voucherId,
                    'log_status_id' => $logStatusId,
                    'previous' => $value['previous'],
                    'next' => $value['next'],
                ]);
                auth()->user()->voucher_histories()->save($voucher_history);
            }
        }
    }

    /**
     * Handle the Parcel "deleted" event.
     *
     * @param  \App\Parcel  $parcel
     * @return void
     */
    public function deleted(Parcel $parcel)
    {
        $voucherId = (getVoucherId()) ? getVoucherId() : 1;
        $logStatusId = getStatusId('delete_parcel');

        VoucherHistory::create([
            'voucher_id' => $voucherId,
            'log_status_id' => $logStatusId,
            'created_by' => isset(auth()->user()->id) ? auth()->user()->id : 1
        ]);
    }

    /**
     * Handle the Parcel "restored" event.
     *
     * @param  \App\Parcel  $parcel
     * @return void
     */
    public function restored(Parcel $parcel)
    {
        //
    }

    /**
     * Handle the Parcel "force deleted" event.
     *
     * @param  \App\Parcel  $parcel
     * @return void
     */
    public function forceDeleted(Parcel $parcel)
    {
        $parcel->parcel_items()->forceDelete();
    }
}
