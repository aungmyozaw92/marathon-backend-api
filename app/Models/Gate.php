<?php

namespace App\Models;

use App\Models\Bus;
use App\Models\BusStation;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Gate extends Model
{
    use SoftDeletes;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'gates';

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
    protected $dates = [ 'deleted_at' ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        // 'gate_debit' => 'boolean',
        // 'gate_rate' => 'integer'
    ];

    /**
     * Attributes that should be mass-assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'gate_rate','gate_debit', 'bus_id', 'bus_station_id', 'created_by', 'updated_by', 'deleted_by'
    ];

    protected $morphClass = 'MorphGate';

    /**
     * scopes
     */
    public function scopeFilter($query, $filter)
    {
        if (isset($filter['day']) && $day = $filter['day']) {
            $query->whereRaw('day(created_at) = ?', [$day]);
        }

        if (isset($filter['month']) && $month = $filter['month']) {
            $query->whereRaw('month(created_at) = ?', [Carbon::parse($month)->month]);
        }

        if (isset($filter['year']) && $year = $filter['year']) {
            $query->whereRaw('year(created_at) = ?', [$year]);
        }

        if (isset($filter['bus_station_name']) && $bus_station_name = $filter['bus_station_name']) {
            $query->where(function ($q) use ($bus_station_name) {
                $q->whereHas('bus_station', function ($qr) use ($bus_station_name) {
                    $qr->where('name', 'ILIKE', "%{$bus_station_name}%");
                });
            });
        }

        if (isset($filter['name']) && $name = $filter['name']) {
            $query->where('name', 'ILIKE', "%{$name}%");
        }

        if (isset($filter['delivery_rate']) && $delivery_rate = $filter['delivery_rate']) {
            $query->where('delivery_rate', 'ILIKE', "%{$delivery_rate}%");
        }

        if (isset($filter['search']) && $search = $filter['search']) {
            $query->where('name', 'ILIKE', "%{$search}%")
                ->orWhere('delivery_rate', 'ILIKE', "%{$search}%")
                ->orWhere(function ($q) use ($search) {
                    $q->whereHas('bus_station', function ($qr) use ($search) {
                        $qr->where('name', 'ILIKE', "%{$search}%");
                    });
                });
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
    public function account()
    {
        return $this->morphOne('App\Models\Account', 'accountable')->withTrashed();
    }

    public function bus_station()
    {
        return $this->belongsTo(BusStation::class)->withTrashed();
    }

    public function bus()
    {
        return $this->belongsTo(Bus::class)->withTrashed();
    }

    public function bus_drop_offs()
    {
        return $this->hasMany(BusDropOff::class)->withTrashed();
    }
}
