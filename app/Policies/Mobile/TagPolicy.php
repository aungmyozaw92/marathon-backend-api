<?php

namespace App\Policies\Mobile;

use App\Models\Tag;
use App\Models\Merchant;
use Illuminate\Auth\Access\HandlesAuthorization;

class TagPolicy
{

    use HandlesAuthorization;

    /**
     * Determine whether the user can view the tag.
     *
     * @param  \App\Models\Merchant  $merchant
     * @param  \App\Tag  $tag
     * @return mixed
     */
    public function view(Merchant $merchant, Tag $tag)
    {
        return $merchant->id === $tag->merchant_id;
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
     * @param  \App\Tag  $tag
     * @return mixed
     */
    public function update(Merchant $merchant, Tag $tag)
    {
        return $merchant->id === $tag->merchant_id;
    }

    /**
     * Determine whether the user can delete the tag.
     *
     * @param  \App\Models\Merchant  $merchant
     * @param  \App\Store  $tag
     * @return mixed
     */
    public function delete(Merchant $merchant, Tag $tag)
    {
        return $merchant->id === $tag->merchant_id;
    }

    /**
     * Determine whether the user can restore the tag.
     *
     * @param  \App\Models\Merchant  $merchant
     * @param  \App\Tag  $tag
     * @return mixed
     */
    public function restore(Merchant $merchant, Tag $tag)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the tag.
     *
     * @param  \App\Models\Merchant  $merchant
     * @param  \App\Tag  $tag
     * @return mixed
     */
    public function forceDelete(Merchant $merchant, Tag $tag)
    {
        //
    }
}
