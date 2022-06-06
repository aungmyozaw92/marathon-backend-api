<?php

namespace App\Policies\Mobile;

use App\Models\AccountInformation;
use App\Models\Merchant;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\AuthorizationException;

class AccountInformationPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view the AccountInformation.
     *
     * @param  \App\Models\Merchant  $merchant
     * @param  \App\AccountInformation  $accountInformation
     * @return mixed
     */
    public function view(Merchant $merchant, AccountInformation $account_information)
    {
        if (optional($account_information->merchant)->id) {
            return $merchant->id === $account_information->merchant->id;
        } else {
            throw new AuthorizationException("This action is unauthorized.");
        }
    }

    /**
     * Determine whether the user can create AccountInformations.
     *
     * @param  \App\Models\Merchant  $merchant
     * @return mixed
     */
    public function create(Merchant $merchant)
    {
        //
    }

    /**
     * Determine whether the user can update the AccountInformation.
     *
     * @param  \App\Models\Merchant  $merchant
     * @param  \App\AccountInformation  $accountInformation
     * @return mixed
     */
    public function update(Merchant $merchant, AccountInformation $account_information)
    {
        if (optional($account_information->merchant)->id) {
            return $merchant->id === $account_information->merchant->id;
        } else {
            throw new AuthorizationException("This action is unauthorized.");
        }
    }

    /**
     * Determine whether the user can delete the AccountInformation.
     *
     * @param  \App\Models\Merchant  $merchant
     * @param  \App\AccountInformation  $accountInformation
     * @return mixed
     */
    public function delete(Merchant $merchant, AccountInformation $account_information)
    {
        if (optional($account_information->merchant)->id) {
            return $merchant->id === $account_information->merchant->id;
        } else {
            throw new AuthorizationException("This action is unauthorized.");
        }
    }

    /**
     * Determine whether the user can restore the AccountInformation.
     *
     * @param  \App\Models\Merchant  $merchant
     * @param  \App\AccountInformation  $accountInformation
     * @return mixed
     */
    public function restore(Merchant $merchant, AccountInformation $account_information)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the AccountInformation.
     *
     * @param  \App\Models\Merchant  $merchant
     * @param  \App\AccountInformation  $accountInformation
     * @return mixed
     */
    public function forceDelete(Merchant $merchant, AccountInformation $account_information)
    {
        //
    }
}
