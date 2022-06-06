<?php

namespace App\Models;

use App\Models\Qr;
use App\Models\Pickup;
use App\Models\Message;
use App\Models\AgentBadge;
use App\Models\Transaction;
use App\Models\PickupHistory;
use App\Models\VoucherHistory;
use App\Models\WaybillHistory;
use App\Models\AccountInformation;
use Illuminate\Database\Eloquent\Model;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Agent extends Authenticatable implements JWTSubject
{
    use SoftDeletes;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'agents';

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
        'on_demand' => 'boolean',
        'is_active' => 'boolean',
        'delivery_commission' => 'integer'
    ];

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
     * Attributes that should be mass-assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'username', 'password', 'city_id', 'token', 'on_demand',
        'agent_branch', 'delivery_commission', 'address', 'phone','is_active',
        'created_by', 'updated_by', 'deleted_by', 'agent_badge_id', 'rewards',
        'is_positive_monthly', 'monthly_collected_amount', 'weekly_collected_amount',
        'shop_name','account_code'
    ];

    /**
     * Accessors
     */
    // public function getDeliveryCommissionAttribute($value)
    // {
    //     return number_format($value);
    // }

    /**
     * scopes
     */
    public function scopeFilter($query, $filter)
    {
        if ($filter['balance'] === '0') {
            $filter['balance'] = '0.00';
        }

        if (isset($filter['search']) && $search = $filter['search']) {
            $query->where(function ($qr) use ($search) {
                $qr->where('name', 'ILIKE', "%{$search}%")
                    ->orWhere('username', 'ILIKE', "%{$search}%")
                    ->orWhere(function ($q) use ($search) {
                        $q->whereHas('city', function ($q1) use ($search) {
                            $q1->where('name', 'ILIKE', "%{$search}%");
                        });
                    });
            });
        }

        if (isset($filter['name']) && $name = $filter['name']) {
            $query->where('name', 'ILIKE', "%{$name}%");
        }

        if (isset($filter['account_code']) && $account_code = $filter['account_code']) {
            $query->where('account_code', 'ILIKE', "%{$account_code}%");
        }

        if (isset($filter['shop_name']) && $shop_name = $filter['shop_name']) {
            $query->where('shop_name', 'ILIKE', "%{$shop_name}%");
        }

        if (isset($filter['username']) && $username = $filter['username']) {
            $query->where('username', 'ILIKE', "%{$username}%");
        }

        if (isset($filter['city_id']) && $city_id = $filter['city_id']) {
            $query->where('city_id', $city_id);
        }

        if (isset($filter['agent_badge_id']) && $agent_badge_id = $filter['agent_badge_id']) {
            $query->where('agent_badge_id', $agent_badge_id);
        }

        if (isset($filter['phone']) && $phone = $filter['phone']) {
            $query->where('phone', 'ILIKE', "%{$phone}%");
        }

        if (isset($filter['is_active']) && $is_active = $filter['is_active']) {
            $query->where('is_active', $is_active);
        }

        if (isset($filter['balance']) && $balance = $filter['balance']) {
            $balance_operator = isset($filter['balance_operator']) ? $filter['balance_operator'] : "=";
            $query->whereHas('account', function ($qr) use ($balance, $balance_operator) {
                $qr->where('balance', $balance_operator, $balance);
            });
        }
    }

    public function scopeOrder($query, $order)
    {
        $sortBy = isset($order['sortBy']) ? $order['sortBy'] : 'id';
        $orderBy = isset($order['orderBy']) ? $order['orderBy'] : 'desc';

        $query->orderBy($sortBy, $orderBy);
    }

    public function city()
    {
        return $this->belongsTo(City::class)->withTrashed();
    }

    public function agent_badge()
    {
        return $this->belongsTo(AgentBadge::class)->withTrashed();
    }

    /**
     * qr relation .
     */
    public function qr()
    {
        return $this->morphOne(Qr::class, 'actor');
    }

    public function account_informations()
    {
        return $this->morphMany(AccountInformation::class, 'resourceable');
    }


    public function account()
    {
        return $this->morphOne('App\Models\Account', 'accountable');
    }

    public function waybill()
    {
        return $this->morphOne('App\Models\Waybill', 'receivable');
    }

    public function pickup_histories()
    {
        return $this->morphMany(PickupHistory::class, 'createable', 'created_by_type', 'created_by');
    }

    public function voucher_histories()
    {
        return $this->morphMany(VoucherHistory::class, 'createable', 'created_by_type', 'created_by');
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
        return $this->morphMany(Pickup::class, 'pickuped_by', 'pickuped_by_type', 'pickuped_by');
    }
    public function waybill_histories()
    {
        return $this->morphMany(WaybillHistory::class, 'createable', 'created_by_type', 'created_by');
    }

    public function attachments()
    {
        return $this->morphMany('App\Models\Attachment', 'resourceable', 'resource_type', 'resource_id');
    }

    public function messages()
    {
        return $this->morphMany(Message::class, 'messenger', 'messenger_type', 'messenger_id');
    }

    public function pending_balance()
    {
        $pending_topup_amount = 0.00;
        $pending_withdraw_amount = 0.00;
        if ($this->account) {
            $transactions = Transaction::where('status', 0)
                                            ->where(function ($q) {
                                                $q->where('from_account_id', $this->account->id)
                                                    ->orWhere('to_account_id', $this->account->id);
                                            })->get();
                                    
            $pending_topup_amount = $transactions->where('type', 'Topup')->sum('amount');
            $pending_withdraw_amount = $transactions->where('type', 'Withdraw')->sum('amount');
        }
        return $pending_topup_amount - $pending_withdraw_amount;
    }

    public function from_agent_waybills()
    {
        return $this->hasMany(Wabill::class,'from_agent_id');
    }

    public function to_agent_waybills()
    {
        return $this->hasMany(Wabill::class,'to_agent_id');
    }

    public function payment_receive_by_pickups()
    {
        return $this->morphMany(Pickup::class, 'payment_receive_by', 'payment_receive_by_type', 'payment_receive_by');
    }
}
