<?php

namespace App\Models;

use App\Models\City;
use App\Models\Voucher;
use App\Models\OrderItem;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
     use SoftDeletes;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'orders';

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
    protected $fillable = [
        'order_no',
        'merchant_id',
        'receiver_name',
        'receiver_phone',
        'receiver_address',
        'receiver_email',
        'sender_city_id',
        'sender_zone_id',
        'receiver_city_id',
        'receiver_zone_id',
        'payment_type_id',
        'global_scale_id',
        'remark',
        'thirdparty_invoice',
        'total_weight',
        'total_qty',
        'total_price',
        'total_delivery_amount',
        'platform',
        'status',
        'good_agent_id',
        'payment_option','payment_method','is_paid','is_receive'
    ];

    /**
     * AMutators
     */
    public function setOrderNoAttribute($value)
    {
        $this->attributes['order_no'] = 'OID' . str_pad($value, 6, '0', STR_PAD_LEFT);
    }

    public function order_items()
    {
        return $this->hasMany(OrderItem::class);
    }

     public function voucher()
    {
        return $this->hasOne(Voucher::class);
    }

    public function sender_city()
    {
        return $this->belongsTo(City::class, 'sender_city_id')->withTrashed();
    }
    public function receiver_city()
    {
        return $this->belongsTo(City::class, 'receiver_city_id')->withTrashed();
    }
    public function sender_zone()
    {
        return $this->belongsTo(Zone::class, 'sender_zone_id')->withTrashed();
    }
    public function receiver_zone()
    {
        return $this->belongsTo(Zone::class, 'receiver_zone_id')->withTrashed();
    }

    public function scopeFilter($query, $filter)
    {
        
        if (isset($filter['good_agent_id']) && $good_agent_id = $filter['good_agent_id']) {
            $query->where('good_agent_id', $good_agent_id);
        }
        if (isset($filter['receiver_name']) && $receiver_name = $filter['receiver_name']) {
            $query->where('receiver_name', 'ILIKE', "%{$receiver_name}%");
        }
        if (isset($filter['receiver_phone']) && $receiver_phone = $filter['receiver_phone']) {
            $query->where('receiver_phone', 'ILIKE', "%{$receiver_phone}%");
        }
        if (isset($filter['receiver_address']) && $receiver_address = $filter['receiver_address']) {
            $query->where('receiver_address', 'ILIKE', "%{$receiver_address}%");
        }
        if (isset($filter['remark']) && $remark = $filter['remark']) {
            $query->where('remark', 'ILIKE', "%{$remark}%");
        }
        if (isset($filter['status']) && $status = $filter['status']) {
            $query->where('status', true);
        }
        if (isset($filter['is_paid']) && $is_paid = $filter['is_paid']) {
            $query->where('is_paid', true);
        }
        if (isset($filter['is_received']) && $is_received = $filter['is_received']) {
            $query->where('is_received', true);
        }
        if (isset($filter['payment_option']) && $payment_option = $filter['payment_option']) {
            $query->where('payment_option', $payment_option);
        }
        if (isset($filter['payment_method']) && $payment_method = $filter['payment_method']) {
            $query->where('payment_method', $payment_method);
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
}
