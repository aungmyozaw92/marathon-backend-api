<?php

namespace App\Observers;

use App\Models\FinanceAsset;

class FinanceAssetObserver
{
    /**
     * Handle the FinanceAsset "created" event.
     *
     * @param  \App\FinanceAsset  $finance_asset
     * @return void
     */
    public function created(FinanceAsset $finance_asset)
    {
        $finance_asset->asset_no = $finance_asset->id;
        $finance_asset->save();
    }

    /**
     * Handle the FinanceAsset "updated" event.
     *
     * @param  \App\FinanceAsset  $finance_asset
     * @return void
     */
    public function updated(FinanceAsset $finance_asset)
    {
        //
    }

    /**
     * Handle the FinanceAsset "deleted" event.
     *
     * @param  \App\FinanceAsset  $finance_asset
     * @return void
     */
    public function deleted(FinanceAsset $finance_asset)
    {
        //
    }

    /**
     * Handle the FinanceAsset "restored" event.
     *
     * @param  \App\FinanceAsset  $finance_asset
     * @return void
     */
    public function restored(FinanceAsset $finance_asset)
    {
        //
    }

    /**
     * Handle the FinanceAsset "force deleted" event.
     *
     * @param  \App\FinanceAsset  $finance_asset
     * @return void
     */
    public function forceDeleted(FinanceAsset $finance_asset)
    {
        //
    }
}
