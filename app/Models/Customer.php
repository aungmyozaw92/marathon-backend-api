<?php

namespace App\Models;

use App\Models\Qr;
use App\Models\City;
use App\Models\Flag;
use App\Models\Staff;
use App\Models\Pickup;
use App\Models\Voucher;
use App\Models\PickupHistory;
use App\Models\VoucherHistory;
use Illuminate\Database\Eloquent\Model;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Customer extends Authenticatable implements JWTSubject
// class Customer extends Model implements JWTSubject 

{
    use Notifiable, SoftDeletes;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'customers';

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
        // 'rate' => 'integer'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    protected $morphClass = 'MorphCustomer';

    public function username()
    {
        return 'phone';
    }
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
            'username' => $this->phone,
        ];
    }


    /**
     * Attributes that should be mass-assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'phone', 'other_phone', 'address', 'phone_confirmation_token', 'city_id',
        'zone_id', 'badge_id', 'point', 'order', 'success', 'return', 'rate',
        'created_by', 'updated_by', 'deleted_by', 'latitude', 'longitude', 'email'
    ];
    /**
     * @return string
     */
    public static function generateVerificationToken()
    {
        return str_random(40);
    }

    /**
     * scopes
     */
    public function scopePhone($query, $phone)
    {
        return $query->where('phone', $phone);
    }

    public function scopeFilter($query, $filter)
    {
        if (isset($filter['search']) && $search = $filter['search']) {
            $query->where('phone', $search);
        }
    }
    public function scopeFilterMerchant($query, $filter)
    {
        if (isset($filter['search']) && $search = $filter['search']) {
            $query->where('name', 'ILIKE', "%{$search}%")
                  ->orWhere('phone', 'ILIKE', "%{$search}%")
                  ->orWhere('address', 'ILIKE', "%{$search}%");;
        }
        if (isset($filter['name']) && $name = $filter['name']) {
            $query->where('name', 'ILIKE', "%{$name}%");
        }
        if (isset($filter['phone']) && $phone = $filter['phone']) {
            $query->where('phone', 'ILIKE', "%{$phone}%");
        }
        if (isset($filter['address']) && $address = $filter['address']) {
            $query->where('address', 'ILIKE', "%{$address}%");
        }
        if (isset($filter['city_id']) && $city_id = $filter['city_id']) {
            $query->where('city_id', $city_id);
        }
        if (isset($filter['zone_id']) && $zone_id = $filter['zone_id']) {
            $query->where('zone_id',  $zone_id);
        }
    }

    /**
     * Get the city of cutomer.
     */
    public function city()
    {
        return $this->belongsTo(City::class)->withTrashed();
    }

    /**
     * Get the zone of cutomer.
     */
    public function zone()
    {
        return $this->belongsTo(Zone::class)->withTrashed();
    }

    /**
     * Get the badge of cutomer.
     */
    public function badge()
    {
        return $this->belongsTo(Badge::class)->withTrashed();
    }

    /**
     * Get all of the customer's pickups.
     */
    public function pickups()
    {
        return $this->morphMany(Pickup::class, 'sender', 'sender_type', 'sender_id');
    }

    /**
     * Get all of the customer's flags.
     */
    public function flags()
    {
        return $this->belongsToMany(Flag::class, 'flagged_customers', 'customer_id', 'flag_id')
            ->as('flagged_customers')
            ->withPivot('frequency')
            ->withTimestamps();
    }

    public function vouchers()
    {
        return $this->hasMany(Voucher::class,'receiver_id')->withTrashed();
    }

    public function staff()
    {
        return $this->belongsTo(Staff::class);
    }
    /**
     * account relation .
     */

    public function account()
    {
        return $this->morphOne('App\Models\Account', 'accountable');
    }

    /**
     * qr relation .
     */
    public function qr()
    {
        return $this->morphOne(Qr::class, 'actor');
    }
    public function voucherPickupFire($logStatus)
    {
        $data = array(
            'requests' => [
                'pickup_id' => $this->pickup_id,
                'previous' => $this->getOriginal('name'),
                'next' => $this->name,
                'logStatus' => $logStatus
            ]
        );
        \Event::fire('customerInPickup', array($data));
    }
    //
    public function pickup_histories()
    {
        return $this->morphMany(PickupHistory::class, 'created_by', 'created_by_type', 'created_by');
    }
    public function voucher_histories()
    {
        return $this->morphMany(VoucherHistory::class, 'created_by', 'created_by_type', 'created_by');
    }

    public function merchants()
    {
        return $this->belongsToMany(Merchant::class, 'merchant_customers', 'customer_id', 'merchant_id')
            // ->using(DeliSheetVoucher::class)
            ->as('merchant_customers')
            ->withTimestamps();
    }
	// remove firestore
    // public function voucherFireStore($voucher)
    // {
    //     $home_collection = (isset($voucher->pickup) && isset($voucher->pickup->merchant)) ?
    //         $voucher->pickup->merchant->firestore_document
    //         : (auth()->user() ? auth()->user()->firestore_document : $voucher->created_by_merchant->firestore_document);
    //     if ($home_collection == null || $voucher->firestore_document == null) {
    //         return;
    //     }
    //     $changes = $this->getChanges();
    //     $expected_columns = ['name', 'address', 'phone', 'other_phone'];
    //     $changes = array_only($changes, $expected_columns);
    //     if (!empty($changes)) {
    //         $params = array('requests' => [
    //             'changes' => $changes,
    //             'homepage_document' => $home_collection,
    //             'voucher_document' => $voucher->firestore_document,
    //             'prefix' => 'customer'
    //         ]);
    //         \Event::fire('fireStoreVoucher', array($params));
    //     }
    // }

    public function updated_by()
    {
        return $this->morphTo()->withTrashed();
    }

    public function updated_by_merchant()
    {
        return BelongsToMorph::build($this, Merchant::class, 'updated_by');
    }

    public function updated_by_staff()
    {
        return BelongsToMorph::build($this, Staff::class, 'updated_by');
    }

}
