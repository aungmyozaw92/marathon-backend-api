<?php

namespace App\Policies\Mobile;

use App\Models\Merchant;
use App\Models\MerchantAssociate;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\AuthorizationException;

class MerchantAssociatePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view the MerchantAssociate.
     *
     * @param  \App\Models\Merchant  $merchant
     * @param  \App\MerchantAssociate  $merchantAssociate
     * @return mixed
     */
    public function view(Merchant $merchant, MerchantAssociate $merchant_associate)
    {
        if (optional($merchant_associate->merchant)->id) {
            return $merchant->id === $merchant_associate->merchant->id;
        } else {
            throw new AuthorizationException("This action is unauthorized.");
        }
    }

    /**
     * Determine whether the user can create MerchantAssociates.
     *
     * @param  \App\Models\Merchant  $merchant
     * @return mixed
     */
    public function create(Merchant $merchant)
    {
        //
    }

    /**
     * Determine whether the user can update the MerchantAssociate.
     *
     * @param  \App\Models\Merchant  $merchant
     * @param  \App\MerchantAssociate  $merchantAssociate
     * @return mixed
     */
    public function update(Merchant $merchant, MerchantAssociate $merchant_associate)
    {
        if (optional($merchant_associate->merchant)->id) {
            return $merchant->id === $merchant_associate->merchant->id;
        } else {
            throw new AuthorizationException("This action is unauthorized.");
        }
    }

    /**
     * Determine whether the user can delete the MerchantAssociate.
     *
     * @param  \App\Models\Merchant  $merchant
     * @param  \App\MerchantAssociate  $merchantAssociate
     * @return mixed
     */
    public function delete(Merchant $merchant, MerchantAssociate $merchant_associate)
    {
        if (optional($merchant_associate->merchant)->id) {
            return $merchant->id === $merchant_associate->merchant->id;
        } else {
            throw new AuthorizationException("This action is unauthorized.");
        }
    }

    /**
     * Determine whether the user can restore the MerchantAssociate.
     *
     * @param  \App\Models\Merchant  $merchant
     * @param  \App\MerchantAssociate  $merchantAssociate
     * @return mixed
     */
    public function restore(Merchant $merchant, MerchantAssociate $merchantAssociate)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the MerchantAssociate.
     *
     * @param  \App\Models\Merchant  $merchant
     * @param  \App\MerchantAssociate  $merchantAssociate
     * @return mixed
     */
    public function forceDelete(Merchant $merchant, MerchantAssociate $merchantAssociate)
    {
        //
    }
}
