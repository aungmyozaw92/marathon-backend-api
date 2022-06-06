<?php

namespace App\Policies\MerchantDashboard;

use App\Models\Customer;
use App\Models\Merchant;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\AuthorizationException;

class CustomerPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view the pickup.
     *
     * @param  \App\Models\Merchant  $merchant
     * @return mixed
     */
    public function view(Merchant $merchant, Customer $customer)
    {
        if (optional($customer)->id) {
            $data = $merchant->merchant_customers->where('customer_id',$customer->id)->count();
            return $data;
        } else {
            throw new AuthorizationException("This action is unauthorized.");
        }
    }

    /**
     * Determine whether the user can delete the pickup.
     *
     * @param  \App\Models\Merchant  $merchant
     * @param  \App\Models\Customer  $customer
     * @return mixed
     */
    public function delete(Merchant $merchant, Customer $customer)
    {
        if (optional($customer)->id) {
            $data = $merchant->merchant_customers->where('customer_id',$customer->id)->count();
            return $data;
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
    public function view_voucher(Merchant $merchant, Customer $customer)
    {
        if (optional($customer)->id) {
            return $merchant->merchant_customers->where('customer_id',$customer->id)->count();
        } else {
            throw new AuthorizationException("This action is unauthorized.");
        }
    }

    
}
