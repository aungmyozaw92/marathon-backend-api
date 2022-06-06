<?php

namespace App\Observers;

use App\Models\BusSheet;

class BusSheetObserver
{
    /**
     * Handle the deli sheet "created" event.
     *
     * @param  \App\BusSheet  $busSheet
     * @return void
     */
    public function created(BusSheet $busSheet)
    {
        $busSheet->bus_sheet_invoice = $busSheet->id;
        $busSheet->save();
    }

    /**
     * Handle the deli sheet "updated" event.
     *
     * @param  \App\BusSheet  $busSheet
     * @return void
     */
    public function updated(BusSheet $busSheet)
    {
        //
    }

    /**
     * Handle the deli sheet "deleted" event.
     *
     * @param  \App\BusSheet  $busSheet
     * @return void
     */
    public function deleted(BusSheet $busSheet)
    {
        //
    }

    /**
     * Handle the deli sheet "restored" event.
     *
     * @param  \App\BusSheet  $busSheet
     * @return void
     */
    public function restored(BusSheet $busSheet)
    {
        //
    }

    /**
     * Handle the deli sheet "force deleted" event.
     *
     * @param  \App\BusSheet  $busSheet
     * @return void
     */
    public function forceDeleted(BusSheet $busSheet)
    {
        //
    }
}
