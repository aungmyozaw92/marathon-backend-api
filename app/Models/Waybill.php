<?php

namespace App\Models;

use App\Models\Agent;
use App\Models\Staff;
use App\Models\Voucher;
use App\Models\BusStation;
use App\Models\PointLog;
use App\Models\CommissionLog;
use Illuminate\Support\Arr;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Waybill extends Model
{
    use SoftDeletes;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'waybills';

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
    protected $dates = ['deleted_at', 'received_date'];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'is_closed' => 'boolean',
        'is_paid' => 'boolean',
        'is_received' => 'boolean',
        'is_delivered' => 'boolean',
        'is_scanned' => 'boolean',
        'is_confirm' => 'boolean',
        'actual_bus_fee' => 'integer',
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
        'waybill_invoice', 'voucher_id', 'from_city_id', 'to_city_id', 'delivery_id', 'staff_id', 'note', 'created_by',
        'updated_by', 'deleted_by', 'from_bus_station_id', 'to_bus_station_id', 'gate_id', 'city_id', 'is_closed',
        'is_paid', 'is_received', 'is_delivered', 'is_scanned', 'is_confirm', 'is_came_from_mobile', 'actby_mobile',
        'courier_type_id', 'is_commissionable', 'is_pointable','from_agent_id', 'to_agent_id'
    ];

    /**
     * Accessors
     */
    // public function getActualBusFeeAttribute($value)
    // {
    //     return number_format($value);
    // }

    public function getQtyAttribute()
    {
        return $this->vouchers()->count();
    }

    /**
     *  Mutators
     */
    public function setWaybillInvoiceAttribute($value)
    {
        $this->attributes['waybill_invoice'] = 'WN' . str_pad($value, 6, '0', STR_PAD_LEFT);
    }

    /**
     * scopes
     */
    public function scopeFilter($query, $filter)
    {
        if (isset($filter['date']) && $date = $filter['date']) {
            $query->whereDate('created_at', $date);
        }

        if (isset($filter['created_at']) && $created_at = $filter['created_at']) {
            $query->whereDate('created_at', $created_at);
        }

        if (isset($filter['waybill_invoice']) && $waybill_invoice = $filter['waybill_invoice']) {
            $query->where('waybill_invoice', 'ILIKE', "%{$waybill_invoice}%");
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

        if (isset($filter['delivery_id']) && $delivery_id = $filter['delivery_id']) {
            $query->where('delivery_id', $delivery_id);
        }

        if (isset($filter['to_city_id']) && $to_city_id = $filter['to_city_id']) {
            $query->where('to_city_id', $to_city_id);
        }

        if (isset($filter['from_city_id']) && $from_city_id = $filter['from_city_id']) {
            $query->where('from_city_id', $from_city_id);
        }

        if (isset($filter['is_closed']) && $is_closed = $filter['is_closed']) {
            $query->where('is_closed', $is_closed);
        }

        if (isset($filter['is_paid']) && $is_paid = $filter['is_paid']) {
            $query->where('is_paid', $is_paid);
        }
        
        if (isset($filter['is_received'])) {
            $query->where('is_received', $filter['is_received']);
        }
        if (isset($filter['from_agent_id']) && $from_agent_id = $filter['from_agent_id']) {
            $query->where('from_agent_id', $from_agent_id);
        }
        if (isset($filter['to_agent_id']) && $to_agent_id = $filter['to_agent_id']) {
            $query->where('to_agent_id', $to_agent_id);
        }
    }

    public function scopeReceivedFilter($query, $filter)
    {
        // close line for multiple agent
        // $query->where('to_city_id', auth()->user()->city_id);
        $query->where('to_agent_id', auth()->user()->id);
        if (isset($filter['is_received']) && $is_received = $filter['is_received']) {
            $query->where('is_received', 1);
        } else {
            $query->where('is_received', 0);
        }
        $query->orderBy('updated_at', 'DESC');
    }

    /**
     * Relations
     */
    public function delivery()
    {
        return $this->belongsTo(Staff::class, 'delivery_id')->withTrashed();
    }

    public function staff()
    {
        return $this->belongsTo(Staff::class)->withTrashed();
    }

    public function from_bus_station()
    {
        return $this->belongsTo(BusStation::class, 'from_bus_station_id')->withTrashed();
    }

    public function to_bus_station()
    {
        return $this->belongsTo(BusStation::class, 'to_bus_station_id')->withTrashed();
    }

    public function city()
    {
        return $this->belongsTo(City::class)->withTrashed();
    }

    public function from_city()
    {
        return $this->belongsTo(City::class, 'from_city_id')->withTrashed();
    }

    public function to_city()
    {
        return $this->belongsTo(City::class, 'to_city_id')->withTrashed();
    }

    public function gate()
    {
        return $this->belongsTo(Gate::class)->withTrashed();
    }

    public function created_by_staff()
    {
        return $this->belongsTo(Staff::class, 'created_by')->withTrashed();
    }


    public function vouchers()
    {
        return $this->belongsToMany(Voucher::class, 'waybill_vouchers', 'waybill_id', 'voucher_id')
            // ->using(DeliSheetVoucher::class)
            ->as('waybill_vouchers')
            ->withTimestamps()
            ->withPivot([
                'note',
                'priority',
                'status',
                'created_by',
                'updated_by',
                'deleted_by'
            ]);
        // ->opened();
    }

    public function attachments()
    {
        return $this->morphMany('App\Models\Attachment', 'resourceable', 'resource_type', 'resource_id');
    }
    public function waybillVoucherFire($voucherInvoice, $logStatus)
    {
        $data = array(
            'requests' => [
                'waybill_id' => $this->id,
                'previous' => $voucherInvoice,
                'logStatus' => $logStatus
            ]
        );
        \Event::fire('waybillForVoucher', array($data));
    }
    public function waybill_histories()
    {
        return $this->hasMany(WaybillHistory::class)->orderBy('id', 'desc');
    }

    public function receivable()
    {
        return $this->morphTo()->withTrashed();
    }

    public function agent()
    {
        return BelongsToMorph::build($this, Agent::class, 'receivable');
    }

    public function received_staff()
    {
        return BelongsToMorph::build($this, Staff::class, 'receivable');
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

    public function from_agent()
    {
        return $this->belongsTo(Agent::class, 'from_agent_id');
    }

    public function to_agent()
    {
        return $this->belongsTo(Agent::class, 'to_agent_id');
    }
}
