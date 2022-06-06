<?php

namespace App\Models;

use App\Models\Pickup;
use App\Models\Merchant;
use App\Models\MerchantSheetHistory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MerchantSheet extends Model
{
    use SoftDeletes;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'merchant_sheets';

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
        'is_paid' => 'boolean',
        'sender_amount_to_collect' => 'integer',
        'receiver_amount_to_collect' => 'integer',
        'credit' => 'integer',
        'debit' => 'integer',
        'balance' => 'integer',
    ];

    /**
     * Attributes that should be mass-assignable.
     *
     * @var array
     */
    protected $fillable = [
        'merchantsheet_invoice', 'merchant_id', 'merchant_associate_id', 'qty', 'sender_amount_to_collect', 'receiver_amount_to_collect',
        'created_by', 'updated_by', 'deleted_by', 'is_paid', 'note'
    ];

    /**
     * Accessors
     */
    // public function getSenderAmountToCollectAttribute($value)
    // {
    //     return number_format($value);
    // }

    // public function getReceiverAmountToCollectAttribute($value)
    // {
    //     return number_format($value);
    // }

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

    /**
     * Mutators
     */
    public function setMerchantsheetInvoiceAttribute($value)
    {
        $this->attributes['merchantsheet_invoice'] = 'M' . str_pad($value, 6, '0', STR_PAD_LEFT);
    }

    /**
     * Relations
     */
    public function merchant()
    {
        return $this->belongsTo(Merchant::class)->withTrashed();
    }

    public function merchant_associate()
    {
        return $this->belongsTo(MerchantAssociate::class, 'merchant_associate_id')->with(['city', 'zone']);
    }

    public function vouchers()
    {
        return $this->belongsToMany(Voucher::class, 'merchant_sheet_vouchers', 'merchant_sheet_id', 'voucher_id')
            // ->using(DeliSheetVoucher::class)
            ->as('merchant_sheet_vouchers')
            ->withTimestamps()
            ->withPivot([
                'created_by',
                'updated_by',
                'deleted_by'
            ]);
        // ->opened();
    }

    public function scopeFilter($query, $filter)
    {
        if (isset($filter['date']) && $date = $filter['date']) {
            $query->whereDate('created_at', $date);
        }

        if (isset($filter['merchant_id']) && $merchant_id = $filter['merchant_id']) {
            $query->where('merchant_id', $merchant_id);
        }

        if (isset($filter['start_date']) && $start_date = $filter['start_date']) {
            if (isset($filter['end_date']) && $end_date = $filter['end_date']) {
                ($start_date == $end_date)
                    ? $query->whereDate('created_at', $start_date)
                    : $query->whereBetween('created_at', [$start_date, \Carbon\Carbon::parse($end_date)->addDays(0)]);
            } else {
                $query->whereDate('created_at', $start_date);
            }
        }
    }
    public function msfVoucherFire($voucherInvoice, $logStatus)
    {
        $data = array(
            'requests' => [
                'merchant_sheet_id' => $this->id,
                'previous' => $voucherInvoice,
                'logStatus' => $logStatus
            ]
        );
        \Event::fire('msfForVoucher', array($data));
    }
    public function merchantsheet_histories()
    {
        return $this->hasMany(MerchantSheetHistory::class, 'merchant_sheet_id')->whereHas('created_by_staff', function ($second) {
            $second->where('city_id', '=', auth()->user()->city_id);
        })->latest();
    }
}
