<?php

namespace App\Observers;

use App\Models\FinanceAdvance;

class FinanceAdvanceObserver
{
    /**
     * Handle the finance Advance "created" event.
     *
     * @param  \App\Models\FinanceAdvance  $financeAdvance
     * @return void
     */
    public function created(FinanceAdvance $financeAdvance)
    {
        $financeAdvance->advance_invoice = $financeAdvance->id;
        $financeAdvance->save();
    }

    /**
     * Handle the finance Advance "updated" event.
     *
     * @param  \App\Models\FinanceAdvance  $financeAdvance
     * @return void
     */
    public function updated(FinanceAdvance $financeAdvance)
    {
        //
    }

    /**
     * Handle the finance Advance "deleted" event.
     *
     * @param  \App\Models\FinanceAdvance  $financeAdvance
     * @return void
     */
    public function deleted(FinanceAdvance $financeAdvance)
    {
        //
    }

    /**
     * Handle the finance Advance "restored" event.
     *
     * @param  \App\Models\FinanceAdvance  $financeAdvance
     * @return void
     */
    public function restored(FinanceAdvance $financeAdvance)
    {
        //
    }

    /**
     * Handle the finance Advance "force deleted" event.
     *
     * @param  \App\Models\FinanceAdvance  $financeAdvance
     * @return void
     */
    public function forceDeleted(FinanceAdvance $financeAdvance)
    {
        //
    }
}
