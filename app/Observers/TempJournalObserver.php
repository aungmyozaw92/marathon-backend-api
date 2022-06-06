<?php

namespace App\Observers;

use App\Models\TempJournal;

class TempJournalObserver
{
    /**
     * Handle the TempJournal "created" event.
     *
     * @param  \App\Models\TempJournal  $temp_journal
     * @return void
     */
    public function created(TempJournal $temp_journal)
    {
        $temp_journal->journal_no = $temp_journal->id;
        $temp_journal->save();
    }

    /**
     * Handle the Journal "updated" event.
     *
     * @param  \App\TempJournal  $temp_journal
     * @return void
     */
    public function updated(TempJournal $temp_journal)
    {
        //
    }

    /**
     * Handle the Journal "deleted" event.
     *
     * @param  \App\TempJournal  $temp_journal
     * @return void
     */
    public function deleted(TempJournal $temp_journal)
    {
        //
    }

    /**
     * Handle the TempJournal "restored" event.
     *
     * @param  \App\TempJournal  $temp_journal
     * @return void
     */
    public function restored(TempJournal $temp_journal)
    {
        //
    }

    /**
     * Handle the Journal "force deleted" event.
     *
     * @param  \App\Journal  $temp_journal
     * @return void
     */
    public function forceDeleted(TempJournal $temp_journal)
    {
        //
    }
}
