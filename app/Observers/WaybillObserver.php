<?php

namespace App\Observers;

use App\Models\Waybill;
use App\Models\WaybillHistory;

class WaybillObserver
{
    /**
     * Handle the waybill "created" event.
     *
     * @param  \App\Waybill  $waybill
     * @return void
     */
    public function created(Waybill $waybill)
    {
        $waybill->waybill_invoice = $waybill->id;
        $waybill->save();
        $logStatusId = getStatusId('new_waybill');
        $waybill_history = new WaybillHistory([
            'waybill_id' => $waybill->id,
            'log_status_id' => $logStatusId,
            'created_by' => isset(auth()->user()->id) ? auth()->user()->id : 1
        ]);
        auth()->user()->waybill_histories()->save($waybill_history);
    }

    /**
     * Handle the waybill "updated" event.
     *
     * @param  \App\Waybill  $waybill
     * @return void
     */
    public function updated(Waybill $waybill)
    {
        $changes = $waybill->getChanges();
        $expected_columns = [
            'delivery_id',
            'from_bus_station_id',
            'to_bus_station_id',
            'gate_id',
            'from_city_id',
            'to_city_id',
            'is_received',
            'is_closed',
            'is_paid',
            'note',
            'actual_bus_fee',
            'is_confirm',
            'is_delivered'
        ];
        $changes = array_only($changes, $expected_columns);
        $transformedInputs = [];
        foreach ($changes as $key => $value) {
            $previous = $waybill->getOriginal($key);
            $next = $value;
            $transformedInputs[] = transformedWaybillsAttribute($key, $previous, $next);
        }

        foreach ($transformedInputs as $key => $value) {
            $logStatusId = getStatusId($value['status']);
            if (
                (
                    ($value['status'] == 'change_note' || $value['status'] == 'change_actual_bus_fee')
                    &&
                    ($value['next'] != null))
                ||
                ($value['previous'] != null && $value['next'] != null)
                ||
                $value['status'] == 'close' || $value['status'] == 'receive_payment' || $value['status'] == 'receive_waybill'
                || $value['status'] == 'confirmed_waybill' || $value['status'] == 'delivered_waybill'
            ) {
                $waybill_history = new WaybillHistory([
                    'waybill_id' => $waybill->id,
                    'log_status_id' => $logStatusId,
                    'previous' => $value['previous'],
                    'next' => $value['next'],
                    'created_by' => isset(auth()->user()->id) ? auth()->user()->id : 1
                ]);
                auth()->user()->waybill_histories()->save($waybill_history);
            }
        }

        if ($waybill->isDirty('is_received') || $waybill->isDirty('is_confirm') || $waybill->isDirty('is_delivered')) {
            foreach ($waybill->vouchers as $voucher) {
                $tracking_status_id = waybillTracker($waybill);
                $voucher->tracking_status()->attach($tracking_status_id, array('city_id' => auth()->user()->city_id, 'created_by' => auth()->user()->id));
            }
        }
    }

    /**
     * Handle the waybill "deleted" event.
     *
     * @param  \App\Waybill  $waybill
     * @return void
     */
    public function deleted(Waybill $waybill)
    {
        $logStatusId = getStatusId('delete_waybill');

        WaybillHistory::create([
            'waybill_id' => $waybill->id,
            'log_status_id' => $logStatusId,
            'previous' => $waybill->waybill_invoice,
            'created_by' => auth()->user()->id
        ]);
    }

    /**
     * Handle the waybill "restored" event.
     *
     * @param  \App\Waybill  $waybill
     * @return void
     */
    public function restored(Waybill $waybill)
    {
        //
    }

    /**
     * Handle the waybill "force deleted" event.
     *
     * @param  \App\Waybill  $waybill
     * @return void
     */
    public function forceDeleted(Waybill $waybill)
    {
        //
    }
}
