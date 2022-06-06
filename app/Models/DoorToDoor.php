<?php

namespace App\Models;

use App\Models\Route;
use App\Models\GlobalScale;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DoorToDoor extends Model
{
    use SoftDeletes;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'door_to_doors';

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
        // 'base_rate' => 'integer',
        // 'agent_base_rate' => 'integer',
        // 'salt' => 'integer',
        // 'agent_salt' => 'integer'
    ];

    /**
     * Attributes that should be mass-assignable.
     *
     * @var array
     */
    protected $fillable = [
        'route_id', 'global_scale_id', 'base_rate', 'agent_base_rate', 'salt', 'agent_salt', 'created_by', 'updated_by', 'deleted_by'
    ];

    /**
     * scopes
     */
    // public function scopeWebsite($query)
    // {
    //     $query->where(function ($q) {
    //         $q->whereHas('route', function ($qr) {
    //             // $qr->whereIn('origin_id', [35, 49, 64]);
    //             $qr->where('origin_id', '=', 64)->where('destination_id', '<>', 64)->whereHas('destination_city', function ($qd) {
    //                 $qd->where('is_available_d2d', '=', true);
    //             });
    //         });
    //     });
    // }
    public function scopeFilter($query, $filter)
    {
        if (isset($filter['search']) && $search = $filter['search']) {
            $query->where('base_rate', 'ILIKE', "%{$search}%")
                ->orWhere('agent_base_rate', 'ILIKE', "%{$search}%")
                ->orWhere('salt', 'ILIKE', "%{$search}%")
                ->orWhere('agent_salt', 'ILIKE', "%{$search}%")
                ->orWhere(function ($q) use ($search) {
                    $q->whereHas('route', function ($qr) use ($search) {
                        $qr->where('route_name', 'ILIKE', "%{$search}%");
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

    public function route()
    {
        return $this->belongsTo(Route::class)->withTrashed();
    }
    public function global_scale()
    {
        return $this->belongsTo(GlobalScale::class)->withTrashed();
    }
}
