<?php

namespace App\Models;

use App\Models\City;
use App\Models\Merchant;
use App\Models\TempJournal;
use App\Models\InvoiceHistory;
use App\Models\InvoiceJournal;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Invoice extends Model
{
    use SoftDeletes;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'invoices';

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
        'invoice_no',
        'merchant_id',
        'city_id',
        'total_voucher',
        'total_amount',
        'payment_status',
        'note',
        'tax',
        'created_by',
        'updated_by',
        'deleted_by'
    ];

    public function scopeFilter($query, $filter)
    {
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

        if (isset($filter['merchant_id']) && $merchant_id = $filter['merchant_id']) {
            $query->where('merchant_id', $merchant_id);
        }

        if (isset($filter['invoice_no']) && $invoice_no = $filter['invoice_no']) {
            $query->where('invoice_no', 'ILIKE', "%{$invoice_no}%");
        }

    }

    public function setInvoiceNoAttribute($value)
    {
        $this->attributes['invoice_no'] = 'IN' . str_pad($value, 6, '0', STR_PAD_LEFT);
    }

    public function merchant()
    {
        return $this->belongsTo(Merchant::class, 'merchant_id');
    }

    public function invoice_journals()
    {
        return $this->hasMany(InvoiceJournal::class);
    }
    public function invoice_histories()
    {
        return $this->hasMany(InvoiceHistory::class);
    }

    public function city()
    {
        return $this->belongsTo(City::class, 'city_id');
    }
    
    public function attachments()
    {
        return $this->morphMany('App\Models\Attachment', 'resourceable', 'resource_type', 'resource_id');
    }



    // public function temp_journals()
    // {
    //     return $this->belongsToMany(TempJournal::class, 'invoice_journals', 'invoice_id', 'temp_journal_id')
    //         // ->using(DeliSheetVoucher::class)
    //         ->as('invoice_journals')
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
    //         ])->orderBy('id', 'decs');
    //     // ->opened();
    // }
}
