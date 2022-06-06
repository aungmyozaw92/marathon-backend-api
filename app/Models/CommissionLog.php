<?php

namespace App\Models;

use App\Models\Zone;
use App\Models\Staff;
use App\Models\Pickup;
use App\Models\Waybill;
use App\Models\DeliSheet;
use App\Models\ReturnSheet;
use App\Models\BelongsToMorph;
use Illuminate\Database\Eloquent\Model;

class CommissionLog extends Model
{
    //
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'commission_logs';

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

    /**
     * Attributes that should be mass-assignable.
     *
     * @var array
     */
    protected $fillable = [
        'staff_id', 'commissionable_id', 'commissionable_type', 'zone_id', 'zone_commission', 'voucher_commission', 'num_of_vouchers', 'created_by', 'updated_by'
    ];


    /**
     * scopes
     */
    public function scopeFilter($query, $filter)
    {
        if (isset($filter['staff_id']) && $staff_id = $filter['staff_id']) {
            $query->where('staff_id', $staff_id);
        }

        if (isset($filter['zone_id']) && $zone_id = $filter['zone_id']) {
            $query->where('zone_id', $zone_id);
        }

        if (isset($filter['commissionable_type']) && $commissionable_type = $filter['commissionable_type']) {
            $query->where('commissionable_type', $commissionable_type);
        }

        if (isset($filter['commissionable_id']) && $commissionable_id = $filter['commissionable_id']) {
            $query->where(function ($qr) use ($commissionable_id) {
                $qr->whereHas('deduction', function ($deduction_qr) use ($commissionable_id) {
                    $deduction_qr->where('description', $commissionable_id);
                })
                    ->orWhereHas('pickup', function ($pickup_qr) use ($commissionable_id) {
                        $pickup_qr->where('pickup_invoice', $commissionable_id);
                    })
                    ->orWhereHas('deli_sheet', function ($deli_sheet_qr) use ($commissionable_id) {
                        $deli_sheet_qr->where('delisheet_invoice', $commissionable_id);
                    })
                    ->orWhereHas('waybill', function ($waybill_qr) use ($commissionable_id) {
                        $waybill_qr->where('waybill_invoice', $commissionable_id);
                    })
                    ->orWhereHas('return_sheet', function ($return_sheet_qr) use ($commissionable_id) {
                        $return_sheet_qr->where('return_sheet_invoice', $commissionable_id);
                    })
                    ->orWhereHas('journal', function ($journal_qr) use ($commissionable_id) {
                        $journal_qr->where('journal_no', $commissionable_id);
                    });
            });
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
    }

    public function scopeOrder($query, $order)
    {
        $sortBy = isset($order['sortBy']) ? $order['sortBy'] : 'id';
        $orderBy = isset($order['orderBy']) ? $order['orderBy'] : 'desc';

        $query->orderBy($sortBy, $orderBy);
    }


    /**
     * Relations
     */
    public function staff()
    {
        return $this->belongsTo(Staff::class)->withTrashed();
    }

    public function zone()
    {
        return $this->belongsTo(Zone::class)->withTrashed();
    }

    public function commissionable()
    {
        return $this->morphTo()->withTrashed();
    }

    public function deli_sheet()
    {
        return BelongsToMorph::build($this, DeliSheet::class, 'commissionable');
    }

    public function pickup()
    {
        return BelongsToMorph::build($this, Pickup::class, 'commissionable');
    }

    public function waybill()
    {
        return BelongsToMorph::build($this, Waybill::class, 'commissionable');
    }

    public function return_sheet()
    {
        return BelongsToMorph::build($this, ReturnSheet::class, 'commissionable');
    }
}
