<?php

namespace App\Models;

use App\Models\City;
use App\Models\Gate;
use App\Models\Zone;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BusStation extends Model
{
    use SoftDeletes;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'bus_stations';

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
        'delivery_rate' => 'integer'
    ];

    /**
     * Attributes that should be mass-assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'lat', 'long', 'number_of_gates', 'locked_by', 'city_id', 'zone_id',
        'delivery_rate','created_by', 'updated_by', 'deleted_by'
    ];

    /**
     * Accessors
     */
    // public function getDeliveryRateAttribute($value)
    // {
    //     return number_format($value);
    // }

    public function gates()
    {
        return $this->hasMany(Gate::class);
    }

    public function city()
    {
        return $this->belongsTo(City::class)->withTrashed();
    }

    public function zone()
    {
        return $this->belongsTo(Zone::class)->withTrashed();
    }

    public function route_cities()
    {
        return $this->hasMany(Route::class, 'origin_id', 'city_id');
    }
}
