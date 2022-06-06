<?php

namespace App\Models;

use App\Models\City;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TempJournal extends Model
{
     use SoftDeletes;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'temp_journals';

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
    // protected $casts = [
    //     'amount' => 'integer'
    // ];

    /**
     * Attributes that should be mass-assignable.
     *
     * @var array
     */
    protected $fillable = [
        'journal_no',
        'merchant_id',
        'city_id',
        'debit_account_id',
        'credit_account_id',
        'amount',
        'resourceable_id',
        'resourceable_type',
        'status',
        'thirdparty_invoice',
        'voucher_no',
        'pickup_date',
        'delivered_date',
        'receiver_name',
        'receiver_address',
        'receiver_phone',
        'receiver_city',
        'receiver_zone',
        'total_amount_to_collect',
        'voucher_remark',
        'balance_status',
        'delivery_status_id',
        'delivery_status',
        'weight',
        'created_by',
        'updated_by'

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
        $this->attributes['journal_no'] = 'TJ' . str_pad($value, 6, '0', STR_PAD_LEFT);
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

    public function merchant()
    {
        return $this->belongsTo(Merchant::class, 'merchant_id');
    }

    public function city()
    {
        return $this->belongsTo(City::class, 'city_id');
    }

    public function scopeFilter($query, $filter)
    {
        if (isset($filter['start_date']) && $start_date = $filter['start_date']) {
            if (isset($filter['end_date']) && $end_date = $filter['end_date']) {
                ($start_date == $end_date)
                    ? $query->whereDate('delivered_date', $start_date)
                    : $query->whereBetween('delivered_date', [$start_date, \Carbon\Carbon::parse($end_date)->addDays(0)]);
            // $query->whereBetween('created_at', [$start_date, $end_date]);
            } else {
                $query->whereDate('delivered_date', $start_date);
            }
        }
    }

    // public function invoices()
    // {
    //     return $this->belongsToMany(Invoice::class, 'temp_journals', 'temp_journal_id', 'invoice_id')
    //         // ->using(DeliSheetVoucher::class)
    //         ->as('temp_journals')
    //         ->withTimestamps()
    //         ->withPivot([
    //             'invoice_no',
    //             'merchant_id',
    //             'debit_account_id',
    //             'credit_account_id',
    //             'amount',
    //             'adjustment_amount',
    //             'resourceable_type',
    //             'resourceable_id',
    //             'status',
    //             'is_dirty',
    //             'thirdparty_invoice',
    //             'voucher_no',
    //             'pickup_date',
    //             'delivered_date',
    //             'receiver_name',
    //             'receiver_address',
    //             'receiver_phone',
    //             'receiver_city',
    //             'receiver_zone',
    //             'total_amount_to_collect',
    //             'voucher_remark',
    //             'balance_status',
    //             'adjustment_by',
    //             'adjustment_date',
    //             'adjustment_note',
    //             'created_by',
    //             'updated_by',
    //             'deleted_by'
    //         ]);
    // }
}
