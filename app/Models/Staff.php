<?php

namespace App\Models;

use App\Models\City;
use App\Models\Role;
use App\Models\Pickup;
use App\Models\Message;
use App\Models\Voucher;
use App\Models\Waybill;
use App\Models\Merchant;
use App\Models\HeroBadge;
use App\Models\Attachment;
use App\Models\Transaction;
use App\Models\CommissionLog;
use App\Models\PickupHistory;
use App\Models\VoucherHistory;
use App\Models\WaybillHistory;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Staff extends Authenticatable implements JWTSubject
{
    use Notifiable, SoftDeletes;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'staffs';

    protected $guard_name = 'api';

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        // 'points' => 'integer'
        'is_commissionable' => 'boolean',
        'is_pointable' => 'boolean'
    ];

    protected $fillable = [
        'name', 'role_id', 'car_no','department_id', 'username', 'password', 'zone_id', 'courier_type_id', 'staff_type', 'hero_badge_id',
        'phone', 'created_by', 'updated_by', 'deleted_by', 'is_present', 'points', 'city_id', 'is_commissionable', 'is_pointable'
    ];

    protected $morphClass = 'MorphStaff';

    public function username()
    {
        return 'username';
    }

    protected $hidden = [
        'password', 'remember_token', 'token'
    ];

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [
            'id' => $this->id,
            'username' => $this->username,
        ];
    }

    /**
     * scopes
     */
    public function scopeFilter($query, $filter)
    {
        if (isset($filter['search']) && $search = $filter['search']) {
            $query->where('name', 'ILIKE', "%{$search}%");
        }
    }

    public function department()
    {
        return $this->belongsTo(Department::class)->withTrashed();
    }

    public function role()
    {
        return $this->belongsTo(Role::class)->withTrashed();
    }

    public function zone()
    {
        return $this->belongsTo(Zone::class)->withTrashed();
    }

    public function city()
    {
        return $this->belongsTo(City::class)->withTrashed();
    }

    public function courier_type()
    {
        return $this->belongsTo(CourierType::class)->withTrashed();
    }

    public function pickups()
    {
        return $this->hasMany(Pickup::class, 'opened_by')->withTrashed();
    }

    public function roles()
    {
        return $this->belongsToMany('App\Models\Role', 'staff_role', 'staff_id', 'role_id');
    }

    public function hasAnyRole($roles)
    {
        if (is_array($roles)) {
            foreach ($roles as $role) {
                if ($this->hasRole($role)) {
                    return true;
                }
            }
        } else {
            if ($this->hasRole($roles)) {
                return true;
            }
        }

        return false;
    }

    public function hasRole($role)
    {
        if ($this->roles()->where('name', $role)->first()) {
            return true;
        }

        return false;
    }

    /**
     * Delivery's deliSheets
     */
    public function deli_sheets()
    {
        return $this->hasMany(DeliSheet::class, 'delivery_id')->orderBy('updated_at', 'decs')->withTrashed();
    }

    public function bus_sheets()
    {
        return $this->hasMany(BusSheet::class, 'delivery_id')->orderBy('updated_at', 'decs')->withTrashed();
    }

    public function account()
    {
        return $this->morphOne('App\Models\Account', 'accountable');
    }

    /**
     * Waybill's deliSheets
     */
    public function waybills()
    {
        return $this->hasMany(Waybill::class, 'delivery_id')->withTrashed();
    }

    public function vouchers()
    {
        return $this->morphMany(Voucher::class, 'created_by', 'created_by_type', 'created_by_id');
    }

    /**
     * Created Relation
     */

    public function created_by_pickups()
    {
        return $this->morphMany(Pickup::class, 'created_by', 'created_by_type', 'created_by_id');
    }
    /**
     * updated relation .
     */

    public function updated_by_pickups()
    {
        return $this->morphMany(Pickup::class, 'updated_by', 'updated_by_type', 'updated_by');
    }
    /**
     * deleted relation .
     */

    public function deleted_by_pickups()
    {
        return $this->morphMany(Pickup::class, 'deleted_by', 'deleted_by_type', 'deleted_by');
    }
    /**
     * assigned relation .
     */

    public function assigned_by_pickups()
    {
        return $this->morphMany(Pickup::class, 'assigned_by', 'assigned_by_type', 'assigned_by');
    }
    /**
     * pickuped relation .
     */

    public function pickuped_by_pickups()
    {
        return $this->morphMany(Pickup::class, 'pickuped_by', 'pickuped_by_type', 'pickuped_by_id');
    }

    // Return SHeets
    public function return_sheets()
    {
        return $this->hasMany(ReturnSheet::class, 'delivery_id')->withTrashed();
    }

    /*
    * Merchant Relation
    */

    public function merchants()
    {
        return $this->hasMany(Merchant::class)->withTrashed();
    }
    //
    public function pickup_histories()
    {
        return $this->morphMany(PickupHistory::class, 'createable', 'created_by_type', 'created_by');
    }
    public function voucher_histories()
    {
        return $this->morphMany(VoucherHistory::class, 'createable', 'created_by_type', 'created_by');
    }
    public function waybill_histories()
    {
        return $this->morphMany(WaybillHistory::class, 'createable', 'created_by_type', 'created_by');
    }

    public function waybill()
    {
        return $this->morphOne('App\Models\Waybill', 'receivable');
    }
    public function messages()
    {
        return $this->morphMany(Message::class, 'messenger', 'messenger_type', 'messenger_id');
    }
    public function hero_badge()
    {
        return $this->belongsTo(HeroBadge::class)->withTrashed();
    }
    public function journals()
    {
        return $this->morphMany('App\Models\Journal', 'resourceable');
    }

    // Staff Profile
    public function profile()
    {
        return $this->morphOne(Attachment::class, 'resourceable', 'resource_type', 'resource_id');
    }

    public function commission_logs()
    {
        return $this->hasMany(CommissionLog::class);
    }

    public function transactions()
    {
        return $this->morphMany(Transaction::class, 'created_by', 'created_by_type', 'created_by_id');
    }

    public function payment_receive_by_pickups()
    {
        return $this->morphMany(Pickup::class, 'payment_receive_by', 'payment_receive_by_type', 'payment_receive_by');
    }
}
