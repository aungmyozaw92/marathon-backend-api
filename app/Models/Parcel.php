<?php

namespace App\Models;

use App\Models\Voucher;
use App\Models\ParcelItem;
use App\Models\GlobalScale;
use App\Models\DiscountType;
use App\Models\CouponAssociate;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Parcel extends Model
{
    use SoftDeletes;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'parcels';

    protected $guard_name = 'api';
    /**
     * The database primary key value.
     *
     * @var string
     */
    protected $primaryKey = 'id';

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['deleted_at'];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        // 'weight' => 'integer',
        'agent_fee' => 'integer',
        'cal_parcel_price' => 'integer',
        'cal_delivery_price' => 'integer',
        'cal_gate_price' => 'integer',
        'discount_price' => 'integer',
        'coupon_price' => 'integer',
        'label_parcel_price' => 'integer',
        'label_delivery_price' => 'integer',
        'label_gate_price' => 'integer',
        'sub_total' => 'integer',
    ];

    /**
     * Attributes that should be mass-assignable.
     *
     * @var array
     */
    protected $fillable = [
        'voucher_id', 'global_scale_id', 'discount_type_id', 'coupon_associate_id', 'cal_delivery_price',
        'discount_price', 'coupon_price', 'sub_total', 'weight', 'cal_gate_price', 'cal_parcel_price', 'label_parcel_price',
        'label_parcel_price', 'label_delivery_price', 'label_gate_price', 'agent_fee',
        'created_by', 'updated_by', 'deleted_by', 'origin_lwh', 'origin_weight', 'seller_discount'
    ];

    /**
     * Accessors
     */
    // public function getAgentFeeAttribute($value)
    // {
    //     return number_format($value);
    // }

    // public function getCalParcelPriceAttribute($value)
    // {
    //     return number_format($value);
    // }
    // public function getCalDeliveryPriceAttribute($value)
    // {
    //     return number_format($value);
    // }

    // public function getCalGatePriceAttribute($value)
    // {
    //     return number_format($value);
    // }

    // public function getDiscountPriceAttribute($value)
    // {
    //     return number_format($value);
    // }

    // public function getCouponPriceAttribute($value)
    // {
    //     return number_format($value);
    // }

    // public function getLabelParcelPriceAttribute($value)
    // {
    //     return number_format($value);
    // }

    // public function getLabelDeliveryPriceAttribute($value)
    // {
    //     return number_format($value);
    // }

    // public function getLabelGatePriceAttribute($value)
    // {
    //     return number_format($value);
    // }

    // public function getSubTotalAttribute($value)
    // {
    //     return number_format($value);
    // }


    /**
     * Mutators
     */
    public function setCouponPricetAttribute($value)
    {
        $this->attributes['coupon_price'] = number_format((float)$value, 2, '.', '');
    }
    public function setWeightAttribute($value)
    {
        $this->attributes['weight'] = number_format((float)$value, 2, '.', '');
    }

    public function parcel_items()
    {
        return $this->hasMany(ParcelItem::class);
    }
    public function global_scale()
    {
        return $this->belongsTo(GlobalScale::class)->withTrashed();
    }
    public function discount_type()
    {
        return $this->belongsTo(DiscountType::class)->withTrashed();
    }
    public function coupon_associate()
    {
        return $this->belongsTo(CouponAssociate::class)->withTrashed();
    }
    public function voucher()
    {
        return $this->belongsTo(Voucher::class)->withTrashed();
    }
}
