<?php

namespace App\Policies\SuperMerchant;

use App\Models\Merchant;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Console\OptimizeCommand;

class MerchantPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view the pickup.
     *
     * @param  \App\Models\Merchant  $merchant
     * @return mixed
     */
    public function view(Merchant $merchant, Merchant $sub_merchant)
    {
        if (optional($sub_merchant)->id) {
            return $merchant->id === $sub_merchant->super_merchant_id;
        } else {
            throw new AuthorizationException("This action is unauthorized.");
        }
    }

    /**
     * Determine whether the user can create pickups.
     *
     * @param  \App\Models\Merchant  $merchant
     * @return mixed
     */
    public function create(Merchant $merchant, Merchant $sub_merchant)
    {
        if (optional($sub_merchant)->id) {
            return $merchant->id === $sub_merchant->super_merchant_id;
        } else {
            throw new AuthorizationException("This action is unauthorized.");
        }
    }

    /**
     * Determine whether the user can update the pickup.
     *
     * @param  \App\Models\Merchant  $merchant
     * @param  \App\Pickup  $pickup
     * @return mixed
     */
    public function update(Merchant $merchant, Merchant $sub_merchant)
    {
        if (optional($sub_merchant)->id) {
            return $merchant->id === $sub_merchant->super_merchant_id;
        } else {
            throw new AuthorizationException("This action is unauthorized.");
        }
    }

    /**
     * Determine whether the user can delete the pickup.
     *
     * @param  \App\Models\Merchant  $merchant
     * @param  \App\Pickup  $pickup
     * @return mixed
     */
    public function delete(Merchant $merchant, Merchant $sub_merchant)
    {
        if (optional($sub_merchant)->id) {
            return $merchant->id === $sub_merchant->super_merchant_id;
        } else {
            throw new AuthorizationException("This action is unauthorized.");
        }
    }

    /**
     * Determine whether the user can permanently delete the pickup.
     *
     * @param  \App\Models\Merchant  $merchant
     * @param  \App\Pickup  $pickup
     * @return mixed
     */
    public function forceDelete(Merchant $merchant, Merchant $sub_merchant)
    {
        //
    }
}
