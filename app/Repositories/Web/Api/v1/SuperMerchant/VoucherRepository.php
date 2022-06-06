<?php

namespace App\Repositories\Web\Api\v1\SuperMerchant;

use Carbon\Carbon;
use App\Models\City;
use App\Models\Gate;
use App\Models\Zone;
use App\Models\Route;
use App\Models\Parcel;
use App\Models\Pickup;
use App\Models\Voucher;
use App\Models\Customer;
use App\Models\Merchant;
use App\Models\BusDropOff;
use App\Models\BusStation;
use App\Models\DoorToDoor;
use App\Models\ParcelItem;
use App\Models\GlobalScale;
use App\Models\PaymentType;
use App\Models\WaybillVoucher;
use App\Models\CouponAssociate;
use App\Models\MerchantDiscount;
use App\Models\MerchantAssociate;
use App\Repositories\BaseRepository;
use App\Repositories\Web\Api\v1\CouponRepository;
use App\Repositories\Web\Api\v1\ParcelRepository;
use App\Repositories\Web\Api\v1\ParcelItemRepository;
use App\Repositories\Web\Api\v1\SuperMerchant\CustomerRepository;

class VoucherRepository extends BaseRepository
{
    /**
     * @return string
     */
    public function model()
    {
        return Voucher::class;
    }

    /**
     * @param array $data
     *
     * @return Voucher
     */
    public function create(array $data)
    {
        $bus_fee              = 0;
        $item_price           = isset($data['total_item_price']) ? $data['total_item_price'] : 0;
        $payment_type         = PaymentType::find($data['payment_type_id']);
        $pickup               = Pickup::find($data['pickup_id']);
        $bus_station          = isset($data['bus_station']) ? $data['bus_station'] : null;
        $warehousing_fee      = 0;
        $transaction_fee      = 0;
        $insurance_fee        = 0;
        $delivery_amount      = 0;
        $discount_amount      = 0;
        $coupon_amount        = 0;
        $is_transaction_fee   = true;
        $discount_id          = null;
        $discount_type_id     = null;
        $coupon_associate_id  = null;
        $coupon_data          = null;
        $merchant_discount    = null;
        $extra_or_discount = 0;
        $total_extra_amount = 0;
        $agent_fee = 0;
        $customer = null;
        $global_scale_id = isset($data['global_scale_id']) ? $data['global_scale_id'] : 1;

        //Merchant or Custormer
        if ($pickup->sender_type == 'Merchant') {
            $merchant_associate = MerchantAssociate::find($pickup->sender_associate_id);
            $sender_city_id = isset($data['sender_city_id']) ? $data['sender_city_id'] : $merchant_associate->city_id;
            $sender_zone_id = ($merchant_associate->zone_id) ? $merchant_associate->zone_id : null;
            $sender_zone_id = isset($data['sender_zone_id']) ? $data['sender_zone_id'] : $sender_zone_id;

            $data['sender_city'] = $sender_city_id;
            $data['sender_zone'] = $sender_zone_id;
            $merchant = Merchant::find($pickup->sender_id);
            $is_transaction_fee = $merchant->is_transaction_fee;
            // Merchant Target Sale count
            $parcel_count = isset($data['parcels']) ? count($data['parcels']) : 1;
            $this->calculate_merchant_sale_count($merchant, $parcel_count);
            // calculate normal delivery amount and merchant discount for bus dropoff and normal voucher
            if ($merchant->is_discount) {
                $merchant_discount    = $this->get_merchant_discount($merchant, $data, 'Staff');
            }
            if ($merchant_discount) {
                $extra_or_discount   = $merchant_discount['extra_or_discount'];
            }
            //calculate warehousing_fee
            if (isset($data['postpone_date']) && $data['postpone_date']) {
                $warehousing_fee = $this->calculate_warehousing_fee($data['postpone_date']);
            }
        } else {
            $customer = Customer::find($pickup->sender_id);
            $sender_city_id     = isset($data['sender_city_id']) ? $data['sender_city_id'] : $customer->city_id;
            $sender_zone_id = ($customer->zone_id) ? $customer->zone_id : null;
            $sender_zone_id = isset($data['sender_zone_id']) ? $data['sender_zone_id'] : $sender_zone_id;
        }

        // Customer find or Create
        $customerRepository = new CustomerRepository();
        $customer = Customer::phone($data['receiver_phone'])->first();
        $receiver = [
            'name' => $data['receiver_name'],
            'phone' => $data['receiver_phone'],
            'city_id' => isset($data['receiver_city_id']) ? $data['receiver_city_id'] : ($customer ? $customer->city_id : null),
            'zone_id' => isset($data['receiver_zone_id']) ? $data['receiver_zone_id'] : ($customer ? $customer->zone_id : null),
            'other_phone' => isset($data['other_phone']) ? $data['other_phone'] : ($customer ? $customer->other_phone : null),
            'address' => isset($data['receiver_address']) ? $data['receiver_address'] : ($customer ? $customer->address : null),
            'latitude' => isset($data['receiver_latitude']) ? $data['receiver_latitude'] : ($customer ? $customer->latitude : null),
            'longitude' => isset($data['receiver_longitude']) ? $data['receiver_longitude'] : ($customer ? $customer->longitude : null),
            'email' => isset($data['receiver_email']) ? $data['receiver_email'] : ($customer ? $customer->email : null),
        ];
        $voucher_id = 0;
        if (Voucher::count()) {
            $voucher_id = Voucher::latest()->first()->id;
        }
        $voucher_id += 1;
        if ($customer) {
            setVoucherId($voucher_id);
            $customer = $customerRepository->update($customer, $receiver);
        } else {
            setVoucherId($voucher_id);
            $customer = $customerRepository->create($receiver);
        }
        $this->receiver_id = $customer->id;

        $receiver_city_id = isset($data['receiver_city_id']) ? $data['receiver_city_id'] : $customer->city_id;
        $route = Route::where('origin_id', $sender_city_id)->where('destination_id', $receiver_city_id)->firstOrFail();
        if ($bus_station) {
            $sender_gate_id = isset($data['sender_gate_id']) ? $data['sender_gate_id'] : null;
            $base_data = BusDropOff::where('gate_id', $sender_gate_id)->where('route_id', $route->id)->firstOrFail();
        }

        // Get route rate for CBM Formula
        if ($bus_station) {
            $bus_station_rate = 0;
            if (isset($data['sender_bus_station_id']) && $data['sender_bus_station_id']) {
                $bus_station_rate = BusStation::findOrFail($data['sender_bus_station_id'])->delivery_rate;
            }
        } else {
            $zone_rate = 0;
            $zone_agent_rate = 0;
            //$route_rate = $route->route_rate;
            if (isset($data['receiver_zone_id']) && $data['receiver_zone_id']) {
                $zone = Zone::findOrFail($data['receiver_zone_id']);
                if ($zone->city_id != $receiver_city_id) {
                    $responses = [
                        'status' => 2,
                        'message' => 'Please select related city and zone.'
                    ];
                    return $responses;
                }
                $zone_rate = ($sender_city_id == $receiver_city_id) ? $zone->zone_rate : $zone->diff_zone_rate;
                $zone_agent_rate = $zone->zone_agent_rate;
            }
        }
        if (isset($data['remark'])) {
            $note = getConvertedString($data['remark']);
        }

        // Checking D2D and BD off
        if (isset($data['parcels'])) {
            foreach ($data['parcels'] as $key => $par) {
                foreach($par['parcel_items'] as $item){
                    if($item['item_price'] > 10000000){
                        $responses = [
                            'status' => 2,
                            'message' => 'Over maximun amount to collect.'
                        ];
                        return $responses;
                    }
                }
                $cbm = GlobalScale::findOrFail($par['global_scale_id']);
                $data_weight = isset($par['weight']) ? $par['weight'] : 1;
                if ($data_weight > $cbm->max_weight) {
                    $responses = [
                        'status' => 2,
                        'message' => 'Over maximun weight.'
                    ];
                    return $responses;
                }
                if ($bus_station) {
                    $base_data = BusDropOff::where('gate_id', $sender_gate_id)->where('global_scale_id', $cbm->id)->firstOrFail();
                } else {
                    $base_data = DoorToDoor::where('route_id', $route->id)->where('global_scale_id', $cbm->id)->firstOrFail();
                }
            }
        } else {
            $cbm = GlobalScale::findOrFail($global_scale_id);
            $data_weight = isset($data['weight']) ? $data['weight'] : 1;
            if ($data_weight > $cbm->max_weight) {
                $responses = [
                    'status' => 2,
                    'message' => 'Over maximun weight.'
                ];
                return $responses;
            }
            if ($bus_station) {
                $sender_gate_id = isset($data['sender_gate_id']) ? $data['sender_gate_id'] : null;
                $base_data = BusDropOff::where('gate_id', $sender_gate_id)->where('global_scale_id', $cbm->id)->firstOrFail();
            } else {
                $base_data = DoorToDoor::where('route_id', $route->id)->where('global_scale_id', $cbm->id)->firstOrFail();
            }
        }

        //Create new Voucher
        $voucher = new Voucher();
        $voucher->receiver_id = $this->receiver_id;
        $voucher->pickup_id = $data['pickup_id'];
        // $voucher->voucher_invoice             = $voucher_no;
        $voucher->total_item_price = isset($data['total_item_price']) ? $data['total_item_price'] : 0;
        // $voucher->total_delivery_amount = $delivery_amount;
        // $voucher->total_amount_to_collect = 0;
        $voucher->payment_type_id = $data['payment_type_id'];
        $voucher->remark = isset($data['remark']) ? $note : null;
        $voucher->origin_city_id = $sender_city_id;
        $voucher->sender_city_id = $sender_city_id;
        $voucher->sender_zone_id = $sender_zone_id;
        $voucher->receiver_city_id = $receiver_city_id;
        $voucher->receiver_zone_id = isset($data['receiver_zone_id']) ? $data['receiver_zone_id'] : null;

        if ($bus_station) {
            $voucher->bus_station = isset($data['bus_station']) ? $data['bus_station'] : null;
            $voucher->sender_bus_station_id = isset($data['sender_bus_station_id']) ? $data['sender_bus_station_id'] : null;
            $voucher->receiver_bus_station_id = isset($data['receiver_bus_station_id']) ? $data['receiver_bus_station_id'] : null;
            $voucher->sender_gate_id = isset($data['sender_gate_id']) ? $data['sender_gate_id'] : null;
            $voucher->receiver_gate_id = isset($data['receiver_gate_id']) ? $data['receiver_gate_id'] : null;
            // $voucher->bus_credit = isset($data['bus_credit']) ? $data['bus_credit'] : 0;
            // $voucher->bus_fee = $bus_fee;
            // $voucher->deposit_amount = isset($data['deposit_amount']) ? $data['deposit_amount'] : 0;
        }
        $voucher->discount_id = $discount_id;
        $voucher->total_coupon_amount = $coupon_amount;
        $voucher->total_discount_amount = $discount_amount;
        $voucher->warehousing_fee = $warehousing_fee;

        $voucher->call_status_id = isset($data['call_status_id']) ? $data['call_status_id'] : 1;
        $voucher->delegate_duration_id = isset($data['delegate_duration_id']) ? $data['delegate_duration_id'] : null;
        $voucher->delegate_person = isset($data['delegate_person']) ? $data['delegate_person'] : null;
        $voucher->delivery_status_id = isset($data['delivery_status_id']) ? $data['delivery_status_id'] : 1;
        $voucher->store_status_id = isset($data['store_status_id']) ? $data['store_status_id'] : 1;
        $voucher->created_by_id = auth()->user()->id;
        $voucher->created_by_type = 'Merchant';
        $voucher->postpone_date = isset($data['postpone_date']) ? $data['postpone_date'] : null;
        $voucher->discount_type = ($extra_or_discount) ? 'extra' : null;
        $voucher->thirdparty_invoice = isset($data['thirdparty_invoice']) ? $data['thirdparty_invoice'] : null;
        $voucher->platform = isset($data['platform']) ? $data['platform'] : null;
        $voucher->save();
        $voucher->voucherPickupFire('new_voucher');
        $parcelRepository = new ParcelRepository();
        $parcelItemRepository = new ParcelItemRepository();

        if (isset($data['parcels'])) {
            $total_deli_amount = 0;
            $total_discount_amount = 0;
            $total_coupon_amount = 0;
            $total_price = 0;
            $total_bus_fee = 0;
            $total_agent_fee = 0;

            foreach ($data['parcels'] as $key => $par) {
                $cbm = GlobalScale::findOrFail($par['global_scale_id']);
                if ($bus_station) {
                    $base_data = BusDropOff::where('gate_id', $sender_gate_id)->where('global_scale_id', $cbm->id)->firstOrFail();
                    $weight = ($par['weight'] > $cbm->support_weight) ? ($par['weight'] - $cbm->support_weight) * $base_data->salt : 0;
                    $delivery_amount = $bus_station_rate;
                    $bus_fee         = $base_data->base_rate + $weight;
                } else {
                    $base_data = DoorToDoor::where('route_id', $route->id)->where('global_scale_id', $cbm->id)->firstOrFail();
                    $weight = ($par['weight'] > $cbm->support_weight) ? ($par['weight'] - $cbm->support_weight) * $base_data->salt : 0;
                    $delivery_amount = $base_data->base_rate + $weight + $zone_rate;
                    //Calculate Agent Rate
                    // if ($sender_city_id != $receiver_city_id) {
                    //     $weight = ($par['weight'] > $cbm->support_weight) ? ($par['weight'] - $cbm->support_weight) * $base_data->agent_salt : 0;
                    //     $agent_fee = $base_data->agent_base_rate + $weight + $zone_agent_rate;
                    // }
                    if ($sender_city_id != $receiver_city_id) {
                        $agent = City::find($receiver_city_id)->agent;
                        if ($agent) {
                            $weight = ($par['weight'] > $cbm->support_weight) ? ($par['weight'] - $cbm->support_weight) * $base_data->agent_salt : 0;
                            $agent_fee = $agent->delivery_commission + $weight + $zone_agent_rate;
                        }else{
                            $agent = City::find($sender_city_id)->agent;
                            if ($agent) {
                                $weight = ($par['weight'] > $cbm->support_weight) ? ($par['weight'] - $cbm->support_weight) * $base_data->agent_salt : 0;
                                $agent_fee = $agent->delivery_commission + $weight + $zone_agent_rate;
                            }
                        }
                    }
                }
                // Check coupon code is valid?
                if (isset($par['coupon_code']) && $par['coupon_code']) {
                    $couponRepository = new CouponRepository();
                    $coupon_arr['coupon_code'] = $par['coupon_code'];
                    $coupon_data = $couponRepository->valid_coupon_code($coupon_arr);
                }
                if ($coupon_data) {
                    $coupon_amount = $this->calculate_coupon_amount($coupon_data, $delivery_amount);
                    $coupon_associate_id = $coupon_data['associate_id'];
                }
                if (!$coupon_data && $pickup->sender_type == 'Merchant') {
                    if ($merchant_discount) {
                        $discount_amount    = $this->calculate_merchant_discount($merchant_discount, $delivery_amount);
                        $discount_id          = $merchant_discount['id'];
                        $discount_type_id     = $merchant_discount['discount_type_id'];
                    }
                }
                //Rouding Adjust
                $delivery_amount = computeRoundingAmount($delivery_amount);

                $cal_label_price = $this->calculate_lable_price($voucher->payment_type_id, $discount_amount, $parcel_total_price = 0, $delivery_amount, $coupon_amount, $bus_fee, $extra_or_discount);
                $par['coupon_price']   = $coupon_amount;
                $par['discount_type_id'] = $discount_type_id;
                $par['coupon_associate_id'] = $coupon_associate_id;
                $par['agent_fee'] = $agent_fee;

                $par['discount_price'] = $cal_label_price['discount_price'];
                //$par['cal_parcel_price'] = $cal_label_price['cal_parcel_price'];
                $par['cal_delivery_price'] = $cal_label_price['cal_delivery_price'];
                $par['cal_gate_price'] = $cal_label_price['cal_gate_price'];
                // $par['label_parcel_price'] = $cal_label_price['label_parcel_price'];
                $par['label_delivery_price'] = $cal_label_price['label_delivery_price'];
                $par['label_gate_price'] = $cal_label_price['label_gate_price'];
                $par['origin_lwh'] = isset($data['origin_lwh']) ? $data['origin_lwh'] : null;
                $par['origin_weight'] = isset($par['weight']) ? $par['weight'] : null;
                // $par['sub_total'] = $cal_label_price['sub_total'];

                $parcel = $parcelRepository->create($par, $voucher->id);

                $total_deli_amount += $parcel->cal_delivery_price;
                $total_coupon_amount += $parcel->coupon_price;
                $total_discount_amount += $parcel->discount_price;
                $total_bus_fee += $parcel->cal_gate_price;
                $total_agent_fee += $parcel->agent_fee;

                //update from valid to invalid for these associate_coupon_id(coupon code)
                if ($coupon_associate_id) {
                    $coupon_associate = CouponAssociate::find($coupon_associate_id);
                    $coupon_associate->update(['valid' => 0]);
                }

                $parcel_total_price = 0;
                foreach ($par["parcel_items"] as $item) {
                    $item = $parcelItemRepository->create($item, $parcel->id);
                    $parcel_total_price += $item->item_price * $item->item_qty;
                    $total_price        += $item->item_price * $item->item_qty;
                }
                $bus_total_price             = ($bus_station) ? 0 : $parcel_total_price;
                // $discount                    = ($extra_or_discount)?$parcel->delivery_amount + $parcel->discount_amount : $parcel->delivery_amount - $parcel->discount_amount;
                // $reduce_delivery             = ($coupon_data) ? $parcel->delivery_amount - $coupon_amount : $discount;
                // $parcel_amount_to_collect    = $this->calculate_amount_to_collect($voucher->payment_type_id, $bus_total_price, $reduce_delivery, $parcel->bus_fee);
                $cal_label_price = $this->calculate_lable_price($voucher->payment_type_id, $discount_amount, $parcel_total_price, $delivery_amount, $coupon_amount, $bus_fee, $extra_or_discount);
                $parcel->update([
                    'sub_total' => $cal_label_price['sub_total'],
                    'cal_parcel_price' => $cal_label_price['cal_parcel_price'],
                    'label_parcel_price' => $cal_label_price['label_parcel_price']
                ]);
                $parcel->save();
            }

            $bus_total_price = ($bus_station) ? 0 : $total_price;
            $discount = ($extra_or_discount) ? $total_deli_amount + $total_discount_amount : $total_deli_amount - $total_discount_amount;
            $total_reduce_delivery = ($coupon_data) ? $total_deli_amount - $total_coupon_amount : $discount;
            $collect_amount = $this->calculate_amount_to_collect($voucher->payment_type_id, $bus_total_price, $total_reduce_delivery, $total_bus_fee);

            $voucher->total_item_price = $total_price;
            $voucher->total_delivery_amount = $total_deli_amount;
            $voucher->total_coupon_amount = $total_coupon_amount;
            $voucher->total_amount_to_collect = $collect_amount;
            $voucher->total_discount_amount = $total_discount_amount;
            $voucher->total_bus_fee = $total_bus_fee;
            $voucher->total_agent_fee = $total_agent_fee;
            $voucher->save();
        } else {
            // Check coupon code is valid?
            if (isset($data['coupon_code']) && $data['coupon_code']) {
                $couponRepository = new CouponRepository();
                $coupon_arr['coupon_code'] = $data['coupon_code'];
                $coupon_data = $couponRepository->valid_coupon_code($coupon_arr);
            }
            if ($coupon_data) {
                $coupon_amount = $this->calculate_coupon_amount($coupon_data, $delivery_amount);
                $coupon_associate_id = $coupon_data['associate_id'];
            }
            if (!$coupon_data && $pickup->sender_type == 'Merchant') {
                if ($merchant_discount) {
                    $discount_amount    = $this->calculate_merchant_discount($merchant_discount, $delivery_amount);
                    $discount_id          = $merchant_discount['id'];
                    $discount_type_id     = $merchant_discount['discount_type_id'];
                }
            }

            $weight = isset($data['weight']) ? ($data['weight'] > $cbm->support_weight) ? ($data['weight'] - $cbm->support_weight) * $base_data->salt : 0 : 0;

            if ($bus_station) {
                $sender_gate_id = isset($data['sender_gate_id']) ? $data['sender_gate_id'] : null;
                $base_data = BusDropOff::where('gate_id', $sender_gate_id)->where('global_scale_id', $cbm->id)->firstOrFail();
                $delivery_amount = $bus_station_rate;
                $bus_fee         = $base_data->base_rate + $weight;
            } else {
                $base_data = DoorToDoor::where('route_id', $route->id)->where('global_scale_id', $cbm->id)->firstOrFail();
                $delivery_amount = $base_data->base_rate + $weight + $zone_rate;
                //Calculate Agent Rate
                if ($sender_city_id != $receiver_city_id) {
                    $agent_fee = $base_data->agent_base_rate + $weight + $zone_agent_rate;
                }
            }
            if ($coupon_data) {
                $coupon_amount = $this->calculate_coupon_amount($coupon_data, $delivery_amount);
                $coupon_associate_id = $coupon_data['associate_id'];
            }
            if (!$coupon_data && $pickup->sender_type == 'Merchant') {
                if ($merchant_discount) {
                    $discount_amount    = $this->calculate_merchant_discount($merchant_discount, $delivery_amount);
                    $discount_id          = $merchant_discount['id'];
                    $discount_type_id     = $merchant_discount['discount_type_id'];
                }
            }
            //Rouding Adjust
            $delivery_amount = computeRoundingAmount($delivery_amount);

            //$discount = ($extra_or_discount)? $delivery_amount + $discount_amount : $delivery_amount - $discount_amount;
            $parcel_item_price = isset($data['item_price']) ? $data['item_price'] : $voucher->total_item_price;
            $cal_label_price = $this->calculate_lable_price($voucher->payment_type_id, $discount_amount, $parcel_item_price, $delivery_amount, $coupon_amount, $bus_fee, $extra_or_discount);
            // $reduce_delivery_amount = ($coupon_data) ? $delivery_amount - $coupon_amount :$discount;
            // $sub_total = ($bus_station) ? $reduce_delivery_amount : $voucher->total_item_price + ($reduce_delivery_amount);

            $parcels = [
                'global_scale_id'      => $cbm->id,
                'discount_type_id'     => $discount_type_id,
                'coupon_associate_id'  => $coupon_associate_id,
                'weight'               => isset($data['weight']) ? $data['weight'] : $cbm->weight,
                'origin_lwh'           => isset($data['origin_lwh']) ? $data['origin_lwh'] : null,
                'origin_weight'        => isset($data['weight']) ? $data['weight'] : $cbm->weight,
                'coupon_price'         => $coupon_amount,
                'agent_fee'            => $agent_fee,

                'discount_price'       => $cal_label_price['discount_price'],
                'cal_parcel_price'     => $cal_label_price['cal_parcel_price'],
                'cal_delivery_price'   => $cal_label_price['cal_delivery_price'],
                'cal_gate_price'       => $cal_label_price['cal_gate_price'],
                'label_parcel_price'   => $cal_label_price['label_parcel_price'],
                'label_delivery_price' => $cal_label_price['label_delivery_price'],
                'label_gate_price'     => $cal_label_price['label_gate_price'],
                'sub_total'            => $cal_label_price['sub_total'],
            ];
            $parcel = $parcelRepository->create($parcels, $voucher->id);
            $parcel_item = [
                'item_name'     => isset($data['item_name']) ? $data['item_name'] : 'Item1',
                'item_qty'      => isset($data['item_qty']) ? $data['item_qty'] : 1,
                'item_price'    => $parcel_item_price,
                'item_status'   => isset($data['item_status']) ? $data['item_status'] : null,
            ];

            //update from valid to invalid for these associate_coupon_id(coupon code)
            if ($coupon_associate_id) {
                $coupon_associate = CouponAssociate::find($coupon_associate_id);
                $coupon_associate->update(['valid' => 0]);
            }

            $item = $parcelItemRepository->create($parcel_item, $parcel->id);

            $bus_total_price = ($bus_station) ? 0 : $item->item_price;

            $discount = ($extra_or_discount) ? $delivery_amount + $discount_amount : $delivery_amount - $discount_amount;
            $reduce_delivery = ($coupon_data) ? $delivery_amount - $coupon_amount : $discount;
            $collect_amount = $this->calculate_amount_to_collect($voucher->payment_type_id, $bus_total_price, $reduce_delivery, $bus_fee);

            $voucher->total_item_price = $item->item_price;
            $voucher->total_delivery_amount = $delivery_amount;
            $voucher->total_agent_fee = $agent_fee;
            $voucher->total_coupon_amount = $coupon_amount;
            $voucher->total_amount_to_collect = $collect_amount;
            $voucher->total_discount_amount = $discount_amount;
            $voucher->total_bus_fee = $bus_fee;
            $voucher->save();
        }

        if (!$bus_station) {
            // Calculate Transaction Fee
            if($is_transaction_fee){
                $transaction_amount  = getTransactionAmount();
                if (
                    $voucher->total_item_price >= $transaction_amount
                    && ($voucher->payment_type_id == 1 || $voucher->payment_type_id == 2
                        || $voucher->payment_type_id == 10)
                ) {
                    $transacount_count = $voucher->total_item_price / $transaction_amount;
                    $transaction_fee     = getTransactionFee();
                    $transaction_fee = $transaction_fee * (int) $transacount_count;

                    $voucher->transaction_fee = $transaction_fee;
                }
            }
            
            if (isset($data['take_insurance']) && $data['take_insurance']) {
                $insurance_fee  = getInsuranceFee();
                $insurance_fee  = $voucher->total_item_price * $insurance_fee / 100;
                $voucher->insurance_fee = $insurance_fee;
            }
            $total_extra_amount =  $transaction_fee + $insurance_fee + $warehousing_fee;
        }
        //calculate amount to collect for Sender and Receiver
        $bus_total_price = ($bus_station) ? 0 : $voucher->total_item_price;
        $discount = ($extra_or_discount) ? $voucher->total_delivery_amount + $voucher->total_discount_amount : $voucher->total_delivery_amount - $voucher->total_discount_amount;
        $total_reduce_delivery = ($voucher->total_coupon_amount > 0) ? $voucher->total_delivery_amount - $voucher->total_coupon_amount : $discount;

        $data = $this->calculate_atc_sender_receiver($voucher->payment_type_id, $bus_total_price, $total_reduce_delivery, $voucher->total_bus_fee, $total_extra_amount);
        $voucher->sender_amount_to_collect = $data['sender_amount_to_collect'];
        $voucher->receiver_amount_to_collect = $data['receiver_amount_to_collect'];

        $voucher->warehousing_fee = $warehousing_fee;
        $before_total_atc = $voucher->total_amount_to_collect; // + $total_extra_amount;
        $after_total_atc = computeRoundingAmount($before_total_atc);
        $voucher->total_amount_to_collect = $after_total_atc;
        $voucher->save();
        $responses = [
            'status' => 1,
            'message' => 'Successfully imported.',
            'data' => $voucher->refresh()
        ];
        return $responses;
    }

    /**
     * @param Voucher $voucher
     */
    public function destroy(Voucher $voucher)
    {
        $deleted = $this->deleteById($voucher->id);

        if ($deleted) {
            $voucher->deleted_by_type = 'Merchant';
            $voucher->deleted_by = auth()->user()->id;
            $voucher->save();
            $voucher->voucherPickupFire('delete_voucher');
        }
    }

    public function calculate_amount_to_collect($payment_type_id, $total_item_price, $total_delivery_amount, $bus_fee = 0)
    {
        switch ($payment_type_id) {
            case 1:
                $amount_to_collect = $total_item_price + $total_delivery_amount + $bus_fee;
                break;
            case 2:
                $amount_to_collect = $total_item_price + $bus_fee;
                break;
            case 3:
                $amount_to_collect = $total_delivery_amount + $bus_fee;
                break;
            case 4:
                $amount_to_collect = 0;
                break;
            case 5:
                $amount_to_collect = 0;
                break;
            case 6:
                $amount_to_collect = $bus_fee;
                break;
            case 7:
                $amount_to_collect = $total_delivery_amount + $bus_fee;
                break;
            case 8:
                $amount_to_collect = $total_item_price + $total_delivery_amount + $bus_fee;
                break;
            case 9:
                $amount_to_collect = 0;
                break;
            case 10:
                $amount_to_collect = $total_item_price + $bus_fee;
                break;
            default:
                $amount_to_collect = 0;
        }
        return $amount_to_collect;
    }

    public function calculate_atc_sender_receiver($payment_type_id, $total_item_price, $total_delivery_amount, $bus_fee, $total_extra_amount = 0)
    {
        switch ($payment_type_id) {
                // Normal Delivery
            case 1: //Sum Total
                return [
                    'sender_amount_to_collect' => 0,
                    'receiver_amount_to_collect' => $total_delivery_amount + $total_item_price + $bus_fee,
                ];
                break;

            case 2: //Net Total
                return [
                    'sender_amount_to_collect' => 0,
                    'receiver_amount_to_collect' => $total_item_price + $bus_fee,
                ];
                break;

            case 3: //Delivery Only
                return [
                    'sender_amount_to_collect' => 0,
                    'receiver_amount_to_collect' => $total_delivery_amount + $bus_fee,
                ];
                break;

            case 4: //Nothing to collect
                return [
                    'sender_amount_to_collect' => 0, //$total_delivery_amount + $total_extra_amount + $bus_fee,
                    'receiver_amount_to_collect' => 0,
                ];
                break;
            case 5: //Unpaid Delivery & Unpaid Bus fee
                return [
                    'sender_amount_to_collect' => 0,
                    'receiver_amount_to_collect' => 0,
                ];
                break;

            case 6: //Unpaid Delivery & Paid Bus fee
                return [
                    'sender_amount_to_collect' => $bus_fee,
                    'receiver_amount_to_collect' => 0,
                ];
                break;
                //bus Dropoff
            case 7: //Paid Delivery & Unpaid Bus fee
                return [
                    'sender_amount_to_collect' => $total_delivery_amount, //+ $bus_fee,
                    'receiver_amount_to_collect' => 0,
                ];
                break;
            case 8: //Paid Delivery & Bus fee
                return [
                    'sender_amount_to_collect' => $total_delivery_amount  + $bus_fee,
                    'receiver_amount_to_collect' => 0,
                ];
                break;
            case 9: //Prepaid NTC
                return [
                    'sender_amount_to_collect' => $total_delivery_amount,
                    'receiver_amount_to_collect' => 0,
                ];
                break;

            case 10: //Prepaid Collect
                return [
                    'sender_amount_to_collect' => $total_delivery_amount + $bus_fee,
                    'receiver_amount_to_collect' => $total_item_price,
                ];
                break;
        }
    }

    public function get_merchant_discount($merchant, array $data, $type)
    {
        $bus_station = isset($data['bus_station']) ? $data['bus_station'] : null;
        // calculate normal delivery amount and merchant discount for bus dropoff and normal voucher
        if ($bus_station) {
            $bus_stations = [
                'from_bus_station_id' => $data['sender_bus_station_id'],
                'to_bus_station_id' => $data['receiver_bus_station_id']
            ];
            //$delivery_amount = Gate::find($data['sender_gate_id'])->delivery_rate;
            $merchant_discounts = $merchant->merchant_discounts()->dropOff()->dropOffDiscount($bus_stations)->first();
            if (!$merchant_discounts) {
                $merchant_discounts = MerchantDiscount::dropOff()->dropOffGlobalDiscount($bus_stations)->first();
            }
        } else {
            $locations = [
                'receiver_zone_id' => isset($data['receiver_zone_id']) ? $data['receiver_zone_id'] : null,
                'sender_zone_id'   => isset($data['sender_zone']) ? $data['sender_zone'] : null,
                'receiver_city_id'     => $data['receiver_city_id'],
                'sender_city_id'       => $data['sender_city']
            ];
            //$delivery_amount = isset($data['receiver_zone_id']) ? Zone::find($data['receiver_zone_id'])->delivery_rate : City::find($data['receiver_city_id'])->delivery_rate;
            $merchant_discounts = $merchant->merchant_discounts()
                ->normalVoucher()->normalVoucherCityDiscount($locations)
                ->normalVoucherZoneDiscount($locations)->first();

            if (!$merchant_discounts) {
                $merchant_discounts = MerchantDiscount::normalVoucher()
                    ->normalVoucherCityGlobalDiscount($locations)
                    ->normalVoucherZoneGlobalDiscount($locations)->first();
            }
        }
        if ($merchant_discounts['platform'] == 'All' || $merchant_discounts['platform'] == 'Web'  || $type == 'Merchant') {
            return $merchant_discounts;
        }
        return null;
    }

    public function calculate_merchant_discount($merchant_discounts, $delivery_amount)
    {
        $discount_amount = 0;
        //calculate percentage and flat amount merchant discount
        if ($merchant_discounts) {
            if ($merchant_discounts->discount_type_id == 1) {
                $discount_amount      = $delivery_amount * ($merchant_discounts->amount / 100);
            } else {
                $discount_amount      = $merchant_discounts->amount;
            }
            //calculate extra or discount
            // if ($merchant_discounts->extra_or_discount) {
            //     $delivery_amount += $discount_amount;
            // } else {
            //     $delivery_amount -= $discount_amount;
            // }
            // $discount_data = [];
            // $merchant_discounts['discount_id'] = $discount_id;
            // $merchant_discounts['discount_type_id'] = $discount_type_id;
            //$merchant_discounts['discount_amount'] = ($discount_amount) ? $discount_amount : 0;
        }
        return $discount_amount;
    }

    public function calculate_warehousing_fee($date)
    {
        $amount = 0;
        $weeks = Carbon::parse(Carbon::now()->addWeeks(1))->format('Y-m-d');
        $one_week = Carbon::createFromFormat('Y-m-d H:s:i', $weeks . '00:00:00');

        $postpone_date    = Carbon::createFromFormat('Y-m-d H:s:i', $date . '00:00:00');
        if ($postpone_date > $one_week) {
            $diff_in_days = $postpone_date->diffInDays($one_week);
            $warehousing_fee  = getWarehousingFee();
            $amount = $diff_in_days * $warehousing_fee;
        }
        return $amount;
    }

    public function calculate_merchant_sale_count($merchant, $parcel_count)
    {
        $current_sale_count = $merchant->current_sale_count;
        $target_date_between = getVolumnTargetDateBetween();
        if ($target_date_between) {
            $total_sale_count = $current_sale_count + $parcel_count;
            $available_coupon = $merchant->available_coupon;
            $target_sale_count  = getTargetSaleCount();
            if ($total_sale_count > $target_sale_count) {
                $target_coupon     = getTargetCoupon();
                $merchant->update(['available_coupon' => $target_coupon, 'current_sale_count' => $total_sale_count - $target_sale_count]);
            } else {
                $merchant->update(['current_sale_count' => $current_sale_count + $parcel_count]);
            }
        }
        if (!$target_date_between && $current_sale_count > 0) {
            $merchant->update(['current_sale_count' => 0]);
        }
    }

    public function calculate_coupon_amount($coupon, $delivery_amount)
    {
        $coupon_amount = 0;
        if ($coupon->discount_type_id == 1) {
            $coupon_amount      = $delivery_amount * ($coupon->amount / 100);
        } else {
            $coupon_amount      = $coupon->amount;
        }

        return $coupon_amount;
    }

    public function calculate_lable_price($payment_type_id, $discount_amount, $total_item_price, $delivery_amount, $coupon_amount, $bus_fee, $extra)
    {
        $discount_price    = $discount_amount;
        $cal_parcel_price = $total_item_price;
        $cal_delivery_price = $delivery_amount;
        $cal_gate_price    = $bus_fee;
        $subtotal = $delivery_amount + $bus_fee;

        if ($coupon_amount > 0) {
            $label_delivery_price = $delivery_amount - $coupon_amount;
        } else {
            $label_delivery_price = ($extra) ? $delivery_amount + $discount_amount : $delivery_amount - $discount_amount;
        }
        switch ($payment_type_id) {
                // Normal Delivery
            case 1: //Sum Total
                $label_parcel_price    = $total_item_price;
                $label_gate_price = 0;

                return [
                    'discount_price' => $discount_price,
                    'cal_parcel_price' => $cal_parcel_price,
                    'cal_delivery_price' => $cal_delivery_price,
                    'cal_gate_price' => $cal_gate_price,
                    'label_parcel_price' => $label_parcel_price,
                    'label_delivery_price' => $label_delivery_price,
                    'label_gate_price' => $label_gate_price,
                    'sub_total' => $subtotal,
                ];
                break;

            case 2: //Net Total
                $label_parcel_price    = $total_item_price;
                $label_delivery_price = 0;
                $label_gate_price = 0;
                return [
                    'discount_price' => $discount_price,
                    'cal_parcel_price' => $cal_parcel_price,
                    'cal_delivery_price' => $cal_delivery_price,
                    'cal_gate_price' => $cal_gate_price,
                    'label_parcel_price' => $label_parcel_price,
                    'label_delivery_price' => $label_delivery_price,
                    'label_gate_price' => $label_gate_price,
                    'sub_total' => $subtotal,
                ];
                break;

            case 3: //Delivery Only
                $label_parcel_price    = 0;
                $label_gate_price = 0;
                return [
                    'discount_price' => $discount_price,
                    'cal_parcel_price' => $cal_parcel_price,
                    'cal_delivery_price' => $cal_delivery_price,
                    'cal_gate_price' => $cal_gate_price,
                    'label_parcel_price' => $label_parcel_price,
                    'label_delivery_price' => $label_delivery_price,
                    'label_gate_price' => $label_gate_price,
                    'sub_total' => $subtotal,
                ];
                break;

            case 4: //Nothing to collect
                $label_parcel_price    = 0;
                $label_delivery_price = 0;
                $label_gate_price = 0;
                $subtotal = 0;
                return [
                    'discount_price' => $discount_price,
                    'cal_parcel_price' => $cal_parcel_price,
                    'cal_delivery_price' => $cal_delivery_price,
                    'cal_gate_price' => $cal_gate_price,
                    'label_parcel_price' => $label_parcel_price,
                    'label_delivery_price' => $label_delivery_price,
                    'label_gate_price' => $label_gate_price,
                    'sub_total' => $subtotal,
                ];
                break;
            case 5: //Unpaid Delivery & Bus fee
                $label_parcel_price    = 0;
                $label_delivery_price = 0;
                $label_gate_price = 0;
                return [
                    'discount_price' => $discount_price,
                    'cal_parcel_price' => $cal_parcel_price,
                    'cal_delivery_price' => $cal_delivery_price,
                    'cal_gate_price' => $cal_gate_price,
                    'label_parcel_price' => $label_parcel_price,
                    'label_delivery_price' => $label_delivery_price,
                    'label_gate_price' => $label_gate_price,
                    'sub_total' => $subtotal,
                ];
                break;

            case 6: //Unpaid Delivery & Paid Bus fee
                $label_parcel_price    = 0;
                $label_delivery_price = 0;
                $label_gate_price = $bus_fee;
                return [
                    'discount_price' => $discount_price,
                    'cal_parcel_price' => $cal_parcel_price,
                    'cal_delivery_price' => $cal_delivery_price,
                    'cal_gate_price' => $cal_gate_price,
                    'label_parcel_price' => $label_parcel_price,
                    'label_delivery_price' => $label_delivery_price,
                    'label_gate_price' => $label_gate_price,
                    'sub_total' => $subtotal,
                ];
                break;
                //bus Dropoff
            case 7: //Paid Delivery & Unpaid Bus fee
                $label_parcel_price    = 0;
                $label_gate_price = 0;
                return [
                    'discount_price' => $discount_price,
                    'cal_parcel_price' => $cal_parcel_price,
                    'cal_delivery_price' => $cal_delivery_price,
                    'cal_gate_price' => $cal_gate_price,
                    'label_parcel_price' => $label_parcel_price,
                    'label_delivery_price' => $label_delivery_price,
                    'label_gate_price' => $label_gate_price,
                    'sub_total' => $subtotal,
                ];
                break;
            case 8: //Paid Delivery & Bus fee
                $label_parcel_price    = 0;
                $label_gate_price = $bus_fee;
                return [
                    'discount_price' => $discount_price,
                    'cal_parcel_price' => $cal_parcel_price,
                    'cal_delivery_price' => $cal_delivery_price,
                    'cal_gate_price' => $cal_gate_price,
                    'label_parcel_price' => $label_parcel_price,
                    'label_delivery_price' => $label_delivery_price,
                    'label_gate_price' => $label_gate_price,
                    'sub_total' => $subtotal,
                ];
                break;
            case 9: //Prepaid NTC
                $label_parcel_price    = 0;
                $label_gate_price = 0;
                return [
                    'discount_price' => $discount_price,
                    'cal_parcel_price' => $cal_parcel_price,
                    'cal_delivery_price' => $cal_delivery_price,
                    'cal_gate_price' => $cal_gate_price,
                    'label_parcel_price' => $label_parcel_price,
                    'label_delivery_price' => $label_delivery_price,
                    'label_gate_price' => $label_gate_price,
                    'sub_total' => $subtotal,
                ];
                break;

            case 10: //Prepaid Collect
                $label_parcel_price    = $total_item_price;
                $label_gate_price = 0;
                return [
                    'discount_price' => $discount_price,
                    'cal_parcel_price' => $cal_parcel_price,
                    'cal_delivery_price' => $cal_delivery_price,
                    'cal_gate_price' => $cal_gate_price,
                    'label_parcel_price' => $label_parcel_price,
                    'label_delivery_price' => $label_delivery_price,
                    'label_gate_price' => $label_gate_price,
                    'sub_total' => $subtotal,
                ];
                break;
        }
    }
}
