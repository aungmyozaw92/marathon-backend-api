<?php

namespace App\Models;

use App\Models\Merchant;
use App\Models\MerchantAssociate;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MerchantRateCard extends Model
{
    use SoftDeletes;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'merchant_rate_cards';

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
        'normal_or_dropoff' => 'boolean',
        'extra_or_discount' => 'boolean',
        'amount' => 'integer'
    ];

    /**
     * Attributes that should be mass-assignable.
     *
     * @var array
     */

    protected $fillable = [
        'amount', 'merchant_id', 'merchant_associate_id','discount_type_id','normal_or_dropoff', 'extra_or_discount', 'platform',
        'sender_city_id', 'receiver_city_id', 'sender_zone_id', 'receiver_zone_id', 'from_bus_station_id',
        'to_bus_station_id', 'start_date', 'end_date', 'note','incremental_weight','incremental_weight',
        'from_weight','to_weight', 'created_by', 'updated_by', 'deleted_by'
    ];

    /**
     * Accessors
     */
    // public function getAmountAttribute($value)
    // {
    //     return number_format($value);
    // }

    /**
     * scopes
     */
    public function scopeFilter($query, $filter)
    {
        $sortBy = isset($order['sortBy']) ? $order['sortBy'] : 'id';
        $orderBy = isset($order['orderBy']) ? $order['orderBy'] : 'desc';

        if (isset($filter['merchant_id']) && $merchant_id = $filter['merchant_id']) {
            $query->where('merchant_id', $merchant_id);
        }
        $query->orderBy($sortBy, $orderBy);
    }

    public function merchant()
    {
        return $this->belongsTo(Merchant::class)->withTrashed();
    }

    public function merchant_associate()
    {
        return $this->belongsTo(MerchantAssociate::class)->withTrashed();
    }

    public function discount_type()
    {
        return $this->belongsTo(DiscountType::class)->withTrashed();
    }

    public function sender_city()
    {
        return $this->belongsTo(City::class, 'sender_city_id');
    }

    public function receiver_city()
    {
        return $this->belongsTo(City::class, 'receiver_city_id');
    }

    public function sender_zone()
    {
        return $this->belongsTo(Zone::class, 'sender_zone_id');
    }
    public function receiver_zone()
    {
        return $this->belongsTo(Zone::class, 'receiver_zone_id');
    }

    public function getCheckZoneNullAttribute()
    {
        return $this->sender_zone_id == null || $this->receiver_zone_id == null;
    }

    public function scopeDropOff($query)
    {
        $query->where('normal_or_dropoff', true);
    }

    public function scopeDropOffRateCard($query, $bus_stations)
    {
        $query->where([['from_bus_station_id', 0], ['to_bus_station_id', 0]]);
        $query->orWhere([['from_bus_station_id', 0 ],['to_bus_station_id', $bus_stations['to_bus_station_id']]]);
        $query->orWhere([['to_bus_station_id', 0 ],['from_bus_station_id', $bus_stations['from_bus_station_id']]]);
        $query->orWhere([['from_bus_station_id', $bus_stations['from_bus_station_id'] ],['to_bus_station_id', $bus_stations['to_bus_station_id']]]);
    }

    public function scopeDropOffGlobalRateCard($query, $bus_stations)
    {
        $query->where([['merchant_id', 0],['from_bus_station_id', 0], ['to_bus_station_id', 0]]);
        $query->orWhere([['merchant_id', 0],['from_bus_station_id', 0 ],['to_bus_station_id', $bus_stations['to_bus_station_id']]]);
        $query->orWhere([['merchant_id', 0],['to_bus_station_id', 0 ],['from_bus_station_id', $bus_stations['from_bus_station_id']]]);
        $query->orWhere([['merchant_id', 0],['from_bus_station_id', $bus_stations['from_bus_station_id'] ],['to_bus_station_id', $bus_stations['to_bus_station_id']]]);
    }

    public function scopeNormalVoucher($query)
    {
        $query->where('normal_or_dropoff', false);
    }

    public function scopeNormalVoucherCityRateCard($query, $locations)
    { 

        if (isset($locations['qty_rate_card']) && isset($locations['qty_rate_card'])) {
            $query->whereNotNull('min_threshold')
                 ->whereNotNull('qty_status');
        }
        $query->where([
            ['receiver_city_id', $locations['receiver_city_id'] ],
            ['sender_city_id', $locations['sender_city_id']]
        ]);
        $query->orWhere([
            ['receiver_city_id', 0 ],
            ['sender_city_id', $locations['sender_city_id']]
        ]);
        $query->orWhere([
            ['sender_city_id', 0 ],
            ['receiver_city_id', $locations['receiver_city_id']]
        ]);
            
    }

    public function scopeNormalVoucherZoneRateCard($query, $locations)
    {
        $query->where([
            ['receiver_zone_id', 0 ],
            ['sender_zone_id', $locations['sender_zone_id']]
        ]);
        $query->orWhere([
            ['sender_zone_id', 0 ],
            ['receiver_zone_id', $locations['receiver_zone_id']]
        ]);
        $query->orWhere([
            ['receiver_zone_id', $locations['receiver_zone_id'] ],
            ['sender_zone_id', $locations['sender_zone_id']]
        ]);
        $query->orWhere([
            ['receiver_zone_id', 0],
            ['sender_zone_id', 0]
        ]);
    }
    public function scopeAllCityRateCard($query, $locations)
    {
        if (isset($locations['qty_rate_card']) && isset($locations['qty_rate_card'])) {
            $query->whereNotNull('min_threshold')
                 ->whereNotNull('qty_status');
        }
        $query->where([
            ['receiver_city_id', 0],
            ['sender_city_id', 0]
        ]);
    }
    public function scopeAllZoneRateCard($query, $locations)
    {
        $query->where([
            ['receiver_zone_id', 0],
            ['sender_zone_id', 0]
        ]);
    }

    public function scopeWeightRateCard($query, $locations)
    {
        $query->where(function ($q){
                $q->whereNull('from_weight')
                ->whereNull('to_weight');
            })->orWhere(function ($q) use ($locations){
                $q->where('from_weight','<=', $locations['weight'])
                    ->where('to_weight','>=', $locations['weight']);
            });
    }

    public function scopeCustomerRateCard($query, $locations)
    {
       $query->where([
            ['merchant_id', 0],
            ['sender_city_id', $locations['sender_city_id']],
            ['receiver_city_id', $locations['receiver_city_id'] ],
            ['receiver_zone_id', $locations['receiver_zone_id']]
            
        ])->where(function ($q) {
            $q->where('platform', 'All')
              ->orWhere('platform', 'Web');
        });
    }

    
    
}
