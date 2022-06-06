<?php

namespace App\Observers;

use App\Models\Pickup;
use App\Models\Customer;
use App\Models\PickupHistory;
use App\Models\VoucherHistory;
use Illuminate\Support\Facades\Auth;

class CustomerObserver
{
    private $condition = false;

    /**
     * Handle the customer "created" event.
     *
     * @param  \App\Customer  $customer
     * @return void
     */
    public function created(Customer $customer)
    {
        $pickupId = getpickupId();
        // $this->condition = str_contains(request()->route()->uri(), 'pickups');

        // if ($this->condition) {
        //     // PickupHistory::create([
        //     //     'pickup_id' =>$pickupId,
        //     //     'log_status_id' => 7
        //     // ]);
        //     clearPickupId();
        // }
        clearPickupId();
        clearVoucherId();
    }

    /**
     * Handle the customer "updated" event.
     *
     * @param  \App\Customer  $customer
     * @return void
     */
    public function updated(Customer $customer)
    {
        /**
         * Customer Updated Event From Voucher
         */
        $this->condition = str_contains(request()->route()->uri(), 'pickups');

        if ($this->condition) {
            $pickupId = getPickupId();
            $transformedInputs = [];
            $changes = $customer->getChanges();
            $changes = array_only($changes, ['name', 'address', 'phone', 'other_phone']);

            foreach ($changes as $key => $value) {
                $previous = $customer->getOriginal($key);
                $next = $value;
                $transformedInputs[] = transformedPickupsAttribute($key, $previous, $next);
            }

            foreach ($transformedInputs as $key => $value) {

                $logStatusId = getStatusId($value['status']);
                if ($value['status'] === 'change_receiver_other_phone' || ($value['previous'] != null && $value['next'] != null)) {
                    $pickup_history = new PickupHistory([
                        'pickup_id' => $pickupId,
                        'log_status_id' => $logStatusId,
                        'previous' => $value['previous'],
                        'next' => $value['next'],
                    ]);
                    auth()->user()->pickup_histories()->save($pickup_history);
                }
            }

            clearPickupId();
        }

        /**
         * Customer Updated Event From Voucher
         */
        $this->condition = str_contains(request()->route()->uri(), 'vouchers');

        if ($this->condition) {
            $voucherId = getVoucherId();
            $transformedInputs = [];
            $changes = $customer->getChanges();
            $changes = array_only($changes, ['name', 'address', 'phone', 'other_phone']);

            // 
            foreach ($changes as $key => $value) {
                $previous = $customer->getOriginal($key);
                $next = $value;
                $transformedInputs[] = transformedVouchersAttribute($key, $previous, $next);
            }

            foreach ($transformedInputs as $key => $value) {
				$logStatusId = getStatusId($value['status']);
                if (Auth::check() && ($value['status'] === 'change_receiver_other_phone' || ($value['previous'] != null && $value['next'] != null))) {
                    $voucher_history = new VoucherHistory([
                        'voucher_id' => $voucherId,
                        'log_status_id' => $logStatusId,
                        'previous' => $value['previous'],
                        'next' => $value['next'],
                    ]);
                    if(Auth::check()) {
						auth()->user()->voucher_histories()->save($voucher_history);
					}
                }
            }

            clearVoucherId();
        }
    }

    /**
     * Handle the customer "deleted" event.
     *
     * @param  \App\Customer  $customer
     * @return void
     */
    public function deleted(Customer $customer)
    {
        //
    }

    /**
     * Handle the customer "restored" event.
     *
     * @param  \App\Customer  $customer
     * @return void
     */
    public function restored(Customer $customer)
    {
        //
    }

    /**
     * Handle the customer "force deleted" event.
     *
     * @param  \App\Customer  $customer
     * @return void
     */
    public function forceDeleted(Customer $customer)
    {
        //
    }
}
