<?php

namespace App\Models;

use App\Models\City;
use App\Models\Zone;
use App\Models\PickupHistory;
use App\Models\VoucherHistory;
use App\Models\WaybillHistory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Branch extends Model
{
    use SoftDeletes;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'branches';

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
        'pickup_fee' => 'integer',
        'dropoff_price' => 'integer',
        'transition_amout' => 'integer',
        'transition_fee' => 'integer',
        'insurance_fee' => 'integer',
        'warehouse_fee' => 'integer',
        'lunch' => 'integer',
        'delivery_commission' => 'integer',
        'pickup_commission' => 'integer',
        'postpone_fee' => 'integer',
        'immediately_return_fee' => 'integer'
    ];

    /**
     * Attributes that should be mass-assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'city_id', 'zone_id', 'username', 'password', 'lang', 'currency', 'pronoun_male',
        'pronoun_female', 'decimal', 'login', 'datetime', 'pickup_fee', 'pickup_min_qty', 'scale_unit',
        'weight_unit', 'dropoff_price', 'target_sale', 'target_coupon', 'target_start_date', 'target_end_date',
        'transition_amout', 'transition_fee', 'insurance_fee', 'warehouse_fee', 'agent_fee_base_rate', 'rounding',
        'return_percentage', 'lunch', 'delivery_commission', 'pickup_commission', 'postpone_day', 'postpone_fee',
        'immediately_return_fee', 'attendance', 'duration'
    ];

    /**
     * Accessors
     */
    // public function getPickupFeeAttribute($value)
    // {
    //     return number_format($value);
    // }

    // public function getDropoffPriceAttribute($value)
    // {
    //     return number_format($value);
    // }

    // public function getTransitionAmoutAttribute($value)
    // {
    //     return number_format($value);
    // }

    // public function getTransitionFeeAttribute($value)
    // {
    //     return number_format($value);
    // }

    // public function getInsuranceFeeAttribute($value)
    // {
    //     return number_format($value);
    // }


    // public function getWarehouseFeeAttribute($value)
    // {
    //     return number_format($value);
    // }

    // public function getLunchAttribute($value)
    // {
    //     return number_format($value);
    // }

    // public function getDeliveryCommissionAttribute($value)
    // {
    //     return number_format($value);
    // }

    // public function getPickupCommissionAttribute($value)
    // {
    //     return number_format($value);
    // }

    // public function getPostponeFeeAttribute($value)
    // {
    //     return number_format($value);
    // }

    // public function getImmediatelyReturnFeeAttribute($value)
    // {
    //     return number_format($value);
    // }

    public function city()
    {
        return $this->belongsTo(City::class)->withTrashed();
    }

    public function zone()
    {
        return $this->belongsTo(Zone::class)->withTrashed();
    }

    public function account()
    {
        return $this->morphOne('App\Models\Account', 'accountable');
    }
    public function pickup_histories()
    {
        return $this->morphMany(PickupHistory::class, 'created_by', 'created_by_type', 'created_by');
    }
    public function voucher_histories()
    {
        return $this->morphMany(VoucherHistory::class, 'created_by', 'created_by_type', 'created_by');
    }
    public function waybill_histories()
    {
        return $this->morphMany(WaybillHistory::class, 'created_by', 'created_by_type', 'created_by');
    }
}
