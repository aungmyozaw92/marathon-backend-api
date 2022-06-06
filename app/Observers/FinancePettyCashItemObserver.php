<?php

namespace App\Observers;

use App\Models\FinancePettyCashItem;

class FinancePettyCashItemObserver
{
    /**
     * Handle the finance expense "created" event.
     *
     * @param  \App\Models\FinancePettyCashItem  $finance_petty_cash_item
     * @return void
     */
    public function created(FinancePettyCashItem $finance_petty_cash_item)
    {
        $finance_petty_cash_item->invoice_no = $finance_petty_cash_item->id;
        $finance_petty_cash_item->save();
    }

    /**
     * Handle the finance expense "updated" event.
     *
     * @param  \App\Models\FinancePettyCashItem  $finance_petty_cash_item
     * @return void
     */
    public function updated(FinancePettyCashItem $finance_petty_cash_item)
    {
        //
    }

    /**
     * Handle the finance expense "deleted" event.
     *
     * @param  \App\Models\FinancePettyCashItem  $finance_petty_cash_item
     * @return void
     */
    public function deleted(FinancePettyCashItem $finance_petty_cash_item)
    {
        //
    }

    /**
     * Handle the finance expense "restored" event.
     *
     * @param  \App\Models\FinancePettyCashItem  $finance_petty_cash_item
     * @return void
     */
    public function restored(FinancePettyCashItem $finance_petty_cash_item)
    {
        //
    }

    /**
     * Handle the finance expense "force deleted" event.
     *
     * @param  \App\Models\FinancePettyCashItem  $finance_petty_cash_item
     * @return void
     */
    public function forceDeleted(FinancePettyCashItem $finance_petty_cash_item)
    {
        //
    }
}
