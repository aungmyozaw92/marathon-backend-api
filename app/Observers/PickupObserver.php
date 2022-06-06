<?php

namespace App\Observers;

use App\Models\Pickup;
use App\Models\LogStatus;
use App\Models\PickupHistory;
use App\Models\Staff;
use \Auth;

class PickupObserver
{
    /**
     * Handle the pickup "created" event.
     *
     * @param  \App\Pickup  $pickup
     * @return void
     */
    public function created(Pickup $pickup)
    {
        $pickup->pickup_invoice = $pickup->id;
        $pickup->save();
        $logStatusId = getStatusId('new_pickup');
        $pickup_history = new PickupHistory([
            'pickup_id' => $pickup->id,
            'log_status_id' => $logStatusId,
        ]);
        auth()->user()->pickup_histories()->save($pickup_history);
        if ($pickup->platform == 'Merchant Dashboard' && $pickup->vouchers()->exists()) {
            $tracking_status_id = getTrackingStatusId('info_received');
            foreach ($pickup->vouchers as $voucher) {
                $voucher->tracking_status()->attach($tracking_status_id, array('city_id' => auth()->user()->city_id, 'created_by' => auth()->user()->id));
            }
        }
    }

    /**
     * Handle the pickup "updating" event.
     *
     * @param  \App\Pickup  $pickup
     * @return void
     */
    public function updating(Pickup $pickup)
    {
        //
    }

    /**
     * Handle the pickup "updated" event.
     *
     * @param  \App\Pickup  $pickup
     * @return void
     */
    public function updated(Pickup $pickup)
    {
        $changes = $pickup->getChanges();
        $expected_columns = [
            'sender_type',
            'sender_id',
            'opened_by',
            'note',
            'pickup_fee',
            'is_closed',
            'requested_date',
            'updated_at',
            'qty',
            'is_pickuped',
            'pickup_date',
            'sender_associate_id',
            'is_paid'
        ];
        $changes = array_only($changes, $expected_columns);
        if (empty($changes)) return;
        $transformedInputs = [];
        foreach ($changes as $key => $value) {
            $previous = $pickup->getOriginal($key);
            $next = $value;
            if (
                (string)$key != 'qty' && (string)$key != 'is_pickuped'
                && (string)$key != 'requested_date' && (string)$key != 'pickup_date'
                && (string)$key != 'updated_at' && (string)$key != 'sender_associate_id'
            ) {
                $transformedInputs[] = transformedPickupsAttribute($key, $previous, $next);
            }
        }

        foreach ($transformedInputs as $key => $value) {
            $logStatusId = getStatusId($value['status']);
            if ((
                    ($value['status'] == 'change_note' || $value['status'] == 'assign_pickup')
                    && ($value['previous'] != null || $value['next'] != null))
                ||
                ($value['previous'] != null && $value['next'] != null)
                || ($value['status'] == 'change_pickup_fee' && $value['previous'] != 0 && $value['next'] != 0) 
                || $value['status'] == 'close' 
                || $value['status'] == 'receive_payment'
            ) {
                $pickup_history = new PickupHistory([
                    'pickup_id' => $pickup->id,
                    'log_status_id' => $logStatusId,
                    'previous' => $value['previous'],
                    'next' => $value['next'],
                ]);
                auth()->user() ? auth()->user()->pickup_histories()->save($pickup_history) : $pickup->created_by_merchant->save($pickup_history);
            }
        }
        // tracking 
        if (($pickup->wasChanged('assigned_by_id') || $pickup->wasChanged('is_pickuped') || $pickup->wasChanged('is_closed')) && $pickup->vouchers()->exists()) {
            $tracking_status_id = pickupTracker($pickup);
            if (!empty($tracking_status_id)) {
                foreach ($pickup->vouchers as $voucher) {
                    $voucher->tracking_status()->attach($tracking_status_id, array('city_id' => auth()->user()->city_id, 'created_by' => auth()->user()->id));
                }
            }
        }
    }

    /**
     * Handle the pickup "deleted" event.
     *
     * @param  \App\Pickup  $pickup
     * @return void
     */
    public function deleted(Pickup $pickup)
    {
        $logStatusId = getStatusId('delete_pickup');

        PickupHistory::create([
            'pickup_id' => $pickup->id,
            'log_status_id' => $logStatusId,
            'previous' => $pickup->pickup_invoice,
            'created_by' => auth()->user()->id
        ]);
    }

    /**
     * Handle the pickup "restored" event.
     *
     * @param  \App\Pickup  $pickup
     * @return void
     */
    public function restored(Pickup $pickup)
    {
        //
    }

    /**
     * Handle the pickup "force deleted" event.
     *
     * @param  \App\Pickup  $pickup
     * @return void
     */
    public function forceDeleted(Pickup $pickup)
    {
        //
    }
}
