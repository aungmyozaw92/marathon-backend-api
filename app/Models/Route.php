<?php

namespace App\Models;

use App\Models\City;
use App\Models\DoorToDoor;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Route extends Model
{
    use SoftDeletes;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'routes';

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
     * Attributes that should be mass-assignable.
     *
     * @var array
     */
    protected $fillable = ['origin_id', 'destination_id', 'travel_day', 'route_name', 'created_by', 'updated_by', 'deleted_by'];

    public function city()
    {
        return $this->belongsTo(City::class)->withTrashed();
    }
    public function origin_city()
    {
        return $this->belongsTo(City::class, 'origin_id')->withTrashed();
    }
    public function destination_city()
    {
        return $this->belongsTo(City::class, 'destination_id')->withTrashed();
    }

    public function door_to_doors()
    {
        return $this->hasMany(DoorToDoor::class);
    }
}
