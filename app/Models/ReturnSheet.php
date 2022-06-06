<?php

namespace App\Models;

use App\Models\Staff;
use App\Models\Pickup;
use App\Models\ReturnSheetHistory;
use App\Models\PointLog;
use App\Models\CommissionLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ReturnSheet extends Model
{
    use SoftDeletes;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'return_sheets';

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
        'is_closed' => 'boolean',
        'is_returned' => 'boolean',
        'sender_amount_to_collect' => 'integer',
        'receiver_amount_to_collect' => 'integer',
        'credit' => 'integer',
        'debit' => 'integer',
        'balance' => 'integer',
        'is_came_from_mobile' => 'boolean',
        'is_commissionable' => 'boolean',
        'is_pointable' => 'boolean'
    ];

    /**
     * Attributes that should be mass-assignable.
     *
     * @var array
     */
    protected $fillable = [
        'return_sheet_invoice', 'merchant_id', 'merchant_associate_id', 'qty', 'sender_amount_to_collect', 'receiver_amount_to_collect',
        'created_by', 'updated_by', 'deleted_by', 'is_paid', 'delivery_id', 'is_came_from_mobile', 'actby_mobile',
        'courier_type_id', 'is_commissionable', 'is_pointable'
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

    public function getQtyAttribute()
    {
        return $this->vouchers()->count();
    }

    /**
     * AMutators
     */
    public function setReturnSheetInvoiceAttribute($value)
    {
        $this->attributes['return_sheet_invoice'] = 'RN' . str_pad($value, 6, '0', STR_PAD_LEFT);
    }

    /**
     * Relations
     */

    public function delivery()
    {
        return $this->belongsTo(Staff::class, 'delivery_id')->withTrashed();
    }

    public function merchant()
    {
        return $this->belongsTo(Merchant::class)->withTrashed();
    }

    public function merchant_associate()
    {
        return $this->belongsTo(MerchantAssociate::class, 'merchant_associate_id')->with(['city', 'zone']);
    }

    // public function vouchers()
    // {
    //     return $this->belongsToMany(Voucher::class, 'return_sheet_vouchers', 'return_sheet_id', 'voucher_id')
    //         ->as('return_sheet_vouchers')
    //         ->withTimestamps()
    //         ->withPivot([
    //             'note',
    //             'priority',
    //             'created_by',
    //             'updated_by',
    //             'deleted_by'
    //         ]);
    // }

    public function vouchers()
    {
        return $this->belongsToMany(Voucher::class, 'return_sheet_vouchers', 'return_sheet_id', 'voucher_id')
            // ->using(DeliSheetVoucher::class)
            ->as('return_sheet_vouchers')
            ->withTimestamps()
            ->withPivot([
                'note',
                'priority',
                'created_by',
                'updated_by',
                'deleted_by'
            ]);
        // ->opened();
    }


    public function scopeFilter($query, $filter)
    {
        
        if (isset($filter['is_closed'])) {
            $query->where('is_closed', $filter['is_closed']);
        }

        if (isset($filter['is_returned'])) {
            // dd($filter['is_returned']);
            $query->where('is_returned', $filter['is_returned']);
        }

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
                    : $query->whereBetween('created_at', [$start_date, \Carbon\Carbon::parse($end_date)->addDays(1)]);
            } else {
                $query->whereDate('created_at', $start_date);
            }
        }

        if (isset($filter['created_at']) && $created_at = $filter['created_at']) {
            $query->whereDate('created_at', $created_at);
        }

        if (isset($filter['delivery_id']) && $delivery_id = $filter['delivery_id']) {
            $query->where('delivery_id', $delivery_id);
        }
    }

    public function scopeGetMerchantReturnSheets($query)
    {
        $query->where('merchant_id', auth()->user()->id);
    }

    public function return_sheet_histories()
    {
        return $this->hasMany(ReturnSheetHistory::class, 'return_sheet_id')->orderBy('id', 'desc');
    }
    public function returnSheetVoucherFire($voucherInvoice, $logStatus)
    {
        $data = array(
            'requests' => [
                'return_sheet_id' => $this->id,
                'previous' => $voucherInvoice,
                'logStatus' => $logStatus
            ]
        );
        \Event::fire('returnSheetForVoucher', array($data));
    }

    //
    public function point_logs()
    {
        return $this->morphMany(PointLog::class, 'resourceable', 'resourceable_type', 'resourceable_id');
    }

    public function acted_hero()
    {
        return $this->belongsTo(Staff::class, 'actby_mobile');
    }

    public function commission_logs()
    {
        return $this->morphMany(CommissionLog::class, 'commissionable', 'commissionable_type', 'commissionable_id');
    }

    public function attachments()
    {
        return $this->morphMany('App\Models\Attachment', 'resourceable', 'resource_type', 'resource_id');
    }
    
    public function issuer()
    {
        return $this->belongsTo(Staff::class, 'created_by')->withTrashed();
    }
    
    public function closed_by()
    {
        return $this->belongsTo(Staff::class, 'closed_by')->withTrashed();
    }

    public function created_by_staff()
    {
        return $this->belongsTo(Staff::class, 'created_by')->withTrashed();
    }

}
