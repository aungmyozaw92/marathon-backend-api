<?php

namespace App\Policies\Mobile;

use App\Models\ProductVariation;
use App\Models\Merchant;
use Illuminate\Auth\Access\HandlesAuthorization;

class ProductVariationPolicy
{

    use HandlesAuthorization;

    /**
     * Determine whether the user can view the product_variation.
     *
     * @param  \App\Models\Merchant  $merchant
     * @param  \App\ProductVariation  $product_variation
     * @return mixed
     */
    public function view(Merchant $merchant, ProductVariation $product_variation)
    {
        return $merchant->id === $product_variation->product->merchant_id;
    }

    /**
     * Determine whether the user can create product_variation.
     *
     * @param  \App\Models\Merchant  $merchant
     * @return mixed
     */
    public function create(Merchant $merchant)
    {
        //
    }

    /**
     * Determine whether the user can update the product_variation.
     *
     * @param  \App\Models\Merchant  $merchant
     * @param  \App\ProductVariation  $product_variation
     * @return mixed
     */
    public function update(Merchant $merchant, ProductVariation $product_variation)
    {
        return $merchant->id === $product_variation->product->merchant_id;
    }

    /**
     * Determine whether the user can delete the product_variation.
     *
     * @param  \App\Models\Merchant  $merchant
     * @param  \App\Store  $product_variation
     * @return mixed
     */
    public function delete(Merchant $merchant, ProductVariation $product_variation)
    {
        return $merchant->id === $product_variation->product->merchant_id;
    }

    /**
     * Determine whether the user can restore the product_variation.
     *
     * @param  \App\Models\Merchant  $merchant
     * @param  \App\ProductVariation  $product_variation
     * @return mixed
     */
    public function restore(Merchant $merchant, ProductVariation $product_variation)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the product_variation.
     *
     * @param  \App\Models\Merchant  $merchant
     * @param  \App\ProductVariation  $product_variation
     * @return mixed
     */
    public function forceDelete(Merchant $merchant, ProductVariation $product_variation)
    {
        //
    }
}
