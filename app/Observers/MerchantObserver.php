<?php

namespace App\Observers;

use App\Models\Merchant;
use App\Models\MerchantDiscount;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Models\PaymentType;
class MerchantObserver
{
    /**
     * Handle the merchant "created" event.
     *
     * @param  \App\Merchant  $merchant
     * @return void
     */

    public function created(Merchant $merchant)
    {
        $merchant->merchant_no = $merchant->id;
        $merchant->save();
    }

    /**
     * Handle the merchant "updated" event.
     *
     * @param  \App\Merchant  $merchant
     * @return void
     */
    public function updated(Merchant $merchant)
    {
        //
    }

    /**
     * Handle the merchant "deleted" event.
     *
     * @param  \App\Merchant  $merchant
     * @return void
     */
    public function deleted(Merchant $merchant)
    {
        //
    }

    /**
     * Handle the merchant "restored" event.
     *
     * @param  \App\Merchant  $merchant
     * @return void
     */
    public function restored(Merchant $merchant)
    {
        //
    }

    /**
     * Handle the merchant "force deleted" event.
     *
     * @param  \App\Merchant  $merchant
     * @return void
     */
    public function forceDeleted(Merchant $merchant)
    {
        //
    }
}
