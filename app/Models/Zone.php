<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Zone extends Model
{
    use SoftDeletes;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'zones';

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
    protected $dates = [ 'deleted_at' ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'is_deliver' => 'boolean',
        'is_available_ecom' => 'boolean',
        // 'zone_rate' => 'integer',
        // 'zone_agent_rate' => 'integer',
        // 'zone_commission' => 'integer'
    ];

    /**
     * Attributes that should be mass-assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'name_mm',  'zone_rate', 'zone_agent_rate' ,'delivery_rate', 'city_id', 'created_by',
        'updated_by', 'deleted_by', 'is_deliver', 'note', 'outsource_rate',
         'zone_commission', 'outsource_car_rate', 'diff_zone_rate','is_available_ecom'
    ];

    protected $morphClass = 'MorphZone';

    public function scopeFilter($query, $filter)
    {
        if (isset($filter['is_available_ecom']) && $is_available_ecom = $filter['is_available_ecom']) {
            $query->where('is_available_ecom', $is_available_ecom);
        }
    }

    public function city()
    {
        return $this->belongsTo(City::class)->withTrashed();
    }

    public function merchant_associates()
    {
        return $this->hasMany(MerchantAssociate::class);
    }

    public function vouchers()
    {
        return $this->hasMany(Voucher::class)->withTrashed();
    }

    public function account()
    {
        return $this->morphOne('App\Models\Account', 'accountable');
    }
}
