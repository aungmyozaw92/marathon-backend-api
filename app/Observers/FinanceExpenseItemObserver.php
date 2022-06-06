<?php

namespace App\Observers;

use App\Models\FinanceExpenseItem;

class FinanceExpenseItemObserver
{
    /**
     * Handle the finance expense "created" event.
     *
     * @param  \App\Models\FinanceExpenseItem  $finance_expense_item
     * @return void
     */
    public function created(FinanceExpenseItem $finance_expense_item)
    {
        $finance_expense_item->expense_item_invoice = $finance_expense_item->id;
        $finance_expense_item->save();
    }

    /**
     * Handle the finance expense "updated" event.
     *
     * @param  \App\Models\FinanceExpenseItem  $finance_expense_item
     * @return void
     */
    public function updated(FinanceExpenseItem $finance_expense_item)
    {
        //
    }

    /**
     * Handle the finance expense "deleted" event.
     *
     * @param  \App\Models\FinanceExpenseItem  $finance_expense_item
     * @return void
     */
    public function deleted(FinanceExpenseItem $finance_expense_item)
    {
        //
    }

    /**
     * Handle the finance expense "restored" event.
     *
     * @param  \App\Models\FinanceExpenseItem  $finance_expense_item
     * @return void
     */
    public function restored(FinanceExpenseItem $finance_expense_item)
    {
        //
    }

    /**
     * Handle the finance expense "force deleted" event.
     *
     * @param  \App\Models\FinanceExpenseItem  $finance_expense_item
     * @return void
     */
    public function forceDeleted(FinanceExpenseItem $finance_expense_item)
    {
        //
    }
}
