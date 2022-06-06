<?php

namespace App\Policies\ThirdParty;

use App\Models\Voucher;
use App\Models\Merchant;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\AuthorizationException;

class VoucherPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view the Voucher.
     *
     * @param  \App\Models\Merchant  $merchant
     * @param  \App\Voucher  $voucher
     * @return mixed
     */
    public function view(Merchant $merchant, Voucher $voucher)
    {
        return ($voucher->created_by_type === 'Merchant' && $voucher->created_by_id === $merchant->id);
    }
}
