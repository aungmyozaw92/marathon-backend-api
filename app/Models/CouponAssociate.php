<?php

namespace App\Models;

use Carbon\Carbon;
use App\Models\Coupon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CouponAssociate extends Model
{
    use SoftDeletes;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'coupon_associates';

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
        'valid' => 'boolean'
    ];

    /**
     * Attributes that should be mass-assignable.
     *
     * @var array
     */
    protected $fillable = [
        'coupon_id', 'valid', 'code', 'created_by', 'updated_by', 'deleted_by',
    ];

    public function coupon()
    {
        return $this->belongsTo(Coupon::class)->withTrashed();
    }

    public function valid_coupon()
    {
        return $this->belongsTo(Coupon::class, 'coupon_id')->validDate();
    }

    // public function scopeFilter($query, $filter)
    // {
    //     $code = isset($filter['code'])?$filter['code']:null;

    //     $query->where('code', $code)->where('valid', 1)->first();
    //     //dd($coupon_associate->coupon()->validDate()->first());
    //     // dd($coupon_associate->valid_coupon);
    //     return $query->valid_coupon;
   
    //     // if ($coupon) {
    //     //     $coupon;
    //     // } else {
    //     //     null;
    //     // }
    // }
}
