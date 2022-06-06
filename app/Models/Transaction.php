<?php

namespace App\Models;

use App\Models\Bank;
use App\Models\Zone;
use App\Models\Staff;
use App\Models\Account;
use App\Models\Journal;
use App\Models\Merchant;
use App\Models\AccountInformation;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Transaction extends Model
{
    use SoftDeletes;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'transactions';

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
        'hq_balance' => 'integer',
        'other_account_balance' => 'integer',
        'amount' => 'integer',
        'extra_amount' => 'integer',
        'status' => 'boolean',
    ];

    /**
     * Attributes that should be mass-assignable.
     *
     * @var array
     */
    protected $fillable = [
        'transaction_no', 'from_account_id', 'to_account_id', 'amount', 'note','extra_amount',
        'account_information_id', 'type', 'status','created_by', 'updated_by', 'deleted_by',
        'account_name', 'account_no', 'bank_id', 'created_by_id', 'created_by_type'
    ];

    /**
     * Accessors
     */
    // public function getHqBalanceAttribute($value)
    // {
    //     return number_format($value);
    // }

    // public function getOtherAccountBalanceAttribute($value)
    // {
    //     return number_format($value);
    // }

    // public function getAmountAttribute($value)
    // {
    //     return number_format($value);
    // }

    // public function getExtraAmountAttribute($value)
    // {
    //     return number_format($value);
    // }

    /**
     * Accessors & Mutators
     */
    // public function setTransactionNoAttribute($value)
    // {
    //     $this->attributes['transaction_no'] = 'T' . str_pad($value, 6, '0', STR_PAD_LEFT);
    // }

    public function from_account()
    {
        return $this->belongsTo(Account::class, 'from_account_id');
    }

    public function to_account()
    {
        return $this->belongsTo(Account::class, 'to_account_id');
    }

    public function attachments()
    {
        return $this->morphMany('App\Models\Attachment', 'resourceable', 'resource_type', 'resource_id');
    }

    public function journal()
    {
        return $this->morphOne(Journal::class, 'resourceable', 'resourceable_type', 'resourceable_id');
    }

    public function scopeFilter($query, $filter)
    {
        // echo $filter['end_date'];
        // dd($filter['start_date']);
        if (isset($filter['start_date']) && $start_date = $filter['start_date']) {
            if (isset($filter['end_date']) && $end_date = $filter['end_date']) {
                ($start_date == $end_date)
                    ? $query->whereDate('created_at', $start_date)
                    : $query->whereBetween('created_at', [$start_date, \Carbon\Carbon::parse($end_date)->addDays(1)]);
            // $query->whereBetween('created_at', [$start_date, $end_date]);
            } else {
                $query->whereDate('created_at', $start_date);
            }
        }
        
        if (isset($filter['from_city_id']) && $from_city_id = $filter['from_city_id']) {
            $query->whereHas('from_account', function ($q) use ($from_city_id) {
                $q->where('city_id', $from_city_id);
            });
        }
        if (isset($filter['to_city_id']) && $to_city_id = $filter['to_city_id']) {
            $query->whereHas('to_account', function ($q) use ($to_city_id) {
                $q->where('city_id', $to_city_id);
            });
        }

        if (isset($filter['bank_id']) && $bank_id = $filter['bank_id']) {
            $bank_id == 'unassign_bank'
                ?  $query->whereDoesntHave('account_information')
                :  $query->whereHas('account_information', function ($q) use ($bank_id) {
                    $q->where('bank_id', $bank_id);
                });
        }

        if (isset($filter['account_name']) && $account_name = $filter['account_name']) {
            $query->whereHas('account_information', function ($q) use ($account_name) {
                $q->where('account_name', 'ILIKE', "%{$account_name}%");
            });
        }

        if (isset($filter['account_no']) && $account_no = $filter['account_no']) {
            $query->whereHas('account_information', function ($q) use ($account_no) {
                $q->where('account_no', $account_no);
            });
        }

        if (isset($filter['amount']) && $amount = $filter['amount']) {
            $query->where('amount', $amount);
        }

        if (isset($filter['transaction_type']) && $transaction_type = $filter['transaction_type']) {
            $query->where('type', $transaction_type);
        }
        if (isset($filter['status']) && $status = $filter['status']) {
            if ($status == 'Confirm') {
                $query->where('status', 1);
            } elseif ($status == 'Pending') {
                $query->where('status', 0);
            }
        }
        if (isset($filter['transaction_no']) && $transaction_no = $filter['transaction_no']) {
            $query->where('transaction_no', 'like', "%{$transaction_no}%");
        }

        if (isset($filter['confirm_date']) && $confirm_date = $filter['confirm_date']) {
            $query->whereDate('updated_at', $confirm_date);
        }

        if (isset($filter['merchant_id']) && $merchant_id = $filter['merchant_id']) {
            $query->where(function ($q) use ($merchant_id) {
                $q->whereHas('to_account', function ($qr) use ($merchant_id) {
                    $qr->where('accountable_id', $merchant_id)
                        ->where('accountable_type', 'Merchant');
                })
                ->orWhereHas('from_account', function ($qr) use ($merchant_id) {
                    $qr->where('accountable_id', $merchant_id)
                        ->where('accountable_type', 'Merchant');
                });
            });
        }

        if (isset($filter['branch_id']) && $branch_id = $filter['branch_id']) {
            $query->where(function ($q) use ($branch_id) {
                $q->whereHas('to_account', function ($qr) use ($branch_id) {
                    $qr->where('accountable_type', 'Branch');
                    $qr->where('accountable_id', $branch_id);
                })->orwhereHas('from_account', function ($qr) use ($branch_id) {
                    $qr->where('accountable_type', 'Branch');
                    $qr->where('accountable_id', $branch_id);
                });
            });
        }
        if (isset($filter['type']) && $type = $filter['type']) {
            $auth = auth()->user();

            // $query->where(function ($q) use ($type, $auth) {
            $query->where(function ($q) use ($type, $auth) {
                    $q->whereHas('to_account', function ($qr) use ($type, $auth) {
                        $qr->where('accountable_type', $type);
                        if ($auth->role->name != 'HQ') {
                            $qr->where('city_id', $auth->city_id);
                        }
                    })->orWhereHas('from_account', function ($qr) use ($type, $auth) {
                        $qr->where('accountable_type', $type);
                        if ($auth->role->name != 'HQ') {
                            $qr->where('city_id', $auth->city_id);
                        }
                    });
                });
                // ->orWhere(function ($q) use ($type, $auth) {
                //     $q->where(function ($q) use ($type, $auth){
                //         $q->whereHas('to_account', function ($qr) use ($type, $auth) {
                //             $qr->where('accountable_type', $type);
                //         })->whereHas('from_account', function ($qr) use ($auth) {
                //             $qr->where('accountable_type', 'Branch');
                //             $qr->where('city_id', $auth->city_id);
                //         });
                //     })->orWhere(function ($q) use ($type, $auth){
                //         $q->whereHas('from_account', function ($qr) use ($type, $auth) {
                //             $qr->where('accountable_type', $type);
                //         })->whereHas('to_account', function ($qr) use ($auth) {
                //             $qr->where('accountable_type', 'Branch');
                //             $qr->where('city_id', $auth->city_id);
                //         });
                //     });
                    
                // });
                
            // });
        } else {
            $type = 'Merchant';
            $query->whereHas('to_account', function ($q) use ($type) {
                $q->where('accountable_type', '!=', $type);
            })->whereHas('from_account', function ($q) use ($type) {
                $q->where('accountable_type', '!=', $type);
            });
        }

        if (isset($filter['start_date']) && $start_date = $filter['start_date']) {
            if (isset($filter['end_date']) && $end_date = $filter['end_date']) {
                ($start_date == $end_date)
                    ? $query->whereDate('created_at', $start_date)
                    : $query->whereBetween('created_at', [$start_date, \Carbon\Carbon::parse($end_date)->addDays(1)]);
            // $query->whereBetween('created_at', [$start_date, $end_date]);
            } else {
                $query->whereDate('created_at', $start_date);
            }
        }

        if (isset($filter['created_by_type']) && $created_by_type = $filter['created_by_type']) {
            $query->where('created_by_type', $created_by_type);
        }

        if (isset($filter['topup']) || isset($filter['withdraw'])) {
            $query->where('type', 'Topup')->orWhere('type', 'Withdraw');
        } elseif (isset($filter['weekly_bonus'])) {
            $query->where('type', 'WeeklyBonus');
        } elseif (isset($filter['monthly_bonus'])) {
            $query->where('type', 'MonthlyBonus')->orWhere('type', 'MembershipReward');
        }
       
        $query->orderBy('id', 'DESC');
    }

    public function scopeOrder($query, $order)
    {
        $sortBy = isset($order['sortBy']) ? $order['sortBy'] : 'id';
        $orderBy = isset($order['orderBy']) ? $order['orderBy'] : 'desc';

        $query->orderBy($sortBy, $orderBy);
    }

    public function scopeGetHqBalanceFilter($query, $filter)
    {
        if (isset($filter['start_date']) && $start_date = $filter['start_date']) {
            if (isset($filter['end_date']) && $end_date = $filter['end_date']) {
                ($start_date == $end_date)
                    ? $query->whereDate('created_at', $start_date)
                    : $query->whereBetween('created_at', [$start_date, \Carbon\Carbon::parse($end_date)->addDays(1)]);
            } else {
                $query->whereDate('created_at', $start_date);
            }
        }
    }

    /**
     * For Journal Resourceable query
    */
    public function payment_type()
    {
        return $this->belongsTo(PaymentType::class);
    }

    public function delivery_status()
    {
        return $this->belongsTo(DeliveryStatus::class);
    }

    public function receiver()
    {
        return $this->belongsTo(Customer::class);
    }
    public function sender_city()
    {
        return $this->belongsTo(City::class);
    }
    public function receiver_city()
    {
        return $this->belongsTo(City::class);
    }
     public function receiver_zone()
    {
        return $this->belongsTo(Zone::class);
    }
    public function pickup()
    {
        return $this->belongsTo(Pickup::class);
    }
    
    public function account_information()
    {
        return $this->belongsTo(AccountInformation::class);
    }

    public function bank()
    {
        return $this->belongsTo(Bank::class)->withTrashed();
    }

    public function created_by()
    {
        return $this->morphTo()->withTrashed();
    }

    public function created_by_merchant()
    {
        return BelongsToMorph::build($this, Merchant::class, 'created_by');
    }

    public function created_by_staff()
    {
        return BelongsToMorph::build($this, Staff::class, 'created_by');
    }

    public function scopeGetMerchantPendingTransactions($query)
    {

        $query->where('status', false);
        $query->where('from_account_id', auth()->user()->account->id)
            ->orWhere('to_account_id', auth()->user()->account->id);
    }
}
