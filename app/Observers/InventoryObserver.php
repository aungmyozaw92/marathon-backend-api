<?php

namespace App\Observers;

use App\Models\Inventory;
use App\Models\InventoryLog;
use Illuminate\Http\Request;

class InventoryObserver
{
    /**
     * Handle the inventory "created" event.
     *
     * @param  \App\Inventory  $inventory
     * @return void
     */
    public function created(Inventory $inventory)
    {
        //
    }

    /**
     * Handle the inventory "updated" event.
     *
     * @param  \App\Inventory  $inventory
     * @return void
     */
    public function updated(Inventory $inventory)
    {
        
        $changes = $inventory->getChanges();
        $changes = array_only($changes, ['qty']);
        $path =request()->path();
        if(str_contains($path, 'thirdparty') || str_contains($path, 'mobile') 
          || str_contains($path, 'merchant_dashboard')){
            $type = "Merchant";
        }else{
            $type = 'Staff';
        }
        
        foreach ($changes as $key => $value) {
            $previous = $inventory->getOriginal($key);
            $next = $value;
            InventoryLog::create([
                'qty' => $next - $previous,
                'inventory_id' => $inventory->id,
                'created_by_id' => auth()->user()->id,
                'created_by_type' => $type,
            ]);
        }
    }

    /**
     * Handle the inventory "deleted" event.
     *
     * @param  \App\Inventory  $inventory
     * @return void
     */
    public function deleted(Inventory $inventory)
    {
        //
    }

    /**
     * Handle the inventory "restored" event.
     *
     * @param  \App\Inventory  $inventory
     * @return void
     */
    public function restored(Inventory $inventory)
    {
        //
    }

    /**
     * Handle the inventory "force deleted" event.
     *
     * @param  \App\Inventory  $inventory
     * @return void
     */
    public function forceDeleted(Inventory $inventory)
    {
        //
    }
}
