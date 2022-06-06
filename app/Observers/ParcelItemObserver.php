<?php

namespace App\Observers;

use App\Models\ParcelItem;
use App\Models\VoucherHistory;

class ParcelItemObserver
{
    /**
     * Handle the Parcel "created" event.
     *
     * @param  \App\Parcel  $parcel
     * @return void
     */

    public function created(ParcelItem $parcel_item)
    {
        $voucherId = (getVoucherId()) ? getVoucherId() : 1;
        $logStatusId = getStatusId('new_parcel_item');
        $voucher_history = new VoucherHistory([
            'voucher_id' => $voucherId,
            'log_status_id' => $logStatusId,
            'previous' => $parcel_item->item_name
        ]);
        auth()->user()->voucher_histories()->save($voucher_history);
    }

    /**
     * Handle the ParcelItem "updating" event.
     *
     * @param  \App\ParcelItem  $parcel_item
     * @return void
     */
    public function updating(ParcelItem $parcel_item)
    {
        //
    }

    /**
     * Handle the Voucher "updated" event.
     *
     * @param  \App\ParcelItem  $parcel_item
     * @return void
     */
    public function updated(ParcelItem $parcel_item)
    {
        $changes = $parcel_item->getChanges();

        $changes = array_only($changes, ['item_name', 'item_qty', 'item_price', 'product_id']);
        if (empty($changes)) return;
        $voucherId = (getVoucherId()) ? getVoucherId() : 1;
        $transformedInputs = [];

        foreach ($changes as $key => $value) {
            if ($key != 'product_id') {
                $previous = $parcel_item->getOriginal($key);
                $next = $value;
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
        // clearVoucherId();
    }

    /**
     * Handle the ParcelItem "deleted" event.
     *
     * @param  \App\ParcelItem  $parcel_item
     * @return void
     */
    public function deleted(ParcelItem $parcel_item)
    {
        $voucherId = (getVoucherId()) ? getVoucherId() : 1;
        $logStatusId = getStatusId('delete_item');
        $voucher_history = new VoucherHistory([
            'voucher_id' => $voucherId,
            'log_status_id' => $logStatusId,
            'previous' => $parcel_item->item_name
        ]);
        auth()->user()->voucher_histories()->save($voucher_history);
    }

    /**
     * Handle the ParcelItem "restored" event.
     *
     * @param  \App\ParcelItem  $parcel_item
     * @return void
     */
    public function restored(ParcelItem $parcel_item)
    {
        //
    }
}
