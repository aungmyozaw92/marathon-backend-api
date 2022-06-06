<?php

namespace App\Policies\Mobile;

use App\Models\ProductReview;
use App\Models\Merchant;
use Illuminate\Auth\Access\HandlesAuthorization;

class ProductReviewPolicy
{

    use HandlesAuthorization;

    /**
     * Determine whether the user can view the product_review.
     *
     * @param  \App\Models\Merchant  $merchant
     * @param  \App\ProductReview  $product_review
     * @return mixed
     */
    public function view(Merchant $merchant, ProductReview $product_review)
    {
        return $merchant->id === $product_review->product->merchant_id;
    }

    /**
     * Determine whether the user can create product_review.
     *
     * @param  \App\Models\Merchant  $merchant
     * @return mixed
     */
    public function create(Merchant $merchant)
    {
        //
    }

    /**
     * Determine whether the user can update the product_review.
     *
     * @param  \App\Models\Merchant  $merchant
     * @param  \App\ProductReview  $product_review
     * @return mixed
     */
    public function update(Merchant $merchant, ProductReview $product_review)
    {
        return $merchant->id === $product_review->product->merchant_id;
    }

    /**
     * Determine whether the user can delete the product_review.
     *
     * @param  \App\Models\Merchant  $merchant
     * @param  \App\Store  $product_review
     * @return mixed
     */
    public function delete(Merchant $merchant, ProductReview $product_review)
    {
        return $merchant->id === $product_review->product->merchant_id;
    }

    /**
     * Determine whether the user can restore the product_review.
     *
     * @param  \App\Models\Merchant  $merchant
     * @param  \App\ProductReview  $product_review
     * @return mixed
     */
    public function restore(Merchant $merchant, ProductReview $product_review)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the product_review.
     *
     * @param  \App\Models\Merchant  $merchant
     * @param  \App\ProductReview  $product_review
     * @return mixed
     */
    public function forceDelete(Merchant $merchant, ProductReview $product_review)
    {
        //
    }
}
