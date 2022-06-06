<?php

namespace App\Policies\Mobile;

use App\Models\VariationMeta;
use App\Models\Merchant;
use Illuminate\Auth\Access\HandlesAuthorization;

class VariationMetaPolicy
{

    use HandlesAuthorization;

    /**
     * Determine whether the user can view the variation_meta.
     *
     * @param  \App\Models\Merchant  $merchant
     * @param  \App\VariationMeta  $variation_meta
     * @return mixed
     */
    public function view(Merchant $merchant, VariationMeta $variation_meta)
    {
        return $merchant->id === $variation_meta->merchant_id;
    }

    /**
     * Determine whether the user can create variation_meta.
     *
     * @param  \App\Models\Merchant  $merchant
     * @return mixed
     */
    public function create(Merchant $merchant)
    {
        //
    }

    /**
     * Determine whether the user can update the variation_meta.
     *
     * @param  \App\Models\Merchant  $merchant
     * @param  \App\VariationMeta  $variation_meta
     * @return mixed
     */
    public function update(Merchant $merchant, VariationMeta $variation_meta)
    {
        return $merchant->id === $variation_meta->merchant_id;
    }

    /**
     * Determine whether the user can delete the variation_meta.
     *
     * @param  \App\Models\Merchant  $merchant
     * @param  \App\Store  $variation_meta
     * @return mixed
     */
    public function delete(Merchant $merchant, VariationMeta $variation_meta)
    {
        return $merchant->id === $variation_meta->merchant_id;
    }

    /**
     * Determine whether the user can restore the variation_meta.
     *
     * @param  \App\Models\Merchant  $merchant
     * @param  \App\VariationMeta  $variation_meta
     * @return mixed
     */
    public function restore(Merchant $merchant, VariationMeta $variation_meta)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the variation_meta.
     *
     * @param  \App\Models\Merchant  $merchant
     * @param  \App\VariationMeta  $variation_meta
     * @return mixed
     */
    public function forceDelete(Merchant $merchant, VariationMeta $variation_meta)
    {
        //
    }
}
