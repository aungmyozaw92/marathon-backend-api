<?php

namespace App\Observers;

use App\Models\FinanceExpense;

class FinanceExpenseObserver
{
    /**
     * Handle the finance expense "created" event.
     *
     * @param  \App\Models\FinanceExpense  $financeExpense
     * @return void
     */
    public function created(FinanceExpense $financeExpense)
    {
        $financeExpense->expense_invoice = $financeExpense->id;
        $financeExpense->save();
    }

    /**
     * Handle the finance expense "updated" event.
     *
     * @param  \App\Models\FinanceExpense  $financeExpense
     * @return void
     */
    public function updated(FinanceExpense $financeExpense)
    {
        //
    }

    /**
     * Handle the finance expense "deleted" event.
     *
     * @param  \App\Models\FinanceExpense  $financeExpense
     * @return void
     */
    public function deleted(FinanceExpense $financeExpense)
    {
        //
    }

    /**
     * Handle the finance expense "restored" event.
     *
     * @param  \App\Models\FinanceExpense  $financeExpense
     * @return void
     */
    public function restored(FinanceExpense $financeExpense)
    {
        //
    }

    /**
     * Handle the finance expense "force deleted" event.
     *
     * @param  \App\Models\FinanceExpense  $financeExpense
     * @return void
     */
    public function forceDeleted(FinanceExpense $financeExpense)
    {
        //
    }
}
