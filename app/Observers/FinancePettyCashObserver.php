<?php

namespace App\Observers;

use App\Models\FinancePettyCash;

class FinancePettyCashObserver
{
    /**
     * Handle the finance expense "created" event.
     *
     * @param  \App\Models\FinancePettyCash  $finance_petty_cash
     * @return void
     */
    public function created(FinancePettyCash $finance_petty_cash)
    {
        $finance_petty_cash->invoice_no = $finance_petty_cash->id;
        $finance_petty_cash->save();
    }

    /**
     * Handle the finance expense "updated" event.
     *
     * @param  \App\Models\FinancePettyCash  $finance_petty_cash
     * @return void
     */
    public function updated(FinancePettyCash $finance_petty_cash)
    {
        //
    }

    /**
     * Handle the finance expense "deleted" event.
     *
     * @param  \App\Models\FinancePettyCash  $finance_petty_cash
     * @return void
     */
    public function deleted(FinancePettyCash $finance_petty_cash)
    {
        //
    }

    /**
     * Handle the finance expense "restored" event.
     *
     * @param  \App\Models\FinancePettyCash  $finance_petty_cash
     * @return void
     */
    public function restored(FinancePettyCash $finance_petty_cash)
    {
        //
    }

    /**
     * Handle the finance expense "force deleted" event.
     *
     * @param  \App\Models\FinancePettyCash  $finance_petty_cash
     * @return void
     */
    public function forceDeleted(FinancePettyCash $finance_petty_cash)
    {
        //
    }
}
