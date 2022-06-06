<?php

namespace App\Models;

use Carbon\Carbon;
use App\Models\Agent;
use App\Models\Order;
use App\Models\Staff;
use App\Models\Parcel;
use App\Models\Pickup;
use App\Models\Journal;
use App\Models\Waybill;
use App\Models\BusSheet;
use App\Models\Customer;
use App\Models\Merchant;
use App\Models\DeliSheet;
use App\Models\Attachment;
use App\Models\PaymentType;
use App\Models\QrAssociate;
use App\Models\ReturnSheet;
use App\Models\TempJournal;
use App\Models\DiscountType;
use App\Models\PaymentStatus;
use App\Models\BelongsToMorph;
use App\Models\TrackingStatus;
use App\Models\VoucherHistory;
use App\Models\WaybillVoucher;
use App\Models\TrackingVoucher;
use App\Models\DelegateDuration;
use App\Models\DeliSheetVoucher;
use App\Models\ReturnSheetVoucher;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Voucher extends Model
{
    use SoftDeletes;
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'vouchers';

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
    protected $dates = ['deleted_at', 'postpone_date', 'delivered_date', 'transaction_date', 'end_date', 'returned_date'];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'is_closed' => 'boolean',
        'is_complete' => 'boolean',
        'is_return' => 'boolean',
        'is_picked' => 'boolean',
        'is_bus_station_dropoff' => 'boolean',
        'is_manual_return' => 'boolean',
        'bus_station' => 'boolean',
        'bus_credit' => 'boolean',
        'total_item_price' => 'integer',
        'total_delivery_amount' => 'integer',
        'total_amount_to_collect' => 'integer',
        'total_coupon_amount' => 'integer',
        'total_discount_amount' => 'integer',
        'warehousing_fee' => 'integer',
        'transaction_fee' => 'integer',
        'insurance_fee' => 'integer',
        'total_agent_fee' => 'integer',
        'deposit_amount' => 'integer',
        'bus_fee' => 'integer',
        'sender_amount_to_collect' => 'integer',
        'receiver_amount_to_collect' => 'integer',
        'return_fee' => 'integer',
        'total_bus_fee' => 'integer',
        'grand_total' => 'integer',
        'delivery_commission' => 'integer'
    ];
    /**
     * Attributes that should be mass-assignable.
     *
     * @var array
     */
    protected $fillable = [
        'sender_status', 'sender_id', 'receiver_id', 'pickup_id', 'outgoing_status',
        'voucher_invoice', 'total_item_price', 'total_delivery_amount', 'total_amount_to_collect', 'remark',
        'discount_id', 'total_coupon_amount', 'total_discount_amount', 'sender_city_id', 'sender_zone_id',
        'receiver_city_id', 'receiver_zone_id', 'warehousing_fee', 'transaction_fee', 'insurance_fee', 'total_agent_fee',
        'bus_station', 'sender_bus_station_id', 'receiver_bus_station_id', 'sender_gate_id', 'receiver_gate_id',
        'bus_credit', 'deposit_amount', 'is_closed', 'bus_fee', 'call_status_id', 'delivery_status_id',
        'store_status_id', 'caller_status', 'delivering_status', 'storing_status', 'discount_type', 'delivery_counter',
        'delegate_person', 'delegate_duration_id', 'created_by_id', 'created_by_type', 'updated_by', 'deleted_by', 'updated_by_type', 'deleted_by_type',
        'is_return', 'is_picked', 'is_bus_station_dropoff', 'is_manual_return', 'qr_associate_id', 'deli_payment_status',
        'delivered_date', 'sender_amount_to_collect', 'receiver_amount_to_collect', 'end_date', 'thirdparty_invoice',
        'delivery_commission', 'transaction_date', 'returned_date', 'pending_returning_date',
        'pending_returning_actor_type', 'pending_returning_actor_id', 'is_complete', 'seller_discount',
        'postpone_perform_date', 'postpone_actor_type', 'postpone_actor_id', 'postpone_date',
        'from_agent_id', 'to_agent_id','order_id'
    ];

    /**
     * Accessors
     */
    // public function getTotalItemPriceAttribute($value)
    // {
    //     return number_format($value);
    // }

    // public function getTotalDeliveryAmountAttribute($value)
    // {
    //     return number_format($value);
    // }

    // public function getTotalAmountToCollectAttribute($value)
    // {
    //     return number_format($value);
    // }

    // public function getTotalCouponAmountAttribute($value)
    // {
    //     return number_format($value);
    // }

    // public function getTotalDiscountAmountAttribute($value)
    // {
    //     return number_format($value);
    // }

    // public function getWarehousingFeeAttribute($value)
    // {
    //     return number_format($value);
    // }

    // public function getTransactionFeeAttribute($value)
    // {
    //     return number_format($value);
    // }

    // public function getInsuranceFeeAttribute($value)
    // {
    //     return number_format($value);
    // }

    // public function getTotalAgentFeeAttribute($value)
    // {
    //     return number_format($value);
    // }

    // public function getDepositAmountAttribute($value)
    // {
    //     return number_format($value);
    // }

    // public function getBusFeeAttribute($value)
    // {
    //     return number_format($value);
    // }

    // public function getSenderAmountToCollectAttribute($value)
    // {
    //     return number_format($value);
    // }

    // public function getReceiverAmountToCollectAttribute($value)
    // {
    //     return number_format($value);
    // }

    // public function getReturnFeeAttribute($value)
    // {
    //     return number_format($value);
    // }

    // public function getTotalBusFeeAttribute($value)
    // {
    //     return number_format($value);
    // }

    // public function getGrandTotalAttribute($value)
    // {
    //     return number_format($value);
    // }

    // public function getDeliveryCommissionAttribute($value)
    // {
    //     return number_format($value);
    // }
	public function getMerchantAppVoucherDetailAttribute() {
		return [
			'customer:id,name,phone,other_phone,address',
			'receiver_city:id,name,name_mm',
			'receiver_zone:id,name,name_mm,is_deliver',
			'delivery_status:id,status,status_mm',
			'payment_type:id,name,name_mm',
			'parcels.parcel_items:id,parcel_id,item_name,item_qty,item_price,product_id',
			'parcels.parcel_items.product:id,sku,item_name,item_price,lwh,weight',
			'parcels.parcel_items.product.attachment:id,image,created_at,resource_id'
		];
	}
	
    /**
     * Mutators
     */
    public function setVoucherInvoiceAttribute($value)
    {
        $this->attributes['voucher_invoice'] = 'VN' . str_pad($value, 6, '0', STR_PAD_LEFT);
    }

    public function setPostponeDateAttribute($value)
    {
        if ($value) {
            $this->attributes['postpone_date'] = date("Y-m-d H:i:s", strtotime($value));
        }
    }

    public function setTransactionFeeAttribute($value)
    {
        $this->attributes['transaction_fee'] = number_format((float) $value, 2, '.', '');
    }
    public function setInsuranceFeeAttribute($value)
    {
        $this->attributes['insurance_fee'] = number_format((float) $value, 2, '.', '');
    }
    public function setWarehousingFeeAttribute($value)
    {
        $this->attributes['warehousing_fee'] = number_format((float) $value, 2, '.', '');
    }

    /**
     * Mutators
     */
    // public function getPostponeDateAttribute($value)
    // {
    //     $this->attributes['postpone_date'] = new Carbon($value);
    // }

    /**
     * scopes
     */
    public function scopeFilter($query, $filter)
    {
        $query->whereNotNull('pickup_id');

        if (isset($filter['search']) && $search = $filter['search']) {
            $query->where(function ($qur) use ($date) {
                $qur->where('voucher_invoice', 'ILIKE', "%{$search}%")
                    ->orWhere(function ($q) use ($search) {
                        $q->whereHas('receiver', function ($qr) use ($search) {
                            $qr->where('name', 'ILIKE', "%{$search}%")
                                ->orWhere('phone', 'ILIKE', "%{$search}%");
                        });
                    })
                    ->orWhere(function ($q) use ($search) {
                        $q->whereHas('pickup', function ($qr) use ($search) {
                            $qr->whereHas('merchant', function ($qr_m) use ($search) {
                                $qr_m->where('name', 'ILIKE', "%{$search}%")
                                    ->orWhere('phone', 'ILIKE', "%{$search}%");
                            });
                        });
                    });
            });
        }

        if (isset($filter['pickup_invoice']) && $pickup_invoice = $filter['pickup_invoice']) {
            $query->whereHas('pickup', function ($qr) use ($pickup_invoice) {
                $qr->where('pickup_invoice', 'ILIKE', "%{$pickup_invoice}%");
            });
        }

        if (isset($filter['pickup_date']) && $pickup_date = $filter['pickup_date']) {
            $query->whereHas('pickup', function ($qr) use ($pickup_date) {
                $qr->whereDate('pickup_date', $pickup_date);
            });
        }

        if (isset($filter['voucher_invoice']) && $voucher_invoice = $filter['voucher_invoice']) {
            $query->where('voucher_invoice', 'ILIKE', "%{$voucher_invoice}%");
        }

        if (isset($filter['date']) && $date = $filter['date']) {
            $query->where(function ($qr) use ($date) {
                $qr->whereDate('postpone_date', $date)
                    ->orWhere(function ($q) use ($date) {
                        $q->where('postpone_date', null)
                            ->whereDate('created_at', $date);
                    });
            });
        }

        if (isset($filter['delivered_date']) && $delivered_date = $filter['delivered_date']) {
            $query->whereDate('delivered_date', $delivered_date);
        }

        if (isset($filter['thirdparty_invoice']) && $thirdparty_invoice = $filter['thirdparty_invoice']) {
            $query->where('thirdparty_invoice', 'ILIKE', "%{$thirdparty_invoice}%");
        }

        // if (isset($filter['receiver_city']) && $receiver_city = $filter['receiver_city']) {
        //     $query->whereHas('receiver_city', function ($q) use ($receiver_city) {
        //         $q->where('name', 'ILIKE', "%{$receiver_city}%");
        //     });
        // }

        if (isset($filter['receiver_city']) && $receiver_city = $filter['receiver_city']) {
            $query->where(function ($qr) use ($receiver_city) {
                $qr->where('receiver_city_id', $receiver_city)
                    ->orWhere(function ($q) use ($receiver_city) {
                        $q->whereNull('return_from_waybill')
                            ->where('delivery_status_id', 9)
                            ->where('is_return', 0)
                            ->where('is_closed', 0)
                            ->where(function ($q) {
                                $q->whereColumn('sender_city_id', '!=', 'receiver_city_id');
                                $q->whereColumn('receiver_city_id', '=', 'origin_city_id');
                            })
                            ->where('sender_city_id', $receiver_city);
                    });
            });
        }

        // if (isset($filter['receiver_zone']) && $receiver_zone = $filter['receiver_zone']) {
        //     $query->whereHas('receiver_zone', function ($q) use ($receiver_zone) {
        //         $q->where('name', 'ILIKE', "%{$receiver_zone}%");
        //     });
        // }

        if (isset($filter['receiver_zone']) && $receiver_zone = $filter['receiver_zone']) {
            $query->where('receiver_zone_id', $receiver_zone);
        }

        if (isset($filter['receiver_amount_to_collect']) && $receiver_amount_to_collect = $filter['receiver_amount_to_collect']) {
            $query->where('receiver_amount_to_collect', $receiver_amount_to_collect);
        }

        if (isset($filter['sender']) && $sender = $filter['sender']) {
            $query->whereHas('pickup', function ($q) use ($sender) {
                $q->whereHas('merchant', function ($qr) use ($sender) {
                    $qr->where('name', 'ILIKE', "%{$sender}%");
                    // ->orWhere('phone', 'ILIKE', "%{$sender}%");
                })
                    ->orwhereHas('customer', function ($qr) use ($sender) {
                        $qr->where('name', 'ILIKE', "%{$sender}%");
                    });
            });
        }

        if (isset($filter['receiver']) && $receiver = $filter['receiver']) {
            $query->whereHas('receiver', function ($q) use ($receiver) {
                $q->where('name', 'ILIKE', "%{$receiver}%");
                // ->orWhere('phone', 'ILIKE', "%{$receiver}%");
            });
        }

        if (isset($filter['sender_name']) && $sender_name = $filter['sender_name']) {
            $query->whereHas('pickup', function ($q) use ($sender_name) {
                $q->whereHas('merchant', function ($qr) use ($sender_name) {
                    $qr->where('name', 'ILIKE', "%{$sender_name}%");
                    // ->orWhere('phone', 'ILIKE', "%{$sender_name}%");
                })
                    ->orwhereHas('customer', function ($qr) use ($sender_name) {
                        $qr->where('name', 'ILIKE', "%{$sender_name}%");
                    });
            });
        }

        if (isset($filter['receiver_name']) && $receiver_name = $filter['receiver_name']) {
            $query->whereHas('receiver', function ($q) use ($receiver_name) {
                $q->where('name', 'ILIKE', "%{$receiver_name}%");
            });
        }

        if (isset($filter['waybill_id']) && $waybill_id = $filter['waybill_id']) {
            $query->whereHas('waybills', function ($q) use ($waybill_id) {
                $q->where('id', $waybill_id);
            });
        }

        if (isset($filter['waybill_invoice']) && $waybill_invoice = $filter['waybill_invoice']) {
            $query->whereHas('waybills', function ($q) use ($waybill_invoice) {
                $q->where('waybill_invoice', 'ILIKE', "%{$waybill_invoice}%");
            });
        }

        if (isset($filter['sender_phone']) && $sender_phone = $filter['sender_phone']) {
            $query->whereHas('pickup', function ($pickup_qr) use ($sender_phone) {
                $pickup_qr->whereHas('merchant', function ($merchant_qr) use ($sender_phone) {
                    $merchant_qr->whereHas('merchant_associates', function ($merchant_associates_qr) use ($sender_phone) {
                        $merchant_associates_qr->whereHas('contact_associates', function ($contact_qr) use ($sender_phone) {
                            $contact_qr->where('value', 'ILIKE', "%{$sender_phone}%");
                        });
                    });
                })
                    ->orwhereHas('customer', function ($customer_qr) use ($sender_phone) {
                        $customer_qr->where('phone', 'ILIKE', "%{$sender_phone}%")
                            ->orWhere('other_phone', 'ILIKE', "%{$sender_phone}%");
                    });
            });
        }

        if (isset($filter['receiver_phone']) && $receiver_phone = $filter['receiver_phone']) {
            $query->whereHas('receiver', function ($q) use ($receiver_phone) {
                $q->where('phone', 'ILIKE', "%{$receiver_phone}%");
            });
        }

        if (isset($filter['sender_gate']) && $sender_gate = $filter['sender_gate']) {
            $query->whereHas('sender_gate', function ($q) use ($sender_gate) {
                $q->where('name', 'ILIKE', "%{$sender_gate}%");
            });
        }

        // if (isset($filter['sender_gate']) && $sender_gate = $filter['sender_gate']) {
        //     $query->where('sender_gate_id', $sender_gate);
        // }

        if (isset($filter['call_status']) && $call_status = $filter['call_status']) {
            $query->whereHas('call_status', function ($q) use ($call_status) {
                $q->where('status', 'ILIKE', "%{$call_status}%");
            });
        }

        if (isset($filter['delivery_status']) && $delivery_status = $filter['delivery_status']) {
            if ($delivery_status == "Delivering") {
                $query->whereNotIn('delivery_status_id', [8, 9, 10]);
            } elseif ($delivery_status == "To Assign Sheet") {
                $query->whereNull('outgoing_status');
            } elseif ($delivery_status == "Delivering Vouchers") {
                $query->whereHas('delisheets', function ($q) {
                    $q->where('is_closed', 0);
                });
            } elseif ($delivery_status == "Over Third Attempt") {
                $query->where('delivery_counter', '>=', 3);
            } else {
                $query->whereHas('delivery_status', function ($q) use ($delivery_status) {
                    $q->where('status', 'ILIKE', "%{$delivery_status}%")
                        ->orWhere('status_mm', 'ILIKE', "%{$delivery_status}%");
                });
            }
        }

        if (isset($filter['payment_type']) && $payment_type = $filter['payment_type']) {
            $query->whereHas('payment_type', function ($q) use ($payment_type) {
                $q->where('name', 'ILIKE', "%{$payment_type}%");
            });
        }

        if (isset($filter['waybill_start_date']) && $waybill_start_date = $filter['waybill_start_date']) {
            if (isset($filter['waybill_end_date']) && $waybill_end_date = $filter['waybill_end_date']) {
                ($waybill_start_date == $waybill_end_date)
                    ?  $query->whereHas('waybills', function ($qr) use ($waybill_start_date) {
                        $qr->whereDate('waybills.created_at', $waybill_start_date);
                    })
                    : $query->whereHas('waybills', function ($qr) use ($waybill_start_date, $waybill_end_date) {
                        $qr->whereBetween('waybills.created_at', [$waybill_start_date, \Carbon\Carbon::parse($waybill_end_date)->addDays(1)]);
                    });
            } else {
                $query->whereHas('waybills', function ($qr) use ($waybill_start_date) {
                    $qr->whereDate('waybills.created_at', $waybill_start_date);
                });
            }
        }

        // if (isset($filter['start_date']) && $start_date = $filter['start_date']) {
        //     if (isset($filter['end_date']) && $end_date = $filter['end_date']) {
        //         ($start_date == $end_date)
        //             ? $query->where(function ($qr) use ($start_date) {
        //                 $qr->whereDate('created_at', $start_date)
        //                     ->where('postpone_date', null);
        //             })
        //             ->orWhereDate('postpone_date', $start_date)
        //             : $query->where(function ($qr) use ($start_date, $end_date) {
        //                 $qr->whereBetween('created_at', [$start_date, \Carbon\Carbon::parse($end_date)->addDays(1)])
        //                     ->where('postpone_date', null);
        //             })
        //             ->orWhereBetween('postpone_date', [$start_date, \Carbon\Carbon::parse($end_date)->addDays(1)]);
        //     } else {
        //         $query->where(function ($qr) use ($start_date) {
        //             $qr->whereDate('created_at', $start_date)
        //                 ->where('postpone_date', null);
        //         })
        //         ->orWhereDate('postpone_date', $start_date);
        //     }
        // }

        if (isset($filter['start_date']) && $start_date = $filter['start_date']) {
            if (isset($filter['end_date']) && $end_date = $filter['end_date']) {
                ($start_date == $end_date)
                    ? $query->whereDate('created_at', $start_date)
                    : $query->whereBetween('created_at', [$start_date, \Carbon\Carbon::parse($end_date)->addDays(1)]);
            } else {
                $query->whereDate('created_at', $start_date);
            }
        }

        if (isset($filter['pickup_start_date']) && $pickup_start_date = $filter['pickup_start_date']) {
            if (isset($filter['pickup_end_date']) && $pickup_end_date = $filter['pickup_end_date']) {
                $query->whereHas('pickup', function ($qr) use ($pickup_start_date, $pickup_end_date) {
                    ($pickup_start_date == $pickup_end_date)
                        ? $qr->whereDate('pickup_date', $pickup_start_date)
                        : $qr->whereBetween('pickup_date', [$pickup_start_date, \Carbon\Carbon::parse($pickup_end_date)->addDays(0)]);
                });
            } else {
                $query->whereHas('pickup', function ($qr) use ($pickup_start_date) {
                    $qr->whereDate('pickup_date', $pickup_start_date);
                });
            }
        }

        if (isset($filter['delivered_start_date']) && $delivered_start_date = $filter['delivered_start_date']) {
            if (isset($filter['delivered_end_date']) && $delivered_end_date = $filter['delivered_end_date']) {
                ($delivered_start_date == $delivered_end_date)
                    ? $query->whereDate('delivered_date', $delivered_start_date)
                    : $query->whereBetween('delivered_date', [$delivered_start_date, \Carbon\Carbon::parse($delivered_end_date)->addDays(0)]);
            } else {
                $query->whereDate('delivered_date', $delivered_start_date);
            }
        }

        if (isset($filter['postpone_date']) && $postpone_date = $filter['postpone_date']) {
            $query->whereDate('postpone_date', $postpone_date)
                ->whereNull('outgoing_status');
            // ->whereDoesntHave('delisheets');
        }

        if (isset($filter['postpone']) && $postpone = $filter['postpone']) {
            $query->whereNotNull('postpone_date')
                ->whereNull('outgoing_status');
            // ->whereDoesntHave('delisheets');
        }

        if (isset($filter['pending_return']) && $pending_return = $filter['pending_return']) {
            $query->where('is_return', false)
                ->where('delivery_status_id', 9)
                ->whereDoesntHave('return_sheets');
        }

        if (isset($filter['from_city_id']) && $from_city_id = $filter['from_city_id']) {
            $query->whereHas('waybills', function ($q) use ($from_city_id) {
                $q->where('from_city_id', $from_city_id);
            });
        }

        if (isset($filter['to_city_id']) && $to_city_id = $filter['to_city_id']) {
            $query->whereHas('waybills', function ($q) use ($to_city_id) {
                $q->where('to_city_id', $to_city_id);
            });
        }

        if (isset($filter['from_agent_id']) && $from_agent_id = $filter['from_agent_id']) {
            $query->where(function ($q) use ($from_agent_id){
                $q->where('from_agent_id', $from_agent_id)
                  ->orWhereHas('waybills', function ($q) use ($from_agent_id) {
                    $q->where('from_agent_id', $from_agent_id);
                 });
            });
        }

        if (isset($filter['to_agent_id']) && $to_agent_id = $filter['to_agent_id']) {
            $query->where(function ($q) use ($to_agent_id){
                $q->where('to_agent_id', $to_agent_id)
                  ->orWhereHas('waybills', function ($q) use ($to_agent_id) {
                    $q->where('to_agent_id', $to_agent_id);
                 });
            });
        }

        if (isset($filter['voucher_type']) && $voucher_type = $filter['voucher_type']) {
            ($voucher_type == 'waybill') ? 
                $query->has('waybills')->whereDoesntHave('delisheets')
                : $query;
        }

        if (isset($filter['outgoing_status']) && $outgoing_status = $filter['outgoing_status']) {
            $query->whereNull('outgoing_status');
        }

        // if (isset($filter['store_status']) && $store_status = $filter['store_status']) {
        //     $query->where('store_status_id', $store_status);
        // }

        if (isset($filter['store_status']) && $store_status = $filter['store_status']) {
            $query->whereHas('store_status', function ($q) use ($store_status) {
                $q->where('status', 'ILIKE', "%{$store_status}%");
            });
        }

        if (isset($filter['try_to_deliver']) && $try_to_deliver = $filter['try_to_deliver']) {
            $query->whereHas('delisheets', function ($q) {
                $q->where('is_closed', 0);
            });
        }
    }

    public function scopeFilterDraft($query, $filter)
    {
        $query->whereNull('pickup_id');
        $query->where('created_by_type', 'Merchant');

        if (isset($filter['search']) && $search = $filter['search']) {
            $query->where(function ($qur) use ($search) {
                $qur->where('voucher_invoice', 'ILIKE', "%{$search}%")
                    ->orWhere(function ($q) use ($search) {
                        $q->whereHas('receiver', function ($qr) use ($search) {
                            $qr->where('name', 'ILIKE', "%{$search}%")
                                ->orWhere('phone', 'ILIKE', "%{$search}%");
                        });
                    })
                    ->orWhere(function ($q) use ($search) {
                        $q->whereHas('created_by', function ($qr) use ($search) {
                            $qr->where('name', 'ILIKE', "%{$search}%")
                                ->orWhere('phone', 'ILIKE', "%{$search}%");
                        });
                    });
            });
        }

        if (isset($filter['voucher_invoice']) && $voucher_invoice = $filter['voucher_invoice']) {
            $query->where('voucher_invoice', 'ILIKE', "%{$voucher_invoice}%");
        }

        if (isset($filter['date']) && $date = $filter['date']) {
            $query->where(function ($qr) use ($date) {
                $qr->whereDate('postpone_date', $date)
                    ->orWhere(function ($q) use ($date) {
                        $q->where('postpone_date', null)
                            ->whereDate('created_at', $date);
                    });
            });
        }

        if (isset($filter['delivered_date']) && $delivered_date = $filter['delivered_date']) {
            $query->whereDate('delivered_date', $delivered_date);
        }

        if (isset($filter['postpone_date']) && $postpone_date = $filter['postpone_date']) {
            $query->whereDate('postpone_date', $postpone_date)
                ->whereNull('outgoing_status');
            // ->whereDoesntHave('delisheets');
        }

        if (isset($filter['postpone']) && $postpone = $filter['postpone']) {
            $query->whereNotNull('postpone_date')
                ->whereNull('outgoing_status');
            // ->whereDoesntHave('delisheets');
        }

        if (isset($filter['pending_return']) && $pending_return = $filter['pending_return']) {
            $query->where('is_return', false)
                ->where('delivery_status_id', 9)
                ->whereDoesntHave('return_sheets');
        }

        if (isset($filter['thirdparty_invoice']) && $thirdparty_invoice = $filter['thirdparty_invoice']) {
            $query->where('thirdparty_invoice', 'ILIKE', "%{$thirdparty_invoice}%");
        }

        // if (isset($filter['receiver_city']) && $receiver_city = $filter['receiver_city']) {
        //     $query->whereHas('receiver_city', function ($q) use ($receiver_city) {
        //         $q->where('name', 'ILIKE', "%{$receiver_city}%");
        //     });
        // }

        if (isset($filter['receiver_city']) && $receiver_city = $filter['receiver_city']) {
            $query->where(function ($qr) use ($receiver_city) {
                $qr->where('receiver_city_id', $receiver_city)
                    ->orWhere(function ($q) use ($receiver_city) {
                        $q->whereNull('return_from_waybill')
                            ->where('delivery_status_id', 9)
                            ->where('is_return', 0)
                            ->where('is_closed', 0)
                            ->where(function ($q) {
                                $q->whereColumn('sender_city_id', '!=', 'receiver_city_id');
                                $q->whereColumn('receiver_city_id', '=', 'origin_city_id');
                            })
                            ->where('sender_city_id', $receiver_city);
                    });
            });
        }

        if (isset($filter['receiver_zone']) && $receiver_zone = $filter['receiver_zone']) {
            $query->where('receiver_zone_id', $receiver_zone);
        }

        if (isset($filter['receiver_amount_to_collect']) && $receiver_amount_to_collect = $filter['receiver_amount_to_collect']) {
            $query->where('receiver_amount_to_collect', $receiver_amount_to_collect);
        }

        if (isset($filter['sender']) && $sender = $filter['sender']) {
            // $query->whereHas('pickup', function ($q) use ($sender) {
            $query->whereHas('created_by', function ($qr) use ($sender) {
                $qr->where('name', 'ILIKE', "%{$sender}%");
                // ->orWhere('phone', 'ILIKE', "%{$sender}%");
            })
                ->orwhereHas('customer', function ($qr) use ($sender) {
                    $qr->where('name', 'ILIKE', "%{$sender}%");
                });
            // });
        }

        if (isset($filter['receiver']) && $receiver = $filter['receiver']) {
            $query->whereHas('receiver', function ($q) use ($receiver) {
                $q->where('name', 'ILIKE', "%{$receiver}%");
                // ->orWhere('phone', 'ILIKE', "%{$receiver}%");
            });
        }

        if (isset($filter['sender_name']) && $sender_name = $filter['sender_name']) {
            // $query->whereHas('pickup', function ($q) use ($sender_name) {
            $query->whereHas('created_by_merchant', function ($qr) use ($sender_name) {
                $qr->where('name', 'ILIKE', "%{$sender_name}%");
                // ->orWhere('phone', 'ILIKE', "%{$sender_name}%");
            })
                ->orwhereHas('customer', function ($qr) use ($sender_name) {
                    $qr->where('name', 'ILIKE', "%{$sender_name}%");
                });
            // });
        }

        if (isset($filter['receiver_name']) && $receiver_name = $filter['receiver_name']) {
            $query->whereHas('receiver', function ($q) use ($receiver_name) {
                $q->where('name', 'ILIKE', "%{$receiver_name}%");
            });
        }

        if (isset($filter['sender_phone']) && $sender_phone = $filter['sender_phone']) {
            // $query->whereHas('pickup', function ($pickup_qr) use ($sender_phone) {
            $query->whereHas('created_by_merchant', function ($merchant_qr) use ($sender_phone) {
                $merchant_qr->whereHas('merchant_associates', function ($merchant_associates_qr) use ($sender_phone) {
                    $merchant_associates_qr->whereHas('contact_associates', function ($contact_qr) use ($sender_phone) {
                        $contact_qr->where('value', 'ILIKE', "%{$sender_phone}%");
                    });
                });
            })
                ->orwhereHas('customer', function ($customer_qr) use ($sender_phone) {
                    $customer_qr->where('phone', 'ILIKE', "%{$sender_phone}%")
                        ->orWhere('other_phone', 'ILIKE', "%{$sender_phone}%");
                });
            // });
        }

        if (isset($filter['receiver_phone']) && $receiver_phone = $filter['receiver_phone']) {
            $query->whereHas('receiver', function ($q) use ($receiver_phone) {
                $q->where('phone', 'ILIKE', "%{$receiver_phone}%");
            });
        }

        if (isset($filter['sender_gate']) && $sender_gate = $filter['sender_gate']) {
            $query->whereHas('sender_gate', function ($q) use ($sender_gate) {
                $q->where('name', 'ILIKE', "%{$sender_gate}%");
            });
        }

        // if (isset($filter['sender_gate']) && $sender_gate = $filter['sender_gate']) {
        //     $query->where('sender_gate_id', $sender_gate);
        // }

        if (isset($filter['call_status']) && $call_status = $filter['call_status']) {
            $query->whereHas('call_status', function ($q) use ($call_status) {
                $q->where('status', 'ILIKE', "%{$call_status}%");
            });
        }

        if (isset($filter['payment_type']) && $payment_type = $filter['payment_type']) {
            $query->whereHas('payment_type', function ($q) use ($payment_type) {
                $q->where('name', 'ILIKE', "%{$payment_type}%");
            });
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

        if (isset($filter['from_city_id']) && $from_city_id = $filter['from_city_id']) {
            $query->whereHas('waybills', function ($q) use ($from_city_id) {
                $q->where('from_city_id', $from_city_id);
            });
        }

        if (isset($filter['to_city_id']) && $to_city_id = $filter['to_city_id']) {
            $query->whereHas('waybills', function ($q) use ($to_city_id) {
                $q->where('to_city_id', $to_city_id);
            });
        }

        if (isset($filter['voucher_type']) && $voucher_type = $filter['voucher_type']) {
            ($voucher_type == 'waybill') ? $query->has('waybills') : $query;
        }

        if (isset($filter['outgoing_status']) && $outgoing_status = $filter['outgoing_status']) {
            $query->whereNull('outgoing_status');
        }

        // if (isset($filter['store_status']) && $store_status = $filter['store_status']) {
        //     $query->where('store_status_id', $store_status);
        // }

        if (isset($filter['store_status']) && $store_status = $filter['store_status']) {
            $query->whereHas('store_status', function ($q) use ($store_status) {
                $q->where('status', 'ILIKE', "%{$store_status}%");
            });
        }

        if (isset($filter['try_to_deliver']) && $try_to_deliver = $filter['try_to_deliver']) {
            $query->whereHas('delisheets', function ($q) {
                $q->where('is_closed', 0);
            });
        }
    }

    public function scopeOrder($query, $order)
    {
        $sortBy = isset($order['sortBy']) ? $order['sortBy'] : 'id';
        $orderBy = isset($order['orderBy']) ? $order['orderBy'] : 'desc';

        $query->orderBy($sortBy, $orderBy);
    }

    public function scopeMerchantFilter($query, $filter)
    {
        if (isset($filter['filter']) && $filter['filter'] == 'draft') {
            $query->where(function ($q) {
                $q->where('created_by_id', auth()->user()->id)
                    ->where('created_by_type', 'Merchant')
                    ->whereNull('pickup_id');
            });
        }

        if (isset($filter['filter']) && $filter['filter'] == 'delivering') {
            $query->whereHas('pickup', function ($q) {
                $q->where('sender_type', 'Merchant')
                    ->where('sender_id', auth()->user()->id);
            })
                ->whereNotIn('store_status_id', [1, 8, 10])
                ->whereNotIn('delivery_status_id', [8])
                ->where('is_return', 0);
        }

        if (isset($filter['filter']) && $filter['filter'] == 'delivered') {
            $query->whereHas('pickup', function ($q) {
                $q->where('sender_type', 'Merchant')
                    ->where('sender_id', auth()->user()->id);
            })->where('delivery_status_id', 8)
                ->orWhere(function ($q1) {
                    $q1->whereHas('pickup', function ($q2) {
                        $q2->where('sender_type', 'Merchant')
                            ->where('sender_id', auth()->user()->id);
                    })->where('delivery_status_id', 9)->where('is_return', 1);
                });
        }


        if (isset($filter['filter']) && $filter['filter'] == 'binded_vouchers') {
            $query->where(function ($q) {
                $q->where('created_by_id', auth()->user()->id)
                    ->where('created_by_type', 'Merchant')
                    ->whereNull('pickup_id')
                    ->whereNotNull('qr_associate_id');
            });
        }

        if (isset($filter['search']) && $search = $filter['search']) {
            $query->whereHas('customer', function ($qr) use ($search) {
                $qr->where('name', 'ILIKE', "%{$search}%")
                    ->orWhere('phone', 'ILIKE', "%{$search}%");
            });
        }
    }
    public function scopeMerchantSearch($query, $filter)
    {
        // $query->where(function ($query) {
        //     $query->where('created_by_id', auth()->user()->id)
        //         ->where('created_by_type', 'Merchant');
        //     })
        //     ->orWhere(function ($query) {
        //         $query->whereHas('pickup', function ($query) {
        //             $query->where('sender_type', 'Merchant')
        //             ->where('sender_id', auth()->user()->id);
        //         });
        //     }) ;

        if (isset($filter['search']) && $search = $filter['search']) {
            $query->whereHas('customer', function ($qr) use ($search) {
                $qr->where('name', 'ILIKE', "%{$search}%")
                    ->orWhere('phone', 'ILIKE', "%{$search}%");
            });
        }
    }

    public function scopeDeliSheet($query)
    {
        // $today = date('Y-m-d');
        // $query->whereNull('outgoing_status')
        //     ->whereIn('store_status_id', [2, 3, 4, 8])
        //     ->where('call_status_id', 6)
        //     ->whereNotIn('delivery_status_id', [8, 9])
        //     ->where('is_closed', 1)
        //     ->whereColumn('sender_city_id', 'receiver_city_id');
        // $query->whereDate('postpone_date', $today)
        //     ->orWhere('postpone_date', null)
        //     ->orderBy('id', 'desc');
        // // ->where(function ($q) use ($date) {
        // //     $q->whereDate('postpone_date', $date)
        // //       ->orWhere(function ($q) use ($date) {
        // //           $q->where('postpone_date', null)
        // //             ->whereDate('created_at', $date);
        // //       });
        // // });

        $query->whereNull('outgoing_status')
            ->whereIn('store_status_id', [2, 3, 4, 8])
            // ->where('call_status_id', 6)
            ->whereNotIn('delivery_status_id', [8, 9])
            //->where('is_closed', 1)
            ->whereColumn('origin_city_id', 'receiver_city_id')
            ->where('origin_city_id', auth()->user()->city_id)
            ->where(function ($qr) {
                $qr->when(!request()->get('date'), function ($q) {
                    $q->whereDate('postpone_date', date('Y-m-d'))
                        ->orWhere('postpone_date', null);
                });
            })
            ->orderBy('id', 'desc');
    }

    public function scopeExpressDeliSheet($query)
    {
        $query->whereNull('outgoing_status')
            // ->whereIn('store_status_id', [2, 3, 4, 8])
            // ->where('call_status_id', 6)
            ->whereNotIn('delivery_status_id', [8, 9])
            //->where('is_closed', 1)
            ->whereColumn('origin_city_id', 'receiver_city_id')
            ->where('origin_city_id', auth()->user()->city_id)
            ->where(function ($qr) {
                $qr->when(!request()->get('date'), function ($q) {
                    $q->whereDate('postpone_date', date('Y-m-d'))
                        ->orWhere('postpone_date', null);
                });
            })
            ->orderBy('id', 'desc');
    }

    public function scopeBusSheet($query)
    {
        $query->whereNull('outgoing_status')
            ->whereIn('store_status_id', [2, 3, 4, 8])
            // ->where('call_status_id', 6)
            ->where('delivery_status_id', '!=', 8)
            //->where('is_closed', 1)
            ->where('bus_station', 1)
            // ->where('sender_bus_station_id', $from_bus_station)
            ->orderBy('id', 'desc');
    }


    public function scopeWayBill($query, $filter)
    {
        $query->where(function ($query) {
            $query->whereNull('outgoing_status')
                ->whereIn('store_status_id', [2, 3, 4, 8])
                //->where('call_status_id', 6)
                ->whereIn('delivery_status_id', [1, 2, 3, 4, 5, 6, 7, 10])
                //->where('is_closed', 1)
                ->where('bus_station', 0)
                //->where('sender_city_id', auth()->user()->city_id)
                ->whereNotNull('pickup_id')
                ->where(function ($q) {
                    $q->whereColumn('sender_city_id', '=', 'origin_city_id');
                    $q->whereColumn('sender_city_id', '!=', 'receiver_city_id')
                        ->orWhere([['sender_city_id', null], ['receiver_city_id', '!=', null]])
                        ->orWhere([['sender_city_id', '!=', null], ['receiver_city_id', null]]);
                });
        })
        ->orWhere(function ($query) {
            $query
                ->where('bus_station', 0)
                ->whereNotNull('pickup_id')
                ->whereNull('return_from_waybill')
                ->where(function ($q) {
                    $q->where(function ($q) {
                        $q->where('delivery_status_id', 9);
                        if (isset($filter['agent_id']) && $agent_id = $filter['agent_id']) {
                            $q->where('from_agent_id', $agent_id);
                        }
                    })
                    ->orWhere(function ($q) {
                        $q->whereColumn('sender_city_id', '!=', 'origin_city_id');
                        $q->whereColumn('receiver_city_id', '!=', 'origin_city_id');
                        $q->whereColumn('sender_city_id', '!=', 'receiver_city_id')
                            ->where('origin_city_id', auth()->user()->city_id);
                    });
                    //   ->orWhere('origin_city_id', auth()->user()->city_id);
                })
                ->where('is_return', 0)
                ->where('is_closed', 0)
                ->whereIn('store_status_id', [2, 3, 4, 8])
                ->where(function ($q) {
                    $q->whereColumn('sender_city_id', '!=', 'receiver_city_id');
                    //$q->whereColumn('receiver_city_id', '=', 'origin_city_id');
                    //  ->orWhere('bus_station', 1);
                });
        }) //
        ->orderBy('id', 'desc');
    }

    public function scopeMerchantSheet($query, $merchant_id)
    {
        $query->whereIn('outgoing_status', [0, 1, 2, 3, 5])
            ->whereIn('store_status_id', [2, 3, 4, 7, 8, 9])
            // ->where('call_status_id', 6)
            ->whereIn('delivery_status_id', [8, 9])
            ->where('is_closed', 1)
            ->where('deli_payment_status', 1)
            ->where('sender_city_id', auth()->user()->city_id)
            ->whereHas('pickup', function ($q) use ($merchant_id) {
                $q->where('sender_type', 'Merchant')
                    ->where('sender_id', $merchant_id);
            })->orderBy('id', 'desc');
    }

    public function scopeAgentWaybillVoucher($query, $filter)
    {
        $query->whereColumn('sender_city_id', '!=', 'receiver_city_id')
            // ->whereIn('delivery_status_id', [1, 2, 3, 4, 5, 6, 7, 10])
            ->where('is_closed', 0)
            ->where('bus_station', 0)
            ->where('receiver_city_id', auth()->user()->city_id)
            //add query for multiple agent
            ->where('to_agent_id', auth()->user()->id)
            ->whereHas('waybills', function ($q) {
                $q->where('is_received', 1);
                $q->where('to_city_id', auth()->user()->city_id)
                  ->where('to_agent_id', auth()->user()->id);
            });
        if (isset($filter['cant_deliver']) && $delivered = $filter['cant_deliver']) {
            $query->where('delivery_status_id', 10);
        } else {
            $query->whereIn('delivery_status_id', [1, 2, 3, 4, 5, 6, 7]);
        }
        $query->orderBy('id', 'desc');
    }

    public function scopeAgentWaybillJobVoucher($query, $filter)
    {
        if (isset($filter['start_date']) && $start_date = $filter['start_date']) {
            if (isset($filter['end_date']) && $end_date = $filter['end_date']) {
                ($start_date == $end_date)
                    ? $query->whereDate('created_at', $start_date)
                    : $query->whereBetween('created_at', [$start_date, \Carbon\Carbon::parse($end_date)->addDays(0)]);
            } else {
                $query->whereDate('created_at', $start_date);
            }
        }
        $query->whereColumn('sender_city_id', '!=', 'receiver_city_id')
            // ->whereIn('delivery_status_id', [1, 2, 3, 4, 5, 6, 7, 10])
            ->where('is_closed', 0)
            ->where('bus_station', 0)
            ->where('receiver_city_id', auth()->user()->city_id)
            ->whereHas('waybills', function ($q) {
                $q->where('is_received', 1);
                $q->where('to_city_id', auth()->user()->city_id);
            });
        $query->whereIn('delivery_status_id', [1, 2, 3, 4, 5, 6, 7, 10]);
        $query->orderBy('id', 'desc');
    }

    public function scopeAgentWaybillVoucherListWithStatusId($query, $filter)
    {
        if (isset($filter['start_date']) && $start_date = $filter['start_date']) {
            if (isset($filter['end_date']) && $end_date = $filter['end_date']) {
                ($start_date == $end_date)
                    ? $query->whereDate('created_at', $start_date)
                    : $query->whereBetween('created_at', [$start_date, \Carbon\Carbon::parse($end_date)->addDays(0)]);
            } else {
                $query->whereDate('created_at', $start_date);
            }
        }
        $query->whereColumn('sender_city_id', '!=', 'receiver_city_id')
            // ->whereIn('delivery_status_id', [1, 2, 3, 4, 5, 6, 7, 10])
            ->where('is_closed', 1)
            ->where('bus_station', 0)
            ->where('receiver_city_id', auth()->user()->city_id)
            ->where('to_agent_id', auth()->user()->id)
            ->whereHas('waybills', function ($q) {
                $q->where('is_received', 1);
            });
        if (isset($filter['delivery_status_id']) && $delivery_status_id = $filter['delivery_status_id']) {
            $query->where('delivery_status_id', $delivery_status_id);
        }
        $query->orderBy('id', 'desc');
    }

    public function scopeReturnSheet($query, $merchant_id)
    {
        
        // dd($merchant_id);
        $query->where('delivery_status_id', 9)
            // ->whereNull('outgoing_status')
            ->where('store_status_id', '!=', 9)
            ->where('is_return', '!=', 1)
            ->where('deli_payment_status', '!=', 1)
            //->where('return_from_waybill', 2)
            ->doesnthave('return_sheets')
            ->whereHas('pickup', function ($query) use ($merchant_id) {
                $query->where('sender_type', 'Merchant')
                    ->where('sender_id', $merchant_id);
            })
            ->where(function ($q) {
                $q->where(function ($q){
                    $q->where('origin_city_id', auth()->user()->city_id);
                    $q->whereNull('from_agent_id');
                })->orWhere(function ($q){
                    $q->where('return_from_waybill',2);
                        // ->where('sender_city_id', auth()->user()->city_id);
                });
            })
            
            // ->where(function ($q) {
            //     $q->whereColumn('sender_city_id', '=', 'receiver_city_id');
            // })
            // ->where(function ($q) {
            //     $q->whereColumn('sender_city_id', '!=', 'receiver_city_id')
            //         ->where('return_from_waybill', 2);
            // })
            ->orderBy('id', 'desc');
    }

    public function scopeMerchantReturnSheet($query, $merchant_id)
    {
        $query->where('delivery_status_id', 9)
            ->where('store_status_id',  9)
            ->where('is_return', '!=', 1)
            ->doesnthave('return_sheets')
            //->where('sender_city_id', auth()->user()->city_id)
            ->where(function ($q){
                $q->where('origin_city_id', auth()->user()->city_id);
            })->orWhere(function ($q){
                $q->where('return_from_waybill',2);
                $q->where('sender_city_id', auth()->user()->city_id);
            })
            
            ->whereHas('pickup', function ($q) use ($merchant_id) {
                $q->where('sender_type', 'Merchant')
                    ->where('sender_id', $merchant_id);
            })
            // ->where(function ($q) {
            //     $q->whereColumn('sender_city_id', '=', 'receiver_city_id');
            // })
            // ->where(function ($q) {
            //     $q->whereColumn('sender_city_id', '!=', 'receiver_city_id')
            //         ->where('return_from_waybill', 2);
            // })
            ->orderBy('id', 'desc');
    }

    public function scopeDeliveryDeliSheet($query, $id)
    {
        $query->whereColumn('sender_city_id', 'receiver_city_id')
            ->whereHas('delisheets', function ($q) use ($id) {
                $q->where('delivery_id', $id);
            });
    }

    public function scopeSuperMerchantVoucherFilter($query, $filter)
    {
        $query->where(function ($q) {
            $q->where('created_by_id', auth()->user()->id)
                ->where('created_by_type', 'Merchant');
        });
        if (isset($filter['merchant_id']) && $merchant_id = $filter['merchant_id']) {
            $query->whereHas('pickup', function ($qr) use ($merchant_id) {
                $qr->where('sender_type', 'Merchant');
                $qr->where('sender_id', $merchant_id);
            });
        }
        $query->orderBy('id', 'DECS');
    }

    public function scopeClosed($query)
    {
        $query->where('is_closed', 1);
    }

    public function scopeOpened($query)
    {
        $query->where('is_closed', 1);
    }

    public function scopeReturn($query)
    {
        $query->where('is_return', 1);
    }

    public function global_scales()
    {
        return $this->hasMany(GlobalScale::class, 'global_scale_id')->withTrashed();
        //return $this->belongsToMany('App\Models\GlobalScale', 'parcels', 'voucher_id', 'global_scale_id');
    }

    public function global_scale()
    {
        return $this->belongsToMany(GlobalScale::class, 'global_scale_id')->withTrashed();
    }

    public function parcels()
    {
        return $this->hasMany(Parcel::class, 'voucher_id')->withTrashed();
    }

    public function call_status()
    {
        return $this->belongsTo(CallStatus::class)->withTrashed();
    }

    public function delivery_status()
    {
        return $this->belongsTo(DeliveryStatus::class)->withTrashed();
    }

    public function store_status()
    {
        return $this->belongsTo(StoreStatus::class)->withTrashed();
    }

    public function payment_status()
    {
        return $this->belongsTo(PaymentStatus::class)->withTrashed();
    }

    public function city()
    {
        return $this->belongsTo(City::class)->withTrashed();
    }

    public function sender_city()
    {
        return $this->belongsTo(City::class, 'sender_city_id')->withTrashed();
    }

    public function all_sheets()
    {
        return ((($this->delisheets->merge($this->waybills))
            ->merge($this->bussheets))
            ->merge($this->merchant_sheets))
            ->merge($this->return_sheets)->sortByDesc('created_at');
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

    public function bus_station()
    {
        return $this->belongsTo(BusStation::class)->withTrashed();
    }

    public function sender_bus_station()
    {
        return $this->belongsTo(BusStation::class, 'sender_bus_station_id')->withTrashed();
    }

    public function receiver_bus_station()
    {
        return $this->belongsTo(BusStation::class, 'receiver_bus_station_id')->withTrashed();
    }

    public function sender_gate()
    {
        return $this->belongsTo(Gate::class, 'sender_gate_id')->withTrashed();
    }

    public function receiver_gate()
    {
        return $this->belongsTo(Gate::class, 'receiver_gate_id')->withTrashed();
    }

    public function pickup()
    {
        return $this->belongsTo(Pickup::class)->withTrashed();
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'receiver_id')->withTrashed();
    }

    public function discount_type()
    {
        return $this->belongsTo(DiscountType::class)->withTrashed();
    }

    public function payment_type()
    {
        return $this->belongsTo(PaymentType::class)->withTrashed();
    }

    public function voucher_histories()
    {
        return $this->hasMany(VoucherHistory::class)->orderBy('id', 'desc');
    }

    public function receiver()
    {
        return $this->belongsTo(Customer::class)->withTrashed();
    }

    public function delegate_person()
    {
        return $this->belongsTo(Staff::class, 'delegate_person')->withTrashed();
    }
    public function delegate_duration()
    {
        return $this->belongsTo(DelegateDuration::class)->withTrashed();
    }

    public function qr_associate()
    {
        return $this->belongsTo(QrAssociate::class)->withTrashed();
    }

    public function delisheets()
    {
        return $this->belongsToMany(DeliSheet::class, 'deli_sheet_vouchers', 'voucher_id', 'delisheet_id')
            // ->using(DeliSheetVoucher::class)
            ->as('deli_sheet_vouchers')
            ->withTimestamps()
            ->withPivot([
                'return',
                'ATC_receiver',
                'is_came_from_mobile',
                'note',
                'priority',
                'delivery_status',
                'created_by',
                'updated_by',
                'deleted_by',
                'cant_deliver'
            ]);
    }

    public function bussheets()
    {
        return $this->belongsToMany(BusSheet::class, 'bus_sheet_vouchers', 'voucher_id', 'bus_sheet_id')
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
    }

    public function waybills()
    {
        return $this->belongsToMany(Waybill::class, 'waybill_vouchers', 'voucher_id', 'waybill_id')
            ->as('waybill_vouchers')
            ->withTimestamps()
            ->withPivot([
                'note',
                'priority',
                'status',
                'created_by',
                'updated_by',
                'deleted_by'
            ]);
    }

    public function merchant_sheets()
    {
        return $this->belongsToMany(MerchantSheet::class, 'merchant_sheet_vouchers', 'voucher_id', 'merchant_sheet_id')
            // ->using(MerchantSheetVoucher::class)
            ->as('merchant_sheet_vouchers')
            ->withTimestamps()
            ->withPivot([
                'created_by',
                'updated_by',
                'deleted_by'
            ]);
    }

    public function return_sheets()
    {
        return $this->belongsToMany(ReturnSheet::class, 'return_sheet_vouchers', 'voucher_id', 'return_sheet_id')
            ->as('return_sheet_vouchers')
            ->withTimestamps()
            ->withPivot([
                'note',
                'priority',
                'created_by',
                'updated_by',
                'deleted_by'
            ]);
    }

    public function tracking_status()
    {
        return $this->belongsToMany(TrackingStatus::class, 'tracking_vouchers', 'voucher_id', 'tracking_status_id')
            ->as('tracking_vouchers')
            ->withTimestamps()
            ->withPivot([
                'created_by',
                'updated_by',
                'deleted_by'
            ]);
    }


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

    public function pending_returning_actor()
    {
        return $this->morphTo()->withTrashed();
    }

    public function pending_returning_actor_by_merchant()
    {
        return BelongsToMorph::build($this, Merchant::class, 'pending_returning_actor');
    }

    public function pending_returning_actor_by_staff()
    {
        return BelongsToMorph::build($this, Staff::class, 'pending_returning_actor');
    }

    public function journals()
    {
        return $this->morphMany(Journal::class, 'resourceable', 'resourceable_type', 'resourceable_id');
    }

    public function temp_journal()
    {
        return $this->morphOne(TempJournal::class, 'resourceable', 'resourceable_type', 'resourceable_id');
    }

    public function attachments()
    {
        return $this->morphMany('App\Models\Attachment', 'resourceable', 'resource_type', 'resource_id');
    }

    public function scopePrepaidAmount($query)
    {
        return $query->whereIn('payment_type_id', [5, 6, 7, 8, 9, 10])->sum('sender_amount_to_collect');
    }

    public function messages()
    {
        return $this->morphMany(Message::class, 'referable')->latest();
    }

    // kyncode
    public function voucherSheetFire($sheetInvoice, $logStatus)
    {
        $voucher_data = array(
            'requests' => [
                'voucher_id' => $this->id,
                'previous' => $sheetInvoice,
                'logStatus' => $logStatus
            ]
        );
        \Event::fire('voucherInSheet', array($voucher_data));
    }
    public function voucherPickupFire($logStatus, $pickup_id = null)
    {
        $data = array(
            'requests' => [
                'pickup_id' => $pickup_id ? $pickup_id : $this->pickup_id,
                'previous' => $this->voucher_invoice,
                'logStatus' => $logStatus
            ]
        );
        \Event::fire('voucherInPickup', array($data));
    }
    public function delisheet_vouchers()
    {
        return $this->hasMany(DeliSheetVoucher::class);
    }
    public function way_bill_vouchers()
    {
        return $this->hasMany(WaybillVoucher::class);
    }
    public function returnsheet_vouchers()
    {
        return $this->hasMany(ReturnSheetVoucher::class);
    }
    public function tracking_vouchers()
    {
        return $this->hasMany(TrackingVoucher::class);
    }
    public function parcel_items()
    {
        return $this->hasManyThrough('App\Models\ParcelItem', 'App\Models\Parcel', 'voucher_id', 'parcel_id', 'id', 'id');
    }
    public function postpone_actor()
    {
        return $this->morphTo()->withTrashed();
    }
    public function postpone_actor_by_merchant()
    {
        return BelongsToMorph::build($this, Merchant::class, 'postpone_actor');
    }
    public function postpone_actor_by_staff()
    {
        return BelongsToMorph::build($this, Staff::class, 'postpone_actor');
    }

    public function from_agent()
    {
        return $this->belongsTo(Agent::class, 'from_agent_id');
    }

    public function to_agent()
    {
        return $this->belongsTo(Agent::class, 'to_agent_id');
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function scopeGetMerchantDeliveryVouchers($query, $filter)
    {
        if ($filter) {
            $query->whereHas('pickup', function ($q) {
                $q->where(function($q) {
                    $q->where('sender_type', 'Merchant')
                      ->where('sender_id', auth()->user()->id);
                })->orWhere(function ($q) {
                    $q->where('created_by_id', auth()->user()->id)
                        ->where('created_by_type', 'Merchant');
                });
            })->where(function($query) use($filter) {
                if ($filter == 'delivering_vouchers') {
                    $query->whereIn('store_status_id', [1,2, 5]);
                }else{
                    $query->where('delivery_status_id', 8)
                         ->whereNotNull('end_date');
                }
            });
        }
    }

    public function scopeGetMerchantAllVouchers($query, $filter)
    {
        if ($filter) {
            $query->whereHas('pickup', function ($q) {
                $q->where(function($q) {
                    $q->where('sender_type', 'Merchant')
                      ->where('sender_id', auth()->user()->id);
                })->orWhere(function ($q) {
                    $q->where('created_by_id', auth()->user()->id)
                        ->where('created_by_type', 'Merchant');
                });
            })->orWhere(function($query) use($filter) {
                $query->where('created_by_id', auth()->user()->id)
                      ->where('created_by_type', 'Merchant');
            });
        }
    }

    public function scopeGetMerchantPickupVouchers($query)
    {
        $query->whereHas('pickup', function ($q) {
            $q->where(function($q) {
                $q->where('sender_type', 'Merchant')
                    ->where('sender_id', auth()->user()->id);
            });
        });
    }

    public function scopeGetMerchantDraftVouchers($query, $filter)
    {
        if ($filter) {
            $query->where('created_by_id', auth()->user()->id)
                    ->where('created_by_type', 'Merchant')
                    ->where(function($query) use($filter) {
                        if($filter == 'incomplete_vouchers'){
                            $query->whereNull('pickup_id')
                                    ->whereNull('receiver_id');
                        }else{
                            $query->whereNull('pickup_id')
                                ->whereNotNull('receiver_id');
                        }
                    });
        }
    }

    public function scopeGetMerchantReturnVouchers($query, $filter)
    {
        $query->where('delivery_status_id', 9);

        $query->whereHas('pickup', function ($query) {
            $query->where('sender_type', 'Merchant')
                ->where('sender_id', auth()->user()->id);
        });
        
        if($filter == 'pending_return_vouchers'){
            $query->whereDoesntHave('return_sheets')
                  ->where('is_return', false);
        }

        if($filter == 'returning_vouchers'){
            $query->where('is_return', false)
                  ->whereHas('return_sheets', function($q) {
                    $q->where('is_closed', false)
                        ->where('is_returned', false);
                        
                 });
        }

        if($filter == 'returned_vouchers'){
            $query->where('is_return', true);
        }
    }

    public function scopeGetMerchantCantDeliveredVouchers($query, $filter)
    {
		if($filter){
            $query->whereHas('pickup', function ($q) {
                $q->where('sender_type', 'Merchant')
                    ->where('sender_id', auth()->user()->id);
            });
            $query->where(function ($query) use ($filter) {
				if ($filter == 'cant_delivered_unsolved') {
					$query->whereNotIn('delivery_status_id', [8, 9])
						->where('delivery_counter','>',0)
						->whereNull('outgoing_status')
                        ->where('is_manual_return',0)
					    ->whereNull('postpone_date');
				} else {
					$query->where('delivery_status_id','!=', 8)
                        ->where('delivery_counter','>',0)
                        ->whereNull('outgoing_status')
						->where(function($query) {
							$query->where('is_manual_return',1)
								  ->orWhere(function ($q){
                                      $q->whereNotNull('postpone_date');
                                  });
						});
				}
			});
        }
    }
}
