<?php

namespace App\Models;

use App\Models\Gate;
use App\Models\Route;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BusDropOff extends Model
{
    use SoftDeletes;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'bus_drop_offs';

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
        'base_rate' => 'integer',
        'agent_base_rate' => 'integer',
        'salt' => 'integer'
    ];

    /**
     * Attributes that should be mass-assignable.
     *
     * @var array
     */
    protected $fillable = [
        'route_id', 'gate_id', 'global_scale_id', 'base_rate', 'agent_base_rate', 'salt',  'created_by',
        'updated_by', 'deleted_by'
    ];

    /**
     * Accessors
     */
    // public function getBaseRateAttribute($value)
    // {
    //     return number_format($value);
    // }


    // public function getAgentBaseRateAttribute($value)
    // {
    //     return number_format($value);
    // }

    // public function getSaltAttribute($value)
    // {
    //     return number_format($value);
    // }

    /**
     * scopes
     */
    public function scopeFilter($query, $filter)
    {
        if (isset($filter['search']) && $search = $filter['search']) {
            $query->where('base_rate', 'ILIKE', "%{$search}%")
                ->orWhere('agent_base_rate', 'ILIKE', "%{$search}%")
                ->orWhere('salt', 'ILIKE', "%{$search}%")
                ->orWhere(function ($q) use ($search) {
                    $q->whereHas('route', function ($qr) use ($search) {
                        $qr->where('route_name', 'ILIKE', "%{$search}%");
                    })
                     ->orWhereHas('gate', function ($qr) use ($search) {
                         $qr->where('name', 'ILIKE', "%{$search}%");
                     })
                     ->orWhereHas('global_scale', function ($qr) use ($search) {
                         $qr->where('description', 'ILIKE', "%{$search}%")
                            ->orWhere('description_mm', 'ILIKE', "%{$search}%");
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

    public function gate()
    {
        return $this->belongsTo(Gate::class);
    }
    public function route()
    {
        return $this->belongsTo(Route::class);
    }
    public function global_scale()
    {
        return $this->belongsTo(GlobalScale::class);
    }
}
