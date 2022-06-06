<?php

namespace App\Policies\Mobile;

use App\Models\Pickup;
use App\Models\Merchant;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\AuthorizationException;

class PickupPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view the pickup.
     *
     * @param  \App\Models\Merchant  $merchant
     * @param  \App\Pickup  $pickup
     * @return mixed
     */
    public function view(Merchant $merchant, Pickup $pickup)
    {
        if (optional($pickup->merchant)->id) {
            return $merchant->id === $pickup->merchant->id;
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
    public function create(Merchant $merchant)
    {
        //
    }

    /**
     * Determine whether the user can update the pickup.
     *
     * @param  \App\Models\Merchant  $merchant
     * @param  \App\Pickup  $pickup
     * @return mixed
     */
    public function update(Merchant $merchant, Pickup $pickup)
    {
        if (optional($pickup->merchant)->id) {
            return $merchant->id === $pickup->merchant->id;
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
    public function delete(Merchant $merchant, Pickup $pickup)
    {
        if (optional($pickup->merchant)->id) {
            return $merchant->id === $pickup->merchant->id;
        } else {
            throw new AuthorizationException("This action is unauthorized.");
        }
    }

    /**
     * Determine whether the user can restore the pickup.
     *
     * @param  \App\Models\Merchant  $merchant
     * @param  \App\Pickup  $pickup
     * @return mixed
     */
    public function restore(Merchant $merchant, Pickup $pickup)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the pickup.
     *
     * @param  \App\Models\Merchant  $merchant
     * @param  \App\Pickup  $pickup
     * @return mixed
     */
    public function forceDelete(Merchant $merchant, Pickup $pickup)
    {
        //
    }
}
