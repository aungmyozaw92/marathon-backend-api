<?php

namespace App\Policies\MerchantDashboard;

use App\Models\Order;
use App\Models\Merchant;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\AuthorizationException;

class OrderPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view the pickup.
     *
     * @param  \App\Models\Merchant  $merchant
     * @return mixed
     */
    public function view(Merchant $merchant, Order $order)
    {
        if (optional($order)->id) {
            return $merchant->id === $order->merchant_id;
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
    public function create(Merchant $merchant, Order $order)
    {
        if (optional($order)->id) {
            return $merchant->id === $order->merchant_id;
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
    public function update(Merchant $merchant, Order $order)
    {
        if (optional($order)->id) {
            return $merchant->id === $order->merchant_id;
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
    public function delete(Merchant $merchant, Order $order)
    {
        if (optional($order)->id) {
            return $merchant->id === $order->merchant_id;
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
    public function forceDelete(Merchant $merchant, Order $order)
    {
        //
    }
}
