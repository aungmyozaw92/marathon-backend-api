<?php

namespace App\Models;

use App\Models\Staff;
use App\Models\Pickup;
use App\Models\Journal;
use App\Models\Waybill;
use App\Models\Deduction;
use App\Models\DeliSheet;
use App\Models\HeroBadge;
use App\Models\ReturnSheet;
use App\Models\BelongsToMorph;
use Illuminate\Database\Eloquent\Model;

class PointLog extends Model
{
    //
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'point_logs';

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
        'staff_id', 'points', 'status', 'resourceable_type', 'resourceable_id', 'hero_badge_id', 'note', 'created_by', 'updated_by'
    ];

    /**
     * scopes
     */
    public function scopeFilter($query, $filter)
    {
        if (isset($filter['staff_id']) && $staff_id = $filter['staff_id']) {
            $query->where('staff_id', $staff_id);
        }

        if (isset($filter['status']) && $status = $filter['status']) {
            $query->where('status', $status);
        }

        if (isset($filter['resourceable_type']) && $resourceable_type = $filter['resourceable_type']) {
            $query->where('resourceable_type', $resourceable_type);
        }

        if (isset($filter['resourceable_id']) && $resourceable_id = $filter['resourceable_id']) {
            $query->where(function ($qr) use ($resourceable_id) {
                $qr->whereHas('deduction', function ($deduction_qr) use ($resourceable_id) {
                    $deduction_qr->where('description', $resourceable_id);
                })
                    ->orWhereHas('pickup', function ($pickup_qr) use ($resourceable_id) {
                        $pickup_qr->where('pickup_invoice', $resourceable_id);
                    })
                    ->orWhereHas('deli_sheet', function ($deli_sheet_qr) use ($resourceable_id) {
                        $deli_sheet_qr->where('delisheet_invoice', $resourceable_id);
                    })
                    ->orWhereHas('waybill', function ($waybill_qr) use ($resourceable_id) {
                        $waybill_qr->where('waybill_invoice', $resourceable_id);
                    })
                    ->orWhereHas('return_sheet', function ($return_sheet_qr) use ($resourceable_id) {
                        $return_sheet_qr->where('return_sheet_invoice', $resourceable_id);
                    })
                    ->orWhereHas('journal', function ($journal_qr) use ($resourceable_id) {
                        $journal_qr->where('journal_no', $resourceable_id);
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

    public function hero_badge()
    {
        return $this->belongsTo(HeroBadge::class)->withTrashed();
    }

    public function resourceable()
    {
        return $this->morphTo()->withTrashed();
    }

    public function deduction()
    {
        return BelongsToMorph::build($this, Deduction::class, 'resourceable');
    }

    public function deli_sheet()
    {
        return BelongsToMorph::build($this, DeliSheet::class, 'resourceable');
    }

    public function pickup()
    {
        return BelongsToMorph::build($this, Pickup::class, 'resourceable');
    }

    public function waybill()
    {
        return BelongsToMorph::build($this, Waybill::class, 'resourceable');
    }

    public function return_sheet()
    {
        return BelongsToMorph::build($this, ReturnSheet::class, 'resourceable');
    }

    public function journal()
    {
        return BelongsToMorph::build($this, Journal::class, 'resourceable');
    }

    public function attachments()
    {
        return $this->morphMany('App\Models\Attachment', 'resourceable', 'resource_type', 'resource_id');
    }
}
