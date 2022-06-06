<?php

namespace App\Models;

use App\Models\Tag;
use App\Models\Product;
use App\Models\Voucher;
use App\Models\Transaction;
use App\Models\PickupHistory;
use App\Models\VariationMeta;
use App\Models\VoucherHistory;
use App\Models\MerchantDiscount;
use App\Models\MerchantRateCard;
use App\Models\DeviceToken;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Merchant extends Authenticatable implements JWTSubject
{
    use Notifiable, SoftDeletes;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'merchants';

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
     * Attributes that should be mass-assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'username', 'password', 'balance', 'available_coupon', 'current_sale_count',
        'staff_id', 'city_id', 'created_by', 'updated_by', 'deleted_by', 'is_discount',
        'is_root_merchant', 'super_merchant_id', 'static_price_same_city', 'static_price_diff_city',
        'static_price_branch', 'is_corporate_merchant', 'facebook', 'facebook_url', 'max_withdraw_days',
        'account_code','merchant_no','is_ecommerce'
    ];

    protected $morphClass = 'MorphMerchant';

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
     * Mutators
     */
    public function setMerchantNoAttribute($value)
    {
        $this->attributes['merchant_no'] = 'M' . str_pad($value, 6, '0', STR_PAD_LEFT);
    }

    /**
     * Accessors & Mutators
     */
    public function getMerchantAssociateIdsAttribute()
    {
        return $this->merchant_associates->pluck('id')->toArray();
    }

    /**
     * Set the merchant's name.
     *
     * @param  string  $value
     * @return void
     */
    public function setNameAttribute($value)
    {
        $this->attributes['name'] = preg_replace('/\s+/', ' ', $value);
    }

    /**
     * scopes
     */
    public function scopeFilter($query, $filter)
    {
        if ($filter['balance'] === '0') {
            $filter['balance'] = '0.00';
        }

        if (isset($filter['search']) && $search = $filter['search']) {
            $query->where(function ($query1) use ($search) {
                $query1->where('name', 'ILIKE', "%{$search}%")
                    ->orWhere(function ($q) use ($search) {
                        $q->whereHas('merchant_associates', function ($qr) use ($search) {
                            $qr->where(function ($qr) use ($search) {
                                $qr->where('label', 'ILIKE', "%{$search}%")
                                    ->orWhere('address', 'ILIKE', "%{$search}%");
                            })->orWhereHas('contact_associates', function ($contact_q) use ($search) {
                                $contact_q->where('value', 'ILIKE', "%{$search}%");
                            });
                        });
                    });
            });
            // ->orWhere(function ($q) use ($search) {
            //     $q->whereHas('staff', function ($qr) use ($search) {
            //         $qr->where('name', 'ILIKE', "%{$search}%")
            //             ->orWhere('username', 'ILIKE', "%{$search}%");
            //     });
            // });
        }

        if (isset($filter['name']) && $name = $filter['name']) {
            $query->where('name', 'ILIKE', "%{$name}%");
        }

        if (isset($filter['account_code']) && $account_code = $filter['account_code']) {
            $query->where('account_code', 'ILIKE', "%{$account_code}%");
        }

        if (isset($filter['is_corporate_merchant']) && $is_corporate_merchant = $filter['is_corporate_merchant']) {
            $query->where('is_corporate_merchant', 1);
        }

        if (isset($filter['username']) && $username = $filter['username']) {
            $query->where('username', 'ILIKE', "%{$username}%");
        }

        if (isset($filter['staff_id']) && $staff_id = $filter['staff_id']) {
            $staff_id == "unassign" ? $query->whereNull('staff_id') : $query->where('staff_id', $staff_id);
        }

        if (isset($filter['merchant_id']) && $merchant_id = $filter['merchant_id']) {
            $query->where('id', $merchant_id);
        }

        if (isset($filter['city_id']) && $city_id = $filter['city_id']) {
            $query->where('city_id', $city_id);
        }

        if (isset($filter['is_deleted']) && $is_deleted = $filter['is_deleted']) {
            if ($is_deleted === "true") {
                $query->whereNotNull('deleted_at');
            }
        }

        if (isset($filter['label']) && $label = $filter['label']) {
            $query->whereHas('merchant_associates', function ($qr) use ($label) {
                $qr->where('label', 'ILIKE', "%{$label}%");
            });
        }

        if (isset($filter['address']) && $address = $filter['address']) {
            $query->whereHas('merchant_associates', function ($qr) use ($address) {
                $qr->where('address', 'ILIKE', "%{$address}%");
            });
        }

        if (isset($filter['phone']) && $phone = $filter['phone']) {
            $query->whereHas('contact_associates', function ($qr) use ($phone) {
                $qr->where('value', 'ILIKE', "%{$phone}%");
            });
        }

        if (isset($filter['email']) && $email = $filter['email']) {
            $query->whereHas('contact_associates', function ($qr) use ($email) {
                $qr->where('value', 'ILIKE', "%{$email}%");
            });
        }

        if (isset($filter['account_name']) && $account_name = $filter['account_name']) {
            $query->whereHas('account_informations', function ($qr) use ($account_name) {
                $qr->where('account_name', 'ILIKE', "%{$account_name}%");
            });
        }

        if (isset($filter['bank_id']) && $bank_id = $filter['bank_id']) {
            $query->whereHas('account_informations', function ($qr) use ($bank_id) {
                $qr->where('bank_id', $bank_id);
            });
        }

        if (isset($filter['account_no']) && $account_no = $filter['account_no']) {
            $query->whereHas('account_informations', function ($qr) use ($account_no) {
                $qr->where('account_no', 'ILIKE', "%{$account_no}%");
            });
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
    public function staff()
    {
        return $this->belongsTo(Staff::class);
    }

    public function zone()
    {
        return $this->belongsTo(Zone::class)->withTrashed();;
    }

    public function merchant_associates()
    {
        return $this->hasMany(MerchantAssociate::class);
    }

    public function contact_associates()
    {
        return $this->hasMany(ContactAssociate::class);
    }

    public function merchant_discounts()
    {
        return $this->hasMany(MerchantDiscount::class);
    }

    public function merchant_rate_cards()
    {
        return $this->hasMany(MerchantRateCard::class);
    }

    /**
     * Get all of the merchant's pickups.
     */
    public function pickups()
    {
        return $this->morphMany(Pickup::class, 'sender', 'sender_type', 'sender_id');
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
     * assigned relation .
     */

    public function assigned_by_pickups()
    {
        return $this->morphMany(Pickup::class, 'assigned_by', 'assigned_by_type', 'assigned_by');
    }

    public function pending_returning_actor_by_vouchers()
    {
        return $this->morphMany(Voucher::class, 'pending_returning_actor', 'pending_returning_actor_type', 'pending_returning_actor_id');
    }

    /**
     * account relation .
     */

    public function account()
    {
        return $this->morphOne('App\Models\Account', 'accountable');
    }
    /**
     * merchant_sheets relation .
     */
    public function merchant_sheets()
    {
        return $this->hasMany(MerchantSheet::class)->orderBy('id', 'decs');
    }
    /**
     * qr relation .
     */
    public function qr()
    {
        return $this->morphOne(Qr::class, 'actor');
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

    public function account_informations()
    {
        return $this->morphMany(AccountInformation::class, 'resourceable', 'resourceable_type', 'resourceable_id');
    }

    public function attachments()
    {
        return $this->morphMany('App\Models\Attachment', 'resourceable', 'resource_type', 'resource_id');
    }

    public function super_merchant()
    {
        return $this->belongsTo(Merchant::class, 'super_merchant_id');
    }

    public function sub_merchants()
    {
        return $this->hasMany(Merchant::class, 'super_merchant_id');
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function tags()
    {
        return $this->hasMany(Tag::class);
    }

    public function variation_metas()
    {
        return $this->hasMany(VariationMeta::class);
    }

    public function temp_journals()
    {
        return $this->hasMany(TempJournal::class);
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
    public function default_payment_type()
    {
        return $this->belongsTo(PaymentType::class, 'default_payment_type_id');
    }

    public function transactions()
    {
        return $this->morphMany(Transaction::class, 'created_by', 'created_by_type', 'created_by_id');
    }

    public function device_tokens()
    {
        return $this->morphMany(DeviceToken::class, 'referable', 'referable_type', 'referable_id');
    }

    public function customers()
    {
        return $this->belongsToMany(Customer::class, 'merchant_customers', 'merchant_id', 'customer_id')
            // ->using(DeliSheetVoucher::class)
            ->as('merchant_customers')
            ->withTimestamps()
            ->orderBy('id', 'decs');
        // ->opened();
    }

    public function merchant_customers()
    {
        return $this->hasMany(MerchantCustomer::class, 'merchant_id');
    }
}
