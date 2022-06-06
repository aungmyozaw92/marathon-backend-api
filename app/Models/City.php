<?php

namespace App\Models;

use App\Models\Route;
use App\Models\Staff;
use App\Models\Branch;
use App\Models\Account;
use App\Models\TrackingVoucher;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class City extends Model
{
    use SoftDeletes;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'cities';

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

    protected $casts = [
        'is_available_d2d' => 'boolean',
        'is_available_ecom' => 'boolean',
        'is_collect_only' => 'boolean',
        'is_on_demand' => 'boolean',
    ];

    /**
     * Attributes that should be mass-assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'name_mm', 'is_collect_only', 'is_on_demand', 'locking', 'locked_by',
        'created_by', 'updated_by', 'deleted_by', 'is_available_d2d','is_available_ecom'
    ];

    public function scopeFilter($query, $filter)
    {
        if (isset($filter['is_available_ecom']) && $is_available_ecom = $filter['is_available_ecom']) {
            $query->where('is_available_ecom', $is_available_ecom);
        }
    }

    public function zones()
    {
        return $this->hasMany(Zone::class);
    }
    public function staffs()
    {
        return $this->hasMany(Staff::class);
    }
    public function routes()
    {
        return $this->hasMany(Route::class)->withTrashed();
    }

    public function locked_by_staff()
    {
        return $this->belongsTo(Staff::class, 'locked_by')->withTrashed();
    }

    public function merchant_associates()
    {
        return $this->hasMany(MerchantAssociate::class);
    }

    public function vouchers()
    {
        return $this->hasMany(Voucher::class)->withTrashed();
    }
    public function accounts()
    {
        return $this->hasMany(Account::class)->withTrashed();
    }

    public function branch()
    {
        return $this->hasOne(Branch::class);
    }
    public function agent()
    {
        return $this->hasOne(Agent::class)->where('is_active', true);
    }

    public function agents()
    {
        return $this->hasMany(Agent::class)->where('is_active', true);
    }

    public function tracking_vouchers()
    {
        return $this->hasMany(TrackingVoucher::class);
    }
}
