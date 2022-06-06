<?php

namespace App\Policies\MerchantDashboard;

use App\Models\Product;
use App\Models\Merchant;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Console\OptimizeCommand;

class ProductPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view the pickup.
     *
     * @param  \App\Models\Merchant  $merchant
     * @return mixed
     */
    public function view(Merchant $merchant, Product $product)
    {
        if (optional($product)->id) {
            return $merchant->id === $product->merchant_id;
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
    public function create(Merchant $merchant, Product $product)
    {
        if (optional($product)->id) {
            return $merchant->id === $product->merchant_id;
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
    public function update(Merchant $merchant, Product $product)
    {
        if (optional($product)->id) {
            return $merchant->id === $product->merchant_id;
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
    public function delete(Merchant $merchant, Product $product)
    {
        if (optional($product)->id) {
            return $merchant->id === $product->merchant_id;
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
    public function forceDelete(Merchant $merchant, Product $product)
    {
        //
    }
}
