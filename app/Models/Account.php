<?php

namespace App\Models;

use App\Models\City;
use App\Models\Zone;
//use App\Models\Transaction;
use App\Models\Staff;
use App\Models\Branch;
use App\Models\Journal;
use App\Models\Customer;
use App\Models\Merchant;
use App\Models\BelongsToMorph;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Account extends Model
{
    use SoftDeletes;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'accounts';

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
        'credit' => 'integer',
        'debit'   => 'integer',
        'balance' => 'integer'
    ];

    /**
     * Attributes that should be mass-assignable.
     *
     * @var array
     */
    protected $fillable = [
        'account_no', 'city_id', 'accountable_type','accountable_id', 'credit', 'debit', 'balance'
    ];

    /**
     * Accessors
     */
    // public function getCreditAttribute($value)
    // {
    //     return number_format($value);
    // }

    // public function getDebitAttribute($value)
    // {
    //     return number_format($value);
    // }

    // public function getBalanceAttribute($value)
    // {
    //     return number_format($value);
    // }

    public function setAccountNoAttribute($value)
    {
        $this->attributes['account_no'] = 'A' . str_pad($value, 6, '0', STR_PAD_LEFT);
    }

    public function city()
    {
        return $this->belongsTo(City::class)->withTrashed();
    }

    public function journal()
    {
        return $this->belongsTo(Journal::class)->withTrashed();
    }

    // public function transaction()
    // {
    //     return $this->belongsTo(Transaction::class)->withTrashed();
    // }

    public function accountable()
    {
        return $this->morphTo()->withTrashed();
    }

    public function merchant()
    {
        return BelongsToMorph::build($this, Merchant::class, 'accountable');
    }

    public function customer()
    {
        return BelongsToMorph::build($this, Customer::class, 'accountable');
    }

    public function staff()
    {
        return BelongsToMorph::build($this, Staff::class, 'accountable');
    }

    public function zone()
    {
        return BelongsToMorph::build($this, Zone::class, 'accountable');
    }
    public function branch()
    {
        return BelongsToMorph::build($this, Branch::class, 'accountable');
    }

    public function gate()
    {
        return BelongsToMorph::build($this, Gate::class, 'accountable');
    }
    public function accounts()
    {
        return $this->hasMany(Account::class)->withTrashed();
    }

    /**
     * scopes
     */
    public function scopeMerchantFilter($query, $filter)
    {
        $query->where('accountable_type', 'Merchant');

        if (isset($filter['name']) && $name = $filter['name']) {
            $query->whereHas('merchant', function ($query) use ($name) {
                $query->where('name', 'ILIKE', "%{$name}%");
                // ->orWhere('phone', 'ILIKE', "%{$name}%");
            });
        }

        if (isset($filter['city_id']) && $city_id = $filter['city_id']) {
            $query->where('city_id', $city_id);
        }
    }

    public function pending_balance()
    {
        $pending_topup_amount = 0.00;
        $pending_withdraw_amount = 0.00;
        $transactions = Transaction::where('status', 0)
                                        ->where(function ($q) {
                                            $q->where('from_account_id', $this->id)
                                                ->orWhere('to_account_id', $this->id);
                                        })->get();
                                    
        $pending_topup_amount = $transactions->where('type', 'Topup')->sum('amount');
        $pending_withdraw_amount = $transactions->where('type', 'Withdraw')->sum('amount');
        return $pending_topup_amount - $pending_withdraw_amount;
    }
}
