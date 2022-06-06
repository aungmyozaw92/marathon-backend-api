<?php

namespace App\Models;

use App\Models\BusDropOff;
use App\Models\DoorToDoor;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class GlobalScale extends Model
{
    use SoftDeletes;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'global_scales';

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
        // 'cbm' => 'integer',
        // 'support_weight' => 'integer',
        // 'max_weight' => 'integer'
    ];

    /**
     * Attributes that should be mass-assignable.
     *
     * @var array
     */
    protected $fillable = [
        'cbm', 'support_weight', 'max_weight', 'description','description_mm', 'created_by', 'updated_by','deleted_by'
    ];
    
    public function vouchers()
    {
        return $this->belongsToMany('App\Models\Voucher', 'percels', 'global_scale_id', 'voucher_id');
    }

    public function door_to_doors()
    {
        return $this->hasMany(DoorToDoor::class);
    }

    public function bus_drop_offs()
    {
        return $this->hasMany(BusDropOff::class);
    }
}
