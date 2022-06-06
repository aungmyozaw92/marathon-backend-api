<?php

namespace App\Policies\Mobile;

use App\Models\Inventory;
use App\Models\Merchant;
use Illuminate\Auth\Access\HandlesAuthorization;

class InventoryPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view the inventory.
     *
     * @param  \App\Models\Merchant  $merchant
     * @param  \App\Inventory  $inventory
     * @return mixed
     */
    public function view(Merchant $merchant, Inventory $inventory)
    {
        return $merchant->id === $inventory->product->merchant_id;
    }

    /**
     * Determine whether the user can create stores.
     *
     * @param  \App\Models\Merchant  $merchant
     * @return mixed
     */
    public function create(Merchant $merchant)
    {
        //
    }

    /**
     * Determine whether the user can update the inventory.
     *
     * @param  \App\Models\Merchant  $merchant
     * @param  \App\Inventory  $inventory
     * @return mixed
     */
    public function update(Merchant $merchant, Inventory $inventory)
    {
        return $merchant->id === $inventory->product->merchant_id;
    }

    /**
     * Determine whether the user can delete the inventory.
     *
     * @param  \App\Models\Merchant  $merchant
     * @param  \App\ProductStore  $inventory
     * @return mixed
     */
    public function delete(Merchant $merchant, Inventory $inventory)
    {
        return $merchant->id === $inventory->product->merchant_id;
    }

    /**
     * Determine whether the user can restore the inventory.
     *
     * @param  \App\Models\Merchant  $merchant
     * @param  \App\Inventory  $inventory
     * @return mixed
     */
    public function restore(Merchant $merchant, Inventory $inventory)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the inventory.
     *
     * @param  \App\Models\Merchant  $merchant
     * @param  \App\Inventory  $inventory
     * @return mixed
     */
    public function forceDelete(Merchant $merchant, Inventory $inventory)
    {
        //
    }
}
