<?php

namespace App\Policies\Mobile;

use App\Models\ProductDiscount;
use App\Models\Merchant;
use Illuminate\Auth\Access\HandlesAuthorization;

class ProductDiscountPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view the tag.
     *
     * @param  \App\Models\Merchant  $merchant
     * @param  \App\ProductDiscount  $product_discount
     * @return mixed
     */
    public function view(Merchant $merchant, ProductDiscount $product_discount)
    {
        return $merchant->id === $product_discount->merchant_id;
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
     * Determine whether the user can update the tag.
     *
     * @param  \App\Models\Merchant  $merchant
     * @param  \App\ProductDiscount  $product_discount
     * @return mixed
     */
    public function update(Merchant $merchant, ProductDiscount $product_discount)
    {
        return $merchant->id === $product_discount->merchant_id;
    }

    /**
     * Determine whether the user can delete the tag.
     *
     * @param  \App\Models\Merchant  $merchant
     * @param  \App\ProductStore  $product_discount
     * @return mixed
     */
    public function delete(Merchant $merchant, ProductDiscount $product_discount)
    {
        return $merchant->id === $product_discount->merchant_id;
    }

    /**
     * Determine whether the user can restore the tag.
     *
     * @param  \App\Models\Merchant  $merchant
     * @param  \App\ProductDiscount  $product_discount
     * @return mixed
     */
    public function restore(Merchant $merchant, ProductDiscount $product_discount)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the tag.
     *
     * @param  \App\Models\Merchant  $merchant
     * @param  \App\ProductDiscount  $product_discount
     * @return mixed
     */
    public function forceDelete(Merchant $merchant, ProductDiscount $product_discount)
    {
        //
    }
}
