<?php

namespace App\Models;

use App\Models\Zone;
use App\Models\Staff;
use App\Models\Attachment;
use App\Models\CourierType;
use App\Models\CommissionLog;
use App\Models\DeliSheetHistory;
use App\Models\DeliSheetVoucher;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DeliSheet extends Model
{
    use SoftDeletes;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'deli_sheets';

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
    protected $dates = ['deleted_at', 'date'];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'is_closed' => 'boolean',
        'is_paid' => 'boolean',
        'is_scanned' => 'boolean',
        'lunch_amount' => 'integer',
        'commission_amount' => 'integer',
        'collect_amount' => 'integer',
        'total_amount' => 'integer',
        'is_commissionable' => 'boolean',
        'is_came_from_mobile' => 'boolean',
        'is_pointable' => 'boolean'
    ];

    /**
     * Attributes that should be mass-assignable.
     *
     * @var array
     */
    protected $fillable = [
        'delisheet_invoice', 'qty', 'zone_id', 'delivery_id', 'staff_id', 'note', 'priority', 'is_closed',
        'created_by', 'updated_by', 'deleted_by', 'is_paid', 'is_scanned', 'date', 'courier_type_id', 
        'is_commissionable', 'is_came_from_mobile', 'actby_mobile', 'is_pointable','payment_token'
    ];

    /**
     * Accessors
     */
    // public function getLunchAmountAttribute($value)
    // {
    //     return number_format($value);
    // }

    // public function getCommissionAmountAttribute($value)
    // {
    //     return number_format($value);
    // }

    // public function getCollectAmountAttribute($value)
    // {
    //     return number_format($value);
    // }

    // public function getTotalAmountAttribute($value)
    // {
    //     return number_format($value);
    // }

    public function getQtyAttribute()
    {
        return $this->vouchers()->count();
    }

    /**
     * Mutators
     */
    public function setDelisheetInvoiceAttribute($value)
    {
        $this->attributes['delisheet_invoice'] = 'DN' . str_pad($value, 6, '0', STR_PAD_LEFT);
    }

    /**
     * scopes
     */
    public function scopeFilter($query, $filter)
    {
        // if (isset($filter['date']) && $date = $filter['date']) {
        //     $query->whereDate('created_at', $date);
        // }

        // if (isset($filter['start_date']) && $start_date = $filter['start_date']) {
        //     if (isset($filter['end_date']) && $end_date = $filter['end_date']) {
        //         $query->whereBetween('created_at', [$start_date, \Carbon\Carbon::parse($end_date)->addDays(1)]);
        //     } else {
        //         $query->whereDate('created_at', $start_date);
        //     }
        // }
        if (isset($filter['delisheet_invoice_no']) && $delisheet_invoice_no = $filter['delisheet_invoice_no']) {
            $query->where('delisheet_invoice', 'ILIKE', "%{$delisheet_invoice_no}%");
        }

        if (isset($filter['delisheet_invoice']) && $delisheet_invoice = $filter['delisheet_invoice']) {
            $query->where('delisheet_invoice', 'ILIKE', "%{$delisheet_invoice}%");
        }

        if (isset($filter['date']) && $date = $filter['date']) {
            $query->whereDate('date', $date);
            // ->orWhereDate('created_at', $date);
        }

        if (isset($filter['delivery_id']) && $delivery_id = $filter['delivery_id']) {
            $delivery_id == "unassign" ? $query->whereNull('delivery_id') : $query->where('delivery_id', $delivery_id);
        }

        if (isset($filter['start_date']) && $start_date = $filter['start_date']) {
            if (isset($filter['end_date']) && $end_date = $filter['end_date']) {
                // $query->whereBetween('date', [$start_date, $end_date]);
                ($start_date == $end_date)
                    ? $query->whereDate('date', $start_date)
                    : $query->whereBetween('date', [$start_date, \Carbon\Carbon::parse($end_date)->addDays(0)]);
            // ->orWhereBetween('created_at', [$start_date, \Carbon\Carbon::parse($end_date)->addDays(1)]);
            } else {
                $query->whereDate('date', $start_date);
                // ->orWhereDate('created_at', $start_date);
            }
        }

        if (isset($filter['is_closed']) && $is_closed = $filter['is_closed']) {
            $query->where('is_closed', $is_closed);
        }

        if (isset($filter['is_paid']) && $is_paid = $filter['is_paid']) {
            $query->where('is_paid', $is_paid);
        }

        if (isset($filter['created_at']) && $created_at = $filter['created_at']) {
            $query->whereDate('created_at', $created_at);
        }
    }

    public function scopeDeliveryFilter($query, $filter)
    {
        if (isset($filter['invoice_no']) && $invoice_no = $filter['invoice_no']) {
            $query->where('delisheet_invoice', 'ILIKE', "%{$invoice_no}%");
        }

        if (isset($filter['date']) && $date = $filter['date']) {
            $query->whereDate('date', $date);
            // ->orWhereDate('created_at', $date);
        }

        if (isset($filter['start_date']) && $start_date = $filter['start_date']) {
            if (isset($filter['end_date']) && $end_date = $filter['end_date']) {
                // $query->whereBetween('date', [$start_date, $end_date]);
                ($start_date == $end_date)
                    ? $query->whereDate('date', $start_date)
                    : $query->whereBetween('date', [$start_date, \Carbon\Carbon::parse($end_date)->addDays(0)]);
            // ->orWhereBetween('created_at', [$start_date, \Carbon\Carbon::parse($end_date)->addDays(1)]);
            } else {
                $query->whereDate('date', $start_date);
                // ->orWhereDate('created_at', $start_date);
            }
        }

        if (isset($filter['is_closed']) && $is_closed = $filter['is_closed']) {
            $query->where('is_closed', $is_closed);
        }

        if (isset($filter['is_paid']) && $is_paid = $filter['is_paid']) {
            $query->where('is_paid', $is_paid);
        }
    }

    /**
     * Relations
     */
    public function zone()
    {
        return $this->belongsTo(Zone::class)->withTrashed();
    }

    public function courier_type()
    {
        return $this->belongsTo(CourierType::class)->withTrashed();
    }

    public function created_by_staff()
    {
        return $this->belongsTo(Staff::class, 'created_by')->withTrashed();
    }

    public function delivery()
    {
        return $this->belongsTo(Staff::class, 'delivery_id')->withTrashed();
    }

    public function staff()
    {
        return $this->belongsTo(Staff::class)->withTrashed();
    }

    public function vouchers()
    {
        return $this->belongsToMany(Voucher::class, 'deli_sheet_vouchers', 'delisheet_id', 'voucher_id')
            // ->using(DeliSheetVoucher::class)
            ->as('deli_sheet_vouchers')
            ->withTimestamps()
            ->withPivot([
                'return',
                'note',
                'priority',
                'ATC_receiver',
                'delivery_status',
                'is_came_from_mobile',
                'created_by',
                'updated_by',
                'deleted_by',
                'cant_deliver'
            ])->orderBy('id', 'decs');
        // ->opened();
    }

    public function deli_sheet_vouchers()
    {
        return $this->hasMany(DeliSheetVoucher::class, 'delisheet_id')->withTrashed();
    }
    public function delisheetVoucherFire($voucherInvoice, $logStatus)
    {
        $data = array(
            'requests' => [
                'delisheet_id' => $this->id,
                'previous' => $voucherInvoice,
                'logStatus' => $logStatus
            ]
        );
        \Event::fire('deliSheetForVoucher', array($data));
    }
    public function delisheet_histories()
    {
        return $this->hasMany(DeliSheetHistory::class, 'delisheet_id')->orderBy('id', 'desc');
    }
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
        return $this->morphMany(Attachment::class, 'resourceable', 'resource_type', 'resource_id');
    }
}
