<?php

namespace App\Observers;

use App\Models\Journal;
use App\Models\JournalHistory;

class JournalObserver
{
    /**
     * Handle the Journal "created" event.
     *
     * @param  \App\Models\Journal  $journal
     * @return void
     */
    public function created(Journal $journal)
    {
        $journal->journal_no = $journal->id;
        $journal->save();

    }

    /**
     * Handle the Journal "updated" event.
     *
     * @param  \App\Journal  $journal
     * @return void
     */
    public function updated(Journal $journal)
    {

        $changes = $journal->getChanges();
        $changes = array_only($changes, ['status']);
        
        foreach ($changes as $key => $value) {
            $previous = $journal->getOriginal($key);
            $next = $value;

            if($next === 1){
                JournalHistory::create([
                    'journal_id' => $journal->id,
                    'resourceable_type' => $journal->resourceable_type,
                    'resourceable_id' => $journal->resourceable_id,
                    'log_type' => 'updated - change status from ' . $previous .' to '. $next,
    
                    'from_path' => request()->path(),
                    'updated_by' => isset(auth()->user()->id) ? auth()->user()->id : 1,
                    'updated_by_name' => auth()->user() ? auth()->user()->name : 1
                ]);
            }
        }
        
    }

    /**
     * Handle the Journal "deleted" event.
     *
     * @param  \App\Journal  $journal
     * @return void
     */
    public function deleted(Journal $journal)
    {
        //
    }

    /**
     * Handle the Journal "restored" event.
     *
     * @param  \App\Journal  $journal
     * @return void
     */
    public function restored(Journal $journal)
    {
        //
    }

    /**
     * Handle the Journal "force deleted" event.
     *
     * @param  \App\Journal  $journal
     * @return void
     */
    public function forceDeleted(Journal $journal)
    {
        //
    }
}
