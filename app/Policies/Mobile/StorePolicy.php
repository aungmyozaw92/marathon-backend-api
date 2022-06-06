<?php

namespace App\Policies\Mobile;

use App\Models\Store;
use App\Models\Merchant;
use Illuminate\Auth\Access\HandlesAuthorization;

class StorePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view the store.
     *
     * @param  \App\Models\Merchant  $merchant
     * @param  \App\Store  $store
     * @return mixed
     */
    public function view(Merchant $merchant, Store $store)
    {
        return $merchant->id === $store->merchant_id;
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
     * Determine whether the user can update the store.
     *
     * @param  \App\Models\Merchant  $merchant
     * @param  \App\Store  $store
     * @return mixed
     */
    public function update(Merchant $merchant, Store $store)
    {
        return $merchant->id === $store->merchant_id;
    }

    /**
     * Determine whether the user can delete the store.
     *
     * @param  \App\Models\Merchant  $merchant
     * @param  \App\Store  $store
     * @return mixed
     */
    public function delete(Merchant $merchant, Store $store)
    {
        return $merchant->id === $store->merchant_id;
    }

    /**
     * Determine whether the user can restore the store.
     *
     * @param  \App\Models\Merchant  $merchant
     * @param  \App\Store  $store
     * @return mixed
     */
    public function restore(Merchant $merchant, Store $store)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the store.
     *
     * @param  \App\Models\Merchant  $merchant
     * @param  \App\Store  $store
     * @return mixed
     */
    public function forceDelete(Merchant $merchant, Store $store)
    {
        //
    }
}
