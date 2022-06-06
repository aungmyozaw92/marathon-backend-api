<?php

namespace App\Models;

use App\Models\Zone;
use App\Models\BusStation;
use App\Models\BusSheetVoucher;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BusSheet extends Model
{
    use SoftDeletes;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'bus_sheets';

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
        'is_closed' => 'boolean',
        'is_paid'   => 'boolean',
    ];

    /**
     * Attributes that should be mass-assignable.
     *
     * @var array
     */
    protected $fillable = [
        'bus_sheet_invoice', 'qty', 'from_bus_station_id', 'delivery_id', 'staff_id', 'is_closed',
        'created_by', 'updated_by', 'deleted_by', 'is_paid', 'note'
    ];

    /**
     * Accessors & Mutators
     */
    public function setBussheetInvoiceAttribute($value)
    {
        $this->attributes['bus_sheet_invoice'] = 'B' . str_pad($value, 6, '0', STR_PAD_LEFT);
    }

    /**
     * scopes
     */
    public function scopeFilter($query, $filter)
    {
        if (isset($filter['date']) && $date = $filter['date']) {
            $query->whereDate('created_at', $date);
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

        if (isset($filter['delivery_id']) && $delivery_id = $filter['delivery_id']) {
            $query->where('delivery_id', $delivery_id);
        }
    }

    /**
     * Relations
     */
    public function from_bus_station()
    {
        return $this->belongsTo(BusStation::class)->withTrashed();
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
        return $this->belongsToMany(Voucher::class, 'bus_sheet_vouchers', 'bus_sheet_id', 'voucher_id')
            // ->using(DeliSheetVoucher::class)
            ->as('bus_sheet_vouchers')
            ->withTimestamps()
            ->withPivot([
                'actual_bus_fee',
                'note',
                'priority',
                // 'payment_status_id',
                'delivery_status_id',
                'is_return',
                'is_paid',
                'created_by',
                'updated_by',
                'deleted_by'
            ]);
        // ->opened();
    }
    public function bus_sheet_vouchers()
    {
        return $this->hasMany(BusSheetVoucher::class, 'bus_sheet_id')->withTrashed();
    }
}
