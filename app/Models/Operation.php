<?php

namespace App\Models;

use App\Models\Role;
use App\Models\Voucher;
use App\Models\Waybill;
use App\Models\VoucherHistory;
use App\Models\PickupHistory;
use App\Models\WaybillHistory;
use App\Models\Message;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Operation extends Authenticatable implements JWTSubject
{
    use Notifiable, SoftDeletes;

    /**
     * The "booting" method of the model.
     *
     * @return void
     */
    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope('department_id', function (Builder $builder) {
            $builder->where('department_id', 3);
        });
    }

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'staffs';

    protected $guard_name = 'api';

    protected $fillable = [
        'name', 'role_id', 'department_id', 'username', 'password', 'zone_id', 'courier_type_id',
        'created_by', 'updated_by', 'deleted_by',
    ];

    protected $morphClass = 'MorphStaff';

    public function username()
    {
        return 'username';
    }

    protected $hidden = [
        'password', 'remember_token',
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
        return $this->hasMany(DeliSheet::class, 'delivery_id')->orderBy('id', 'decs')->withTrashed();
    }

    public function bus_sheets()
    {
        return $this->hasMany(BusSheet::class, 'delivery_id')->orderBy('id', 'decs')->withTrashed();
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
    public function messages()
    {
        return $this->morphMany(Message::class, 'messenger', 'messenger_type', 'messenger_id');
    }
}
