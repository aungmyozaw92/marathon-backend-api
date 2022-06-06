<?php

namespace App\Policies\MerchantDashboard;

use App\Models\ProductType;
use App\Models\Merchant;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Console\OptimizeCommand;

class ProductTypePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view the pickup.
     *
     * @param  \App\Models\Merchant  $merchant
     * @return mixed
     */
    public function view(Merchant $merchant, ProductType $product_type)
    {
        if (optional($product_type)->merchant_id) {
            return $merchant->id === $product_type->merchant_id;
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
    public function create(Merchant $merchant, ProductType $product_type)
    {
        if (optional($product_type)->id) {
            return $merchant->id === $product_type->merchant_id;
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
    public function update(Merchant $merchant, ProductType $product_type)
    {
        if (optional($product_type)->merchant_id) {
            return $merchant->id === $product_type->merchant_id;
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
    public function delete(Merchant $merchant, ProductType $product_type)
    {
        if (optional($product_type)->id) {
            return $merchant->id === $product_type->merchant_id;
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
    public function forceDelete(Merchant $merchant, ProductType $product_type)
    {
        //
    }
}
