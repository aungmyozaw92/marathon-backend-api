<?php

namespace App\Models;

use Carbon\Carbon;
use App\Models\DiscountType;
use App\Models\CouponAssociate;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Coupon extends Model
{
    use SoftDeletes;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'coupons';

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
        'amount' => 'integer',
    ];

    /**
     * Attributes that should be mass-assignable.
     *
     * @var array
     */
    protected $fillable = [
        'discount_type_id', 'valid_date', 'amount',
        'created_by', 'updated_by', 'deleted_by',
    ];

    /**
     * Accessors
     */
    // public function getAmountAttribute($value)
    // {
    //     return number_format($value);
    // }

    public function coupon_associates()
    {
        return $this->hasMany(CouponAssociate::class);
    }
    
    public function discount_type()
    {
        return $this->belongsTo(DiscountType::class)->withTrashed();
    }

    public function scopeValidDate($query)
    {
        $query->whereDate('valid_date', '>=', \Carbon\Carbon::parse(\Carbon\Carbon::now())->format('Y-m-d H:i:s'));
    }
}
