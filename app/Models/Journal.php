<?php

namespace App\Models;

use App\Models\Account;
use App\Models\PointLog;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

// use Illuminate\Database\Eloquent\SoftDeletes;

class Journal extends Model
{
    // use SoftDeletes;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'journals';

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
    //protected $dates = ['deleted_at'];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'amount' => 'integer'
    ];

    /**
     * Attributes that should be mass-assignable.
     *
     * @var array
     */
    protected $fillable = [
        'journal_no', 'debit_account_id', 'credit_account_id', 'amount', 'resourceable_type', 'status',
        'balance_status', 'resourceable_id'
    ];

    /**
     * Accessors
     */
    // public function getAmountAttribute($value)
    // {
    //     return number_format($value);
    // }

    public function setJournalNoAttribute($value)
    {
        $this->attributes['journal_no'] = 'JN' . str_pad($value, 6, '0', STR_PAD_LEFT);
    }

    public function scopeDebitAmount($query, $id, $account_id)
    {
        return $query->where('balance_status', 0)
            ->where('debit_account_id', $account_id)
            ->where('resourceable_id', $id)->sum('amount');
    }

    public function scopeCreditAmount($query, $id, $account_id)
    {
        return $query->where('balance_status', 0)
            ->where('credit_account_id', $account_id)
            ->where('resourceable_id', $id)->sum('amount');
    }

    public function scopeBranchSheet($query, $branch)
    {
        //dd($branch);
        $query->where('status', 0)
            ->where('resourceable_type', 'Voucher')
            ->whereHas('credit_account', function ($q) use ($branch) {
                $q->where('accountable_type', 'Branch')
                    ->where('accountable_id', $branch->account->id);
            })
            ->orWhereHas('debit_account', function ($q) use ($branch) {
                $q->where('accountable_type', 'Branch')
                    ->where('accountable_id', $branch->account->id);
            })
            // ->where('credit_account_id', $branch->account->id)
            // ->orWhere('debit_account_id', $branch->account->id)
            ->orderBy('id', 'desc');
    }

    public function scopeGetJournal($query, $merchant_account_id, $voucher_id)
    {
        $hq_account = getHqAccount();

        $query->where(function ($q) use ($hq_account, $merchant_account_id, $voucher_id) {
            $q->where('credit_account_id', $merchant_account_id);
            $q->where('debit_account_id', $hq_account->id);
            $q->where('status', 0);
            $q->where('balance_status', 0);
            $q->where('resourceable_type', 'Voucher');
            $q->where('resourceable_id', $voucher_id);
        })->orWhere(function ($q1) use ($hq_account, $merchant_account_id, $voucher_id) {
            $q1->where('credit_account_id', $hq_account->id);
            $q1->where('debit_account_id', $merchant_account_id);
            $q1->where('balance_status', 0);
            $q1->where('resourceable_type', 'Voucher');
            $q1->where('resourceable_id', $voucher_id);
        });
    }

    public function scopeGetTransactionJournal($query, $account_id, $filter)
    {
    
        $hq_account = getHqAccount();
        
        $query->where(function ($q) use ($hq_account, $account_id, $filter) {
            $q->where('credit_account_id', $account_id);

            if (isset($filter['start_date']) && $start_date = $filter['start_date']) {
                if (isset($filter['end_date']) && $end_date = $filter['end_date']) {
                    ($start_date == $end_date)
                        ? $q->whereDate('updated_at', $start_date)
                        : $q->whereBetween('updated_at', [$start_date, \Carbon\Carbon::parse($end_date)->addDays(1)]);
                } else {
                    $q->whereDate('updated_at', $start_date);
                }
            }
            //$q->where('debit_account_id', $hq_account->id);
            $q->where(function ($q) use ($hq_account, $filter) {
                $q->where(function ($q) use ($hq_account, $filter) {
                   
                    if (isset($filter['transaction']) && $transaction = $filter['transaction']) {
                        $q->where('resourceable_type', 'Transaction');
                    }else{
                        $q->where('status', 1);
                        $q->where('resourceable_type', 'Voucher');
                        $q->where('debit_account_id', $hq_account->id);
                    }
                    
                })->orWhere(function ($q) use ($hq_account) {
                    $q->where('resourceable_type', 'Transaction');
                });
            });
        })->orWhere(function ($q1) use ($hq_account, $account_id, $filter) {

            //$q1->where('credit_account_id', $hq_account->id);
            $q1->where('debit_account_id', $account_id);

            if (isset($filter['start_date']) && $start_date = $filter['start_date']) {
                if (isset($filter['end_date']) && $end_date = $filter['end_date']) {
                    ($start_date == $end_date)
                        ? $q1->whereDate('updated_at', $start_date)
                        : $q1->whereBetween('updated_at', [$start_date, \Carbon\Carbon::parse($end_date)->addDays(1)]);
                } else {
                    $q1->whereDate('updated_at', $start_date);
                }
            }

            $q1->where(function ($q) use ($hq_account , $filter) {
                $q->where(function ($q) use ($hq_account, $filter) {
                    if (isset($filter['transaction']) && $transaction = $filter['transaction']) {
                        $q->where('resourceable_type', 'Transaction');
                    }else{
                        $q->where('status', 1);
                        $q->where('resourceable_type', 'Voucher');
                        $q->where('credit_account_id', $hq_account->id);
                    }
                })->orWhere(function ($q) use ($hq_account) {
                    $q->where('resourceable_type', 'Transaction');
                });
            });
        });

        $query->orderBy('updated_at', 'desc');
    }

    public function scopeGetOnlyTransactionJournal($query, $account_id, $filter)
    {
        $hq_account = getHqAccount();
        
        $query->where(function ($q) use ($hq_account, $account_id, $filter) {
            $q->where('credit_account_id', $account_id);

            if (isset($filter['start_date']) && $start_date = $filter['start_date']) {
                if (isset($filter['end_date']) && $end_date = $filter['end_date']) {
                    ($start_date == $end_date)
                        ? $q->whereDate('updated_at', $start_date)
                        : $q->whereBetween('updated_at', [$start_date, \Carbon\Carbon::parse($end_date)->addDays(1)]);
                } else {
                    $q->whereDate('updated_at', $start_date);
                }
            }
            $q->where('resourceable_type', 'Transaction');
               
        })->orWhere(function ($q1) use ($hq_account, $account_id, $filter) {

            //$q1->where('credit_account_id', $hq_account->id);
            $q1->where('debit_account_id', $account_id);

            if (isset($filter['start_date']) && $start_date = $filter['start_date']) {
                if (isset($filter['end_date']) && $end_date = $filter['end_date']) {
                    ($start_date == $end_date)
                        ? $q1->whereDate('updated_at', $start_date)
                        : $q1->whereBetween('updated_at', [$start_date, \Carbon\Carbon::parse($end_date)->addDays(1)]);
                } else {
                    $q1->whereDate('updated_at', $start_date);
                }
            }

            $q1->where('resourceable_type', 'Transaction');
        
        });

        $query->orderBy('updated_at', 'desc');
    }

    public function scopeGetTransactionJournalSearch($query, $filter)
    {
        if (isset($filter['search']) && $search = $filter['search']) {
            $hq_account = getHqAccount();
            $account_id = auth()->user()->account->id;
            $query->where(function ($q) use ($hq_account, $account_id, $search) {
                $q->where('credit_account_id', $account_id);
                $q->where('debit_account_id', $hq_account->id);
                $q->where('status', 1);
                $q->where('resourceable_type', 'Voucher');
                $q->whereHas('voucher', function ($qr) use ($search) {
                    $qr->whereHas('receiver', function ($qu) use ($search) {
                        $qu->where('name', 'ILIKE', "%{$search}%")
                            ->orWhere('phone', 'ILIKE', "%{$search}%");
                    });
                });
            })->orWhere(function ($q1) use ($hq_account, $account_id, $search) {
                $q1->where('credit_account_id', $hq_account->id);
                $q1->where('debit_account_id', $account_id);
                $q1->where('status', 1);
                $q1->where('resourceable_type', 'Voucher');
                $q1->whereHas('voucher', function ($qr) use ($search) {
                    $qr->whereHas('receiver', function ($qu) use ($search) {
                        $qu->where('name', 'ILIKE', "%{$search}%")
                            ->orWhere('phone', 'ILIKE', "%{$search}%");
                    });
                });
            });
        }
    }

    public function scopeFilterAgentCommission($query, $filter)
    {
        $hq_account = getHqAccount();
        $agent = auth()->user();
        $agent_account_id = $agent->account->id;
        if (isset($filter['commission']) && $commission = $filter['commission']) {
            if (isset($filter['start_date']) && $start_date = $filter['start_date']) {
                if (isset($filter['end_date']) && $end_date = $filter['end_date']) {
                    ($start_date == $end_date)
                        ? $query->whereDate('created_at', $start_date)
                        : $query->whereBetween('created_at', [$start_date, \Carbon\Carbon::parse($end_date)->addDays(1)]);
                } else {
                    $query->whereDate('created_at', $start_date);
                }
            }
            $query->where('status', 1);
            $query->where('resourceable_type', 'Voucher');
            $query->where('credit_account_id', $agent_account_id);
            $query->where('debit_account_id', $hq_account->id);
        } else {
            $query->where(function ($q) use ($hq_account, $agent_account_id, $filter) {
                if (isset($filter['start_date']) && $start_date = $filter['start_date']) {
                    if (isset($filter['end_date']) && $end_date = $filter['end_date']) {
                        ($start_date == $end_date)
                            ? $q->whereDate('created_at', $start_date)
                            : $q->whereBetween('created_at', [$start_date, \Carbon\Carbon::parse($end_date)->addDays(1)]);
                    } else {
                        $q->whereDate('created_at', $start_date);
                    }
                }
                $q->where('status', 1);
                //$q->where('resourceable_type', 'Voucher');
                $q->where('credit_account_id', $agent_account_id);
                $q->where('debit_account_id', $hq_account->id);
            })
                ->orWhere(function ($q1) use ($hq_account, $agent_account_id, $filter) {
                    if (isset($filter['start_date']) && $start_date = $filter['start_date']) {
                        if (isset($filter['end_date']) && $end_date = $filter['end_date']) {
                            ($start_date == $end_date)
                                ? $q1->whereDate('created_at', $start_date)
                                : $q1->whereBetween('created_at', [$start_date, \Carbon\Carbon::parse($end_date)->addDays(1)]);
                        } else {
                            $q1->whereDate('created_at', $start_date);
                        }
                    }
                    $q1->where('status', 1);
                    //$q1->where('resourceable_type', 'Voucher');
                    $q1->where('credit_account_id', $hq_account->id);
                    $q1->where('debit_account_id', $agent_account_id);
                });
        }
        $query->orderBy('updated_at', 'DESC');
    }

    public function scopeFilterAgentCreditLists($query, $filter)
    {
        $hq_account = getHqAccount();
        $agent = auth()->user();
        $agent_account_id = $agent->account->id;
        if (isset($filter['start_date']) && $start_date = $filter['start_date']) {
            if (isset($filter['end_date']) && $end_date = $filter['end_date']) {
                ($start_date == $end_date)
                    ? $query->whereDate('created_at', $start_date)
                    : $query->whereBetween('created_at', [$start_date, \Carbon\Carbon::parse($end_date)->addDays(1)]);
            } else {
                $query->whereDate('created_at', $start_date);
            }
        }
        $query->where('status', 1)
            ->where('resourceable_type', 'Voucher')
            ->where('credit_account_id', $agent_account_id)
            ->where('debit_account_id', $hq_account->id)
            ->orderBy('updated_at', 'DESC');
    }

    public function scopeFilterAgentDebitLists($query, $filter)
    {
        $hq_account = getHqAccount();
        $agent = auth()->user();
        $agent_account_id = $agent->account->id;
        if (isset($filter['start_date']) && $start_date = $filter['start_date']) {
            if (isset($filter['end_date']) && $end_date = $filter['end_date']) {
                ($start_date == $end_date)
                    ? $query->whereDate('created_at', $start_date)
                    : $query->whereBetween('created_at', [$start_date, \Carbon\Carbon::parse($end_date)->addDays(1)]);
            } else {
                $query->whereDate('created_at', $start_date);
            }
        }
        $query->where('status', 1)
            ->where('resourceable_type', 'Voucher')
            ->where('credit_account_id', $hq_account->id)
            ->where('debit_account_id', $agent_account_id)
            ->orderBy('updated_at', 'DESC');
    }

    public function account()
    {
        return $this->belongsTo(Account::class)->withTrashed();
    }

    public function debit_account()
    {
        return $this->belongsTo(Account::class, 'debit_account_id')->withTrashed();
    }

    public function credit_account()
    {
        return $this->belongsTo(Account::class, 'credit_account_id')->withTrashed();
    }

    public function resourceable()
    {
        return $this->morphTo()->withTrashed();
    }

    public function voucher()
    {
        return $this->belongsTo(Voucher::class, 'resourceable_id');
    }
    //
    public function point_logs()
    {
        return $this->morphMany(PointLog::class, 'resourceable', 'resourceable_type', 'resourceable_id');
    }
}
