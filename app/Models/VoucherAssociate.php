<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VoucherAssociate extends Model
{

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'delivery_amount' => 'integer',
        'item_price' => 'integer',
        'total' => 'integer'
    ];

    /**
     * Accessors
     */
    // public function getDeliveryAmountAttribute($value)
    // {
    //     return number_format($value);
    // }

    // public function getItemPriceAttribute($value)
    // {
    //     return number_format($value);
    // }

    // public function getTotalAttribute($value)
    // {
    //     return number_format($value);
    // }
}
