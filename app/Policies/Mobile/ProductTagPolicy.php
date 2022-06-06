<?php

namespace App\Policies\Mobile;

use App\Models\ProductTag;
use App\Models\Merchant;
use Illuminate\Auth\Access\HandlesAuthorization;

class ProductTagPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view the tag.
     *
     * @param  \App\Models\Merchant  $merchant
     * @param  \App\ProductTag  $product_tag
     * @return mixed
     */
    public function view(Merchant $merchant, ProductTag $product_tag)
    {
        return $merchant->id === $product_tag->tag->merchant_id;
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
     * @param  \App\ProductTag  $product_tag
     * @return mixed
     */
    public function update(Merchant $merchant, ProductTag $product_tag)
    {
        return $merchant->id === $product_tag->tag->merchant_id;
    }

    /**
     * Determine whether the user can delete the tag.
     *
     * @param  \App\Models\Merchant  $merchant
     * @param  \App\ProductStore  $product_tag
     * @return mixed
     */
    public function delete(Merchant $merchant, ProductTag $product_tag)
    {
        return $merchant->id === $product_tag->tag->merchant_id;
    }

    /**
     * Determine whether the user can restore the tag.
     *
     * @param  \App\Models\Merchant  $merchant
     * @param  \App\ProductTag  $product_tag
     * @return mixed
     */
    public function restore(Merchant $merchant, ProductTag $product_tag)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the tag.
     *
     * @param  \App\Models\Merchant  $merchant
     * @param  \App\ProductTag  $product_tag
     * @return mixed
     */
    public function forceDelete(Merchant $merchant, ProductTag $product_tag)
    {
        //
    }
}
