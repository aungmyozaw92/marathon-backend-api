<?php

namespace App\Models;

use App\Models\Account;
use App\Models\Invoice;
use App\Models\Voucher;
use App\Models\Merchant;
use App\Models\TempJournal;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class InvoiceJournal extends Model
{
    use SoftDeletes;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'invoice_journals';

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
        'invoice_id',
        'temp_journal_id',
        'invoice_no',
        'merchant_id',
        'debit_account_id',
        'credit_account_id',
        'amount',
        'adjustment_amount',
        'diff_adjustment_amount',
        'resourceable_type',
        'resourceable_id',
        'status',
        'is_dirty',
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
        'adjustment_by',
        'adjustment_by_name',
        'adjustment_date',
        'adjustment_note',
        'delivery_status_id',
        'delivery_status',
        'weight',
        'created_by',
        'updated_by',
        'deleted_by'
    ];

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

    public function invoice()
    {
        return $this->belongsTo(Invoice::class, 'invoice_id');
    }
    public function temp_journal()
    {
        return $this->belongsTo(TempJournal::class, 'temp_journal_id');
    }
}