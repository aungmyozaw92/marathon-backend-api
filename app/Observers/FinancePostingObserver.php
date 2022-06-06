<?php

namespace App\Observers;

use App\Models\FinancePosting;

class FinancePostingObserver
{
    /**
     * Handle the finance posting "created" event.
     *
     * @param  \App\Models\FinancePosting  $financePosting
     * @return void
     */
    public function created(FinancePosting $financePosting)
    {
        $financePosting->posting_invoice = $financePosting->id;
        $financePosting->save();
    }

    /**
     * Handle the finance posting "updated" event.
     *
     * @param  \App\Models\FinancePosting  $financePosting
     * @return void
     */
    public function updated(FinancePosting $financePosting)
    {
        //
    }

    /**
     * Handle the finance posting "deleted" event.
     *
     * @param  \App\Models\FinancePosting  $financePosting
     * @return void
     */
    public function deleted(FinancePosting $financePosting)
    {
        //
    }

    /**
     * Handle the finance posting "restored" event.
     *
     * @param  \App\Models\FinancePosting  $financePosting
     * @return void
     */
    public function restored(FinancePosting $financePosting)
    {
        //
    }

    /**
     * Handle the finance posting "force deleted" event.
     *
     * @param  \App\Models\FinancePosting  $financePosting
     * @return void
     */
    public function forceDeleted(FinancePosting $financePosting)
    {
        //
    }
}
