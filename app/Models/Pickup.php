<?php

namespace App\Models;

use App\Models\City;
use App\Models\Agent;
use App\Models\Staff;
use App\Models\Voucher;
use App\Models\Customer;
use App\Models\Merchant;
use App\Models\PickupHistory;
use App\Models\PointLog;
use App\Models\BelongsToMorph;
use App\Models\MerchantAssociate;
use App\Models\CommissionLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Pickup extends Model
{
    use SoftDeletes;

    // protected static function boot()
    // {
    //     parent::boot();
    //     static::creating(function($pickup) {
    //         $pickup->pickup_invoice = 'P' . str_pad($pickup->qty, 6, '0', STR_PAD_LEFT);
    //     });
    // }

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'pickups';

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
    protected $dates = ['deleted_at', 'pickup_date', 'requested_date'];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'is_closed' => 'boolean',
        'is_paid' => 'boolean',
        'is_pickuped' => 'boolean',
        'total_amount_to_collect' => 'integer',
        'pickup_fee' => 'integer',
        'total_delivery_amount' => 'integer',
        'is_came_from_mobile' => 'boolean',
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
        'city_id', 'sender_type', 'sender_id', 'sender_associate_id', 'sender', 'sender_phone', 'sender_address',
        'pickup_invoice', 'qty', 'total_delivery_amount', 'total_amount_to_collect', 'note',
        'pickup_fee', 'opened_by', 'is_closed', 'is_paid', 'is_pickuped', 'pickup_date',
        'created_by_id', 'created_by_type', 'assigned_by_id', 'assigned_by_type', 'pickuped_by_id', 'pickuped_by_type',
        'updated_by', 'deleted_by', 'is_called', 'requested_date', 'is_came_from_mobile', 'actby_mobile', 'platform',
        'courier_type_id', 'is_commissionable', 'is_pointable','payment_receive_date','payment_receive_by_id',
        'payment_receive_by_type'
    ];

    /**
     * Accessors
     */
    // public function getTotalAmountToCollectAttribute($value)
    // {
    //     return number_format($value);
    // }

    // public function getPickupFeeAttribute($value)
    // {
    //     return number_format($value);
    // }

    // public function getTotalDeliveryAmountAttribute($value)
    // {
    //     return number_format($value);
    // }

    public function getQtyAttribute()
    {
        return $this->vouchers()->count();
    }

    /**
     * Mutators
     */
    public function setPickupInvoiceAttribute($value)
    {
        // $value = isset($value) ? $value : $this->qty;
        $this->attributes['pickup_invoice'] = 'PN' . str_pad($value, 6, '0', STR_PAD_LEFT);
    }

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

        if (isset($filter['sender_name']) && $sender_name = $filter['sender_name']) {
            $query->where(function ($q) use ($sender_name) {
                $q->whereHas('customer', function ($qr) use ($sender_name) {
                    $qr->where('name', 'ILIKE', "%{$sender_name}%");
                });
            });
        }

        if (isset($filter['sender_phone']) && $sender_phone = $filter['sender_phone']) {
            $query->where(function ($q) use ($sender_phone) {
                $q->whereHas('customer', function ($qr) use ($sender_phone) {
                    $qr->where('phone', 'ILIKE', "%{$sender_phone}%");
                });
            });
        }

        if (isset($filter['sender_address']) && $sender_address = $filter['sender_address']) {
            $query->where(function ($q) use ($sender_address) {
                $q->whereHas('customer', function ($qr) use ($sender_address) {
                    $qr->where('address', 'ILIKE', "%{$sender_address}%");
                });
            });
        }

        if (isset($filter['opened_by']) && $opened_by = $filter['opened_by']) {
            $query->where('opened_by', $opened_by);
        }

        if (isset($filter['note']) && $note = $filter['note']) {
            $query->where('note', 'ILIKE', "%{$note}%");
        }

        if (isset($filter['pickuped_by']) && $pickuped_by = $filter['pickuped_by']) {
            $query->whereNull('pickuped_by_id')
                    ->where('is_pickuped', 0);
        }

        if (isset($filter['pickuped_by_id']) && $pickuped_by_id = $filter['pickuped_by_id']) {
            $query->where('pickuped_by_id', $pickuped_by_id);
        }

        if (isset($filter['pickuped_by_type']) && $pickuped_by_type = $filter['pickuped_by_type']) {
            $query->where('pickuped_by_type', $pickuped_by_type);
        }

        if (isset($filter['is_closed']) && $is_closed = $filter['is_closed']) {
            $query->where('is_closed', $is_closed);
        }

        if (isset($filter['is_paid']) && $is_paid = $filter['is_paid']) {
            $query->where('is_paid', $is_paid);
        }

        if (isset($filter['search']) && $search = $filter['search']) {
            $query->where('note', 'ILIKE', "%{$search}%")
                ->orWhere('pickup_invoice', 'ILIKE', "%{$search}%")
                ->orWhere('opened_by', 'ILIKE', "%{$search}%")
                ->orWhereHas('customer', function ($qr) use ($search) {
                    $qr->where('name', 'ILIKE', "%{$search}%")
                        ->orWhere('phone', 'ILIKE', "%{$search}%")
                        ->orWhere('address', 'ILIKE', "%{$search}%");
                });
        }

        if (isset($filter['date']) && $date = $filter['date']) {
            $query->whereDate('created_at', $date);
        }

        if (isset($filter['created_at']) && $created_at = $filter['created_at']) {
            $query->whereDate('created_at', $created_at);
        }

        if (isset($filter['requested_date']) && $requested_date = $filter['requested_date']) {
            $query->whereDate('requested_date', $requested_date);
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

        if (isset($filter['pickup_start_date']) && $pickup_start_date = $filter['pickup_start_date']) {
            if (isset($filter['pickup_end_date']) && $pickup_end_date = $filter['pickup_end_date']) {
                ($pickup_start_date == $pickup_end_date)
                    ? $query->whereDate('pickup_date', $pickup_start_date)
                    : $query->whereBetween('pickup_date', [$pickup_start_date, \Carbon\Carbon::parse($pickup_end_date)->addDays(1)]);
            } else {
                $query->whereDate('pickup_date', $pickup_start_date);
            }
        }

        if (isset($filter['merchant_id']) && $merchant_id = $filter['merchant_id']) {
            $query->whereHas('merchant', function ($qr) use ($merchant_id) {
                $qr->where('id', $merchant_id);
            });
        }

        if (isset($filter['pickup_invoice']) && $pickup_invoice = $filter['pickup_invoice']) {
            $query->where('pickup_invoice', 'ILIKE', "%{$pickup_invoice}%");
        }

        if (isset($filter['is_pickuped']) && $is_pickuped = $filter['is_pickuped']) {
            $query->where('is_pickuped', $is_pickuped);
        }

        if (isset($filter['voucher_count'])) {
            $voucher_count = $filter['voucher_count'];
            if ($voucher_count === 0) {
                $query->doesntHave('vouchers');
            } else {
                $query->has('vouchers', $voucher_count);
            }
        }

        if (isset($filter['created_by_type']) && $created_by_type = $filter['created_by_type']) {
            $query->where('created_by_type', $created_by_type);
        }


        if (isset($filter['sender_type']) && $sender_type = $filter['sender_type']) {
            $query->where('sender_type', $sender_type);
        }


        // if(isset($filter['term']) && $term = $filter['term'])
        // {
        //     $query->where(function($q) use ($term)
        //     {
        //         $q->whereHas('author', function($qr) use ($term) {
        //             $qr->where('name', 'ILIKE', "%{$term}%");
        //         });
        //         $q->orWhereHas('category', function($qr) use ($term) {
        //             $qr->where('title', 'ILIKE', "%{$term}%");
        //         });
        //         $q->orWhere('title', 'ILIKE', "%{$term}%");
        //         $q->orWhere('excerpt', 'ILIKE', "%{$term}%");
        //     });
        // }
    }

    public function scopeGetMerchantPickups($query)
    {
        $query->where(function ($q) {
			$q->where('created_by_id', auth()->user()->id)
				->where('created_by_type', 'Merchant');
			});
    }
    

    public function scopeOperationFilter($query, $filter)
    {
        if (isset($filter['merchant_id']) && $merchant_id = $filter['merchant_id']) {
            $query->where('sender_id', $merchant_id)->where('sender_type', 'Merchant');
        }

        if (isset($filter['delivery_id']) && $delivery_id = $filter['delivery_id']) {
            $query->where('opened_by', $delivery_id);
        }
        // $query->where('is_closed', 0);
    }

    /**
     * Relations
     */
    public function opened_by_staff()
    {
        return $this->belongsTo(Staff::class, 'opened_by')->withTrashed();
    }

    // public function created_by_staff()
    // {
    //     return $this->belongsTo(Staff::class, 'created_by_id')->withTrashed();
    // }

    /**
     * Get all of the owning pickuptable models.
     */
    public function sender()
    {
        return $this->morphTo()->with(['city', 'zone'])->withTrashed();
    }

    public function merchant()
    {
        return BelongsToMorph::build($this, Merchant::class, 'sender');
    }

    public function customer()
    {
        return BelongsToMorph::build($this, Customer::class, 'sender');
    }

    public function sender_associate()
    {
        return $this->belongsTo(MerchantAssociate::class, 'sender_associate_id')->with(['city', 'zone'])->withTrashed();
    }

    public function vouchers()
    {
        return $this->hasMany(Voucher::class)->orderBy('id', 'decs');
    }

    public function prepaid_vouchers()
    {
        return $this->hasMany(Voucher::class)->whereIn('payment_type_id', [9, 10])->orderBy('id', 'decs');
    }

    public function pickup_histories()
    {
        return $this->hasMany(PickupHistory::class)->orderBy('id', 'desc');
    }

    public function journals()
    {
        return $this->morphMany('App\Models\Journal', 'resourceable');
    }

    /*
    Created Relation with Merchant and Staff
    */
    public function created_by()
    {
        return $this->morphTo()->withTrashed();
    }

    public function created_by_merchant()
    {
        return BelongsToMorph::build($this, Merchant::class, 'created_by');
    }

    public function created_by_staff()
    {
        return BelongsToMorph::build($this, Staff::class, 'created_by');
    }

    public function city()
    {
        return $this->belongsTo(City::class)->withTrashed();
    }
    /*
    updated Relation with Merchant and Staff
    */

    public function updated_by()
    {
        return $this->morphTo()->withTrashed();
    }

    public function updated_by_merchant()
    {
        return BelongsToMorph::build($this, Merchant::class, 'updated_by');
    }

    public function updated_by_staff()
    {
        return BelongsToMorph::build($this, Staff::class, 'updated_by');
    }
    /*
    Start deleted Relation with Merchant and Staff
    */
    public function deleted_by()
    {
        return $this->morphTo()->withTrashed();
    }

    public function deleted_by_merchant()
    {
        return BelongsToMorph::build($this, Merchant::class, 'deleted_by');
    }

    public function deleted_by_staff()
    {
        return BelongsToMorph::build($this, Staff::class, 'deleted_by');
    }

    /*
    End deleted Relation with Merchant and Staff
    */
    /*
    Start assigned Relation with Merchant and Staff , Agent
    */
    public function assigned_by()
    {
        return $this->morphTo()->withTrashed();
    }

    public function assigned_by_merchant()
    {
        return BelongsToMorph::build($this, Merchant::class, 'assigned_by');
    }

    public function assigned_by_staff()
    {
        return BelongsToMorph::build($this, Staff::class, 'assigned_by');
    }

    public function assigned_by_agent()
    {
        return BelongsToMorph::build($this, Agent::class, 'assigned_by');
    }

    /*
    End assigned Relation with Merchant and Staff , Agent
    */
    /*
    Start assigned Relation with Merchant and Staff , Agent
    */
    public function pickuped_by()
    {
        return $this->morphTo()->withTrashed();
    }

    public function pickuped_by_staff()
    {
        return BelongsToMorph::build($this, Staff::class, 'pickuped_by');
    }

    public function pickuped_by_agent()
    {
        return BelongsToMorph::build($this, Agent::class, 'pickuped_by');
    }

    public function payment_receive_by()
    {
        return $this->morphTo()->withTrash();
    }

    public function payment_receive_by_staff()
    {
        return BelongsToMorph::build($this, Staff::class, 'payment_receive_by');
    }

    public function payment_receive_by_agent()
    {
        return BelongsToMorph::build($this, Agent::class, 'payment_receive_by');
    }

    public function attachments()
    {
        return $this->morphMany('App\Models\Attachment', 'resourceable', 'resource_type', 'resource_id');
    }

    public function pickupVoucherFire($logStatus, $voucherId)
    {
        $data = array(
            'requests' => [
                'voucher_id' => $voucherId,
                'previous' => $this->pickup_invoice,
                'logStatus' => $logStatus
            ]
        );
        \Event::fire('pickupLogVoucher', array($data));
    }

    /*
    End assigned Relation with Merchant and Staff , Agent
    */
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

    
}
