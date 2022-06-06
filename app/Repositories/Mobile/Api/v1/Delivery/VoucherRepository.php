<?php

namespace App\Repositories\Mobile\Api\v1\Delivery;

use App\Models\Gate;
use App\Models\Zone;
use App\Models\Route;
use App\Models\Parcel;
use App\Models\Pickup;
use App\Models\Voucher;
use App\Models\Customer;
use App\Models\Merchant;
use App\Models\DeliSheet;
use App\Models\BusDropOff;
use App\Models\BusStation;
use App\Models\DoorToDoor;
use App\Models\ParcelItem;
use App\Models\GlobalScale;
use App\Models\PaymentType;
use App\Models\BusSheetVoucher;
use App\Models\DeliSheetVoucher;
use App\Models\MerchantDiscount;
use App\Models\MerchantAssociate;
use App\Repositories\BaseRepository;
use Illuminate\Support\Facades\Storage;
use App\Repositories\Web\Api\v1\CouponRepository;
use App\Repositories\Web\Api\v1\ParcelRepository;
use App\Repositories\Web\Api\v1\CustomerRepository;
use App\Repositories\Web\Api\v1\ParcelItemRepository;
use App\Repositories\Mobile\Api\v1\Delivery\AttachmentRepository;
use App\Repositories\Mobile\Api\v1\Delivery\CalculateAmountRepository;

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
        //$pickup               = Pickup::find($data['pickup_id']);
        $bus_station          = isset($data['bus_station']) ? $data['bus_station'] : null;
        $warehousing_fee      = 0;
        $transaction_fee      = 0;
        $insurance_fee        = 0;
        $delivery_amount      = 0;
        $discount_amount      = 0;
        $coupon_amount        = 0;
        $discount_id          = null;
        $discount_type_id     = null;
        $coupon_associate_id  = null;
        $coupon_data          = null;
        $merchant_discount    = null;
        $extra_or_discount = 0;

        //Merchant or Custormer
        // $merchant_associate = MerchantAssociate::find($data['sender_associate_id']);
        $sender_city_id     = $data['sender_city_id'];
        // $sender_zone_id = ($merchant_associate->zone_id) ? $merchant_associate->zone_id : null;
        $sender_zone_id = isset($data['sender_zone_id']) ? $data['sender_zone_id'] : null;

        $data['sender_city'] = $sender_city_id;
        $data['sender_zone'] = $sender_zone_id;
        $merchant = Merchant::findOrFail($data['sender_id']);
        // Merchant Target Sale count
        $parcel_count = isset($data['parcels']) ? count($data['parcels']) : 1;
        $this->calculate_merchant_sale_count($merchant, $parcel_count);
        // calculate normal delivery amount and merchant discount for bus dropoff and normal voucher
        $merchant_discount    = $this->get_merchant_discount($merchant, $data);
        if ($merchant_discount) {
            $extra_or_discount   = $merchant_discount['extra_or_discount'];
        }

        //calculate warehousing_fee
        if (isset($data['postpone_date']) && $data['postpone_date']) {
            $warehousing_fee = $this->calculate_warehousing_fee($data['postpone_date']);
        }

        // Customer find or Create
        $customerRepository = new CustomerRepository();
        $customer = Customer::phone($data['receiver_phone'])->first();
        $receiver = [
            'name' => $data['receiver_name'],
            'phone' => $data['receiver_phone'],
            'other_phone' => isset($data['other_phone']) ? $data['other_phone'] : null,
            'address' => isset($data['receiver_address']) ? $data['receiver_address'] : null,
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
            if (isset($data['receiver_zone_id']) && $data['receiver_zone_id']) {
                $zone_rate = Zone::findOrFail($data['receiver_zone_id'])->zone_rate;
            }
        }
        if (isset($data['remark'])) {
            $remark = getConvertedString($data['remark']);
        }

        //Create new Voucher
        $voucher = new Voucher();
        $voucher->receiver_id = $this->receiver_id;
        $voucher->total_item_price = isset($data['total_item_price']) ? $data['total_item_price'] : 0;
        $voucher->payment_type_id = $data['payment_type_id'];
        $voucher->pickup_id = $data['pickup_id'];
        $voucher->remark = isset($data['remark']) ? $remark : null;
        $voucher->origin_city_id = ($sender_city_id != $receiver_city_id) ? $sender_city_id : null;
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
        $voucher->save();

        $parcelRepository = new ParcelRepository();
        $parcelItemRepository = new ParcelItemRepository();

        if (isset($data['parcels'])) {
            $total_deli_amount = 0;
            $total_discount_amount = 0;
            $total_coupon_amount = 0;
            $total_price = 0;
            $total_bus_fee = 0;

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
                if (!$coupon_data) {
                    if ($merchant_discount) {
                        $discount_amount    = $this->calculate_merchant_discount($merchant_discount, $delivery_amount);
                        $discount_id          = $merchant_discount['id'];
                        $discount_type_id     = $merchant_discount['discount_type_id'];
                    }
                }
                $cal_label_price = $this->calculate_lable_price($voucher->payment_type_id, $discount_amount, $parcel_total_price = 0, $delivery_amount, $coupon_amount, $bus_fee, $extra_or_discount);
                $par['coupon_price']   = $coupon_amount;
                $par['discount_type_id'] = $discount_type_id;
                $par['coupon_associate_id'] = $coupon_associate_id;

                $par['discount_price'] = $cal_label_price['discount_price'];
                //$par['cal_parcel_price'] = $cal_label_price['cal_parcel_price'];
                $par['cal_delivery_price'] = $cal_label_price['cal_delivery_price'];
                $par['cal_gate_price'] = $cal_label_price['cal_gate_price'];
                // $par['label_parcel_price'] = $cal_label_price['label_parcel_price'];
                $par['label_delivery_price'] = $cal_label_price['label_delivery_price'];
                $par['label_gate_price'] = $cal_label_price['label_gate_price'];
                // $par['sub_total'] = $cal_label_price['sub_total'];

                $parcel = $parcelRepository->create($par, $voucher->id);

                $total_deli_amount += $parcel->cal_delivery_price;
                $total_coupon_amount += $parcel->coupon_price;
                $total_discount_amount += $parcel->discount_price;
                $total_bus_fee += $parcel->cal_gate_price;

                $parcel_total_price = 0;
                foreach ($par["parcel_items"] as $item) {
                    $item = $parcelItemRepository->create($item, $parcel->id);
                    $parcel_total_price += $item->item_price;
                    $total_price        += $item->item_price;
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
            $voucher->save();
        } else {
            $global_scale_id = isset($data['global_scale_id']) ? $data['global_scale_id'] : 1;
            $global_scale = GlobalScale::findOrFail($global_scale_id);
            $weight = 0;
            if ($bus_station) {
                $sender_gate_id = isset($data['sender_gate_id']) ? $data['sender_gate_id'] : null;
                $base_data = BusDropOff::where('gate_id', $sender_gate_id)->where('global_scale_id', $global_scale->id)->firstOrFail();
                $delivery_amount = $bus_station_rate;
                $bus_fee         = $base_data->base_rate + $weight;
            } else {
                $base_data = DoorToDoor::where('route_id', $route->id)->where('global_scale_id', $global_scale->id)->firstOrFail();
                $delivery_amount = $base_data->base_rate + $weight + $zone_rate;
            }
            if ($coupon_data) {
                $coupon_amount = $this->calculate_coupon_amount($coupon_data, $delivery_amount);
                $coupon_associate_id = $coupon_data['associate_id'];
            }
            if (!$coupon_data) {
                if ($merchant_discount) {
                    $discount_amount    = $this->calculate_merchant_discount($merchant_discount, $delivery_amount);
                    $discount_id          = $merchant_discount['id'];
                    $discount_type_id     = $merchant_discount['discount_type_id'];
                }
            }
            //$discount = ($extra_or_discount)? $delivery_amount + $discount_amount : $delivery_amount - $discount_amount;
            $parcel_item_price = isset($data['item_price']) ? $data['item_price'] : $voucher->total_item_price;
            $cal_label_price = $this->calculate_lable_price($voucher->payment_type_id, $discount_amount, $parcel_item_price, $delivery_amount, $coupon_amount, $bus_fee, $extra_or_discount);
            // $reduce_delivery_amount = ($coupon_data) ? $delivery_amount - $coupon_amount :$discount;
            // $sub_total = ($bus_station) ? $reduce_delivery_amount : $voucher->total_item_price + ($reduce_delivery_amount);

            $parcels = [
                'global_scale_id'      => $global_scale->id,
                'discount_type_id'     => $discount_type_id,
                'coupon_associate_id'  => $coupon_associate_id,
                'weight'               => $global_scale->weight,
                'coupon_price'         => $coupon_amount,

                'discount_price' => $cal_label_price['discount_price'],
                'cal_parcel_price' => $cal_label_price['cal_parcel_price'],
                'cal_delivery_price' => $cal_label_price['cal_delivery_price'],
                'cal_gate_price' => $cal_label_price['cal_gate_price'],
                'label_parcel_price' => $cal_label_price['label_parcel_price'],
                'label_delivery_price' => $cal_label_price['label_delivery_price'],
                'label_gate_price' => $cal_label_price['label_gate_price'],
                'sub_total' => $cal_label_price['sub_total'],
            ];
            $parcel = $parcelRepository->create($parcels, $voucher->id);
            $parcel_item = [
                'item_name'     => isset($data['item_name']) ? $data['item_name'] : 'Item1',
                'item_qty'      => isset($data['item_qty']) ? $data['item_qty'] : 1,
                'item_price'    => $parcel_item_price,
                'item_status'   => isset($data['item_status']) ? $data['item_status'] : null,
            ];
            $item = $parcelItemRepository->create($parcel_item, $parcel->id);

            $bus_total_price = ($bus_station) ? 0 : $item->item_price;
            $reduce_delivery = ($coupon_data) ? $delivery_amount - $coupon_amount : $delivery_amount - $discount_amount;
            $collect_amount = $this->calculate_amount_to_collect($voucher->payment_type_id, $bus_total_price, $reduce_delivery, $bus_fee);

            $voucher->total_item_price = $item->item_price;
            $voucher->total_delivery_amount = $delivery_amount;
            $voucher->total_coupon_amount = $coupon_amount;
            $voucher->total_amount_to_collect = $collect_amount;
            $voucher->total_discount_amount = $discount_amount;
            $voucher->total_bus_fee = $bus_fee;
            $voucher->save();
        }
        // Calculate Transaction Fee
        $transaction_amount  = getTransactionAmount();
        if ($voucher->total_item_price >= $transaction_amount) {
            $transacount_count = $voucher->total_item_price / $transaction_amount;
            $transaction_fee     = getTransactionFee();
            $voucher->transaction_fee = $transaction_fee * (int) $transacount_count;
        }

        if (isset($data['take_insurance']) && $data['take_insurance']) {
            $insurance_fee  = getInsuranceFee();
            $insurance_fee  = $voucher->total_item_price * $insurance_fee / 100;
            $voucher->insurance_fee = $insurance_fee;
        }
        $total_extra_amount =  $transaction_fee + $insurance_fee + $warehousing_fee;
        //calculate amount to collect for Sender and Receiver
        $bus_total_price = ($bus_station) ? 0 : $voucher->total_item_price;
        $total_reduce_delivery = ($voucher->total_coupon_amount > 0) ? $voucher->total_delivery_amount - $voucher->total_coupon_amount : $voucher->total_delivery_amount - $voucher->total_discount_amount;
        $data = $this->calculate_atc_sender_receiver($voucher->payment_type_id, $bus_total_price, $total_reduce_delivery, $voucher->total_bus_fee, $total_extra_amount);
        $voucher->sender_amount_to_collect = $data['sender_amount_to_collect'];
        $voucher->receiver_amount_to_collect = $data['receiver_amount_to_collect'];

        $voucher->warehousing_fee = $warehousing_fee;
        // $before_total_atc = $voucher->total_amount_to_collect + $total_extra_amount;
        // $after_total_atc = computeRoundingAmount($before_total_atc);
        // $voucher->total_amount_to_collect = $after_total_atc;
        $voucher->save();
        return $voucher->refresh();
    }
    /**
     * @param Voucher $voucher
     * @param array   $data
     *
     * @return mixed
     */
    public function update(Voucher $voucher, array $data): Voucher
    {
        $extra_or_discount = 0;
        $bus_fee = 0;
        //$payment_type_id = isset($data['payment_type_id']) ? $data['payment_type_id'] : $voucher->payment_type_id;
        $sender_id       = isset($data['sender_id']) ? $data['sender_id'] : $voucher->sender_id;
        //$payment_type    = PaymentType::find($payment_type_id);
        //$pickup          = Pickup::find($pickup_id);
        $bus_station     = isset($data['bus_station']) ? $data['bus_station'] : $voucher->bus_station;

        $sender_gate_id = isset($data['sender_gate_id']) ? $data['sender_gate_id'] : $voucher->sender_gate_id;
        $receiver_gate_id = isset($data['receiver_gate_id']) ? $data['receiver_gate_id'] : $voucher->receiver_gate_id;
        $receiver_zone_id = isset($data['receiver_zone_id']) ? $data['receiver_zone_id'] : $voucher->receiver_zone_id;
        $sender_zone_id = isset($data['sender_zone_id']) ? $data['sender_zone_id'] : $voucher->sender_zone_id;
        $receiver_city_id = isset($data['receiver_city_id']) ? $data['receiver_city_id'] : $voucher->receiver_city_id;
        $sender_city_id = isset($data['sender_city_id']) ? $data['sender_city_id'] : $voucher->sender_city_id;
        $sender_bus_station_id  = isset($data['sender_bus_station_id']) ? $data['sender_bus_station_id'] : $voucher->sender_bus_station_id;

        $warehousing_fee = ($voucher->warehousing_fee) ? $voucher->warehousing_fee : 0;
        $transaction_fee = ($voucher->transaction_fee) ? $voucher->transaction_fee : 0;
        $insurance_fee = ($voucher->insurance_fee) ? $voucher->insurance_fee : 0;

        $discount_amount = $voucher->parcels->count() > 0 ? $voucher->parcels[0]->discount_price : 0;
        $coupon_amount   = 0;
        $discount_id     = null;
        $discount_type_id = null;
        $coupon_associate_id = $voucher->parcels->count() > 0 ? $voucher->parcels[0]->coupon_associate_id : null;
        $coupon_data = null;


        $merchant = Merchant::find($sender_id);

        $data['sender_city']     = $sender_city_id;
        $data['sender_zone'] = $sender_zone_id;
        // calculate normal delivery amount and merchant discount for bus dropoff and normal voucher
        $merchant_discount    = $this->get_merchant_discount($merchant, $data);
        if ($merchant_discount) {
            $extra_or_discount   = $merchant_discount['extra_or_discount'];
        }

        //calculate warehousing_fee
        if (isset($data['postpone_date']) && $data['postpone_date']) {
            $warehousing_fee = $this->calculate_warehousing_fee($data['postpone_date']);
        }
        $route = Route::where('origin_id', $sender_city_id)->where('destination_id', $receiver_city_id)->firstOrFail();
        // Get route rate for CBM Formula
        if ($bus_station) {
            $base_data = BusDropOff::where('gate_id', $sender_gate_id)->where('route_id', $route->id)->firstOrFail();
            $bus_station_rate = 0;
            if ($sender_bus_station_id) {
                $bus_station_rate = BusStation::find($sender_bus_station_id)->delivery_rate;
            }
        } else {
            $zone_rate = 0;
            if ($receiver_zone_id) {
                $zone_rate = Zone::find($receiver_zone_id)->zone_rate;
            }
        }
        setVoucherId($voucher->id);
        $customerRepository = new CustomerRepository();
        // $customer = Customer::phone($data['receiver_phone'])->first();
        $customer = Customer::findOrFail($voucher->receiver_id);
        $receiver = [
            'name'    => $data['receiver_name'],
            'phone'   => $data['receiver_phone'],
            'other_phone' => isset($data['other_phone']) ? $data['other_phone'] : null,
            'address' => isset($data['receiver_address']) ? $data['receiver_address'] : null,
        ];
        if ($customer) {
            setVoucherId($voucher->id);
            $customer = $customerRepository->update($customer, $receiver);
        } else {
            setVoucherId($voucher->id);
            $customer = $customerRepository->create($receiver);
        }

        $this->receiver_id = $customer->id;

        $voucher->receiver_id = $this->receiver_id;
        //$voucher->pickup_id = isset($data['pickup_id']) ? $data['pickup_id'] : $voucher->pickup_id;
        $voucher->remark = isset($data['remark']) ? $data['remark'] : $voucher->remark;
        $voucher->payment_type_id = isset($data['payment_type_id']) ? $data['payment_type_id'] : $voucher->payment_type_id;
        $voucher->sender_city_id = isset($data['sender_city_id']) ? $data['sender_city_id'] : $voucher->sender_city_id;
        $voucher->sender_zone_id = isset($data['sender_zone_id']) ? $data['sender_zone_id'] : $voucher->sender_zone_id;
        $voucher->origin_city_id = ($sender_city_id != $receiver_city_id) ? $sender_city_id : null;
        $voucher->receiver_city_id = isset($data['receiver_city_id']) ? $data['receiver_city_id'] : $voucher->receiver_city_id;
        $voucher->receiver_zone_id = isset($data['receiver_zone_id']) ? $data['receiver_zone_id'] : $voucher->receiver_zone_id;
        $voucher->bus_station = isset($data['bus_station']) ? $data['bus_station'] : $voucher->bus_station;
        $voucher->sender_bus_station_id = isset($data['sender_bus_station_id']) ? $data['sender_bus_station_id'] : $voucher->sender_bus_station_id;
        $voucher->receiver_bus_station_id = isset($data['receiver_bus_station_id']) ? $data['receiver_bus_station_id'] : $voucher->receiver_bus_station_id;
        $voucher->sender_gate_id = isset($data['sender_gate_id']) ? $data['sender_gate_id'] : $voucher->sender_gate_id;
        $voucher->receiver_gate_id = isset($data['receiver_gate_id']) ? $data['receiver_gate_id'] : $voucher->receiver_gate_id;
        $voucher->total_bus_fee = $voucher->total_bus_fee;
        $voucher->discount_id = $discount_id;
        $voucher->total_coupon_amount = $coupon_amount;
        $voucher->total_discount_amount = isset($data['total_discount_amount']) ? $data['total_discount_amount'] : $voucher->total_discount_amount;
        $voucher->call_status_id = isset($data['call_status_id']) ? $data['call_status_id'] : $voucher->call_status_id;
        $voucher->delivery_status_id = isset($data['delivery_status_id']) ? $data['delivery_status_id'] : $voucher->delivery_status_id;
        $voucher->store_status_id = isset($data['store_status_id']) ? $data['store_status_id'] : $voucher->store_status_id;
        $voucher->postpone_date = isset($data['postpone_date']) ? $data['postpone_date'] : $voucher->postpone_date;

        $voucher->delegate_duration_id = isset($data['delegate_duration_id']) ? $data['delegate_duration_id'] : $voucher->delegate_duration_id;
        $voucher->delegate_person = isset($data['delegate_person']) ? $data['delegate_person'] : $voucher->delegate_person;

        // $voucher->deposit_amount = $deposit_amount;
        $voucher->discount_type = ($extra_or_discount) ? 'extra' : null;

        if ($voucher->isDirty()) {
            $voucher->updated_by = auth()->user()->id;
            $voucher->save();
        }

        $parcelRepository = new ParcelRepository();
        $parcelItemRepository = new ParcelItemRepository();

        if (isset($data['deletedIds'])) {
            if (isset($data['deletedIds']['itemIds'])) {
                foreach ($data['deletedIds']["itemIds"] as $item_id) {
                    $item_data = ParcelItem::findOrFail($item_id);
                    $parcelItemRepository->destroy($item_data);
                }
            }

            if (isset($data['deletedIds']['parcelIds'])) {
                foreach ($data['deletedIds']["parcelIds"] as $parcel_id) {
                    $parcel_data = Parcel::findOrFail($parcel_id);
                    $parcelRepository->destroy($parcel_data);
                }
            }
        }

        if (isset($data['parcels'])) {
            $total_deli_amount = 0;
            $total_discount_amount = 0;
            $total_coupon_amount = 0;
            $total_price = 0;
            $total_bus_fee = 0;

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
                }
                // Check coupon code is valid?
                if (isset($par['coupon_code']) && $par['coupon_code']) {
                    $couponRepository = new CouponRepository();
                    $coupon_arr['coupon_code'] = $par['coupon_code'];
                    $coupon_data = $couponRepository->valid_coupon_code($coupon_arr);
                } else {
                    $coupon_data = null;
                }
                if ($coupon_data) {
                    $coupon_amount = $this->calculate_coupon_amount($coupon_data, $delivery_amount);
                    $coupon_associate_id = $coupon_data['associate_id'];
                } else {
                    $coupon_amount = 0;
                }
                if (!$coupon_data) {
                    if ($merchant_discount) {
                        $discount_amount    = $this->calculate_merchant_discount($merchant_discount, $delivery_amount);
                        $discount_id          = $merchant_discount['id'];
                        $discount_type_id     = $merchant_discount['discount_type_id'];
                    }
                } else {
                    $discount_amount    = 0;
                    $discount_id          = null;
                    $discount_type_id     = null;
                }

                $cal_label_price = $this->calculate_lable_price($voucher->payment_type_id, $discount_amount, $parcel_total_price = 0, $delivery_amount, $coupon_amount, $bus_fee, $extra_or_discount);
                $par['coupon_price']   = $coupon_amount;
                $par['discount_type_id'] = $discount_type_id;
                $par['coupon_associate_id'] = $coupon_associate_id;
                $par['discount_price'] = $cal_label_price['discount_price'];
                //$par['cal_parcel_price'] = $cal_label_price['cal_parcel_price'];
                $par['cal_delivery_price'] = $cal_label_price['cal_delivery_price'];
                $par['cal_gate_price'] = $cal_label_price['cal_gate_price'];
                // $par['label_parcel_price'] = $cal_label_price['label_parcel_price'];
                $par['label_delivery_price'] = $cal_label_price['label_delivery_price'];
                $par['label_gate_price'] = $cal_label_price['label_gate_price'];
                // $par['sub_total'] = $cal_label_price['sub_total'];

                if (isset($par['id']) && isset($par['is_delete']) && $par['is_delete'] == true) {
                    $parcel_data = Parcel::findOrFail($par['id']);
                    $parcelRepository->destroy($parcel_data);
                } else {
                    if (isset($par['id'])) {
                        $parcel_data = Parcel::findOrFail($par['id']);
                        $parcel = $parcelRepository->update($parcel_data, $par);

                        $total_deli_amount += $parcel->cal_delivery_price;
                        $total_discount_amount += $parcel->discount_price;
                        $total_coupon_amount += $parcel->coupon_price;
                        $total_bus_fee += $parcel->cal_gate_price;

                        $parcel_total_price = 0;

                        foreach ($par["parcel_items"] as $item) {
                            if (isset($item['is_delete'])) {
                                $item_data = ParcelItem::findOrFail($item['id']);
                                $parcelItemRepository->destroy($item_data);
                            } else {
                                if (isset($item['id'])) {
                                    $item_data = ParcelItem::findOrFail($item['id']);
                                    $parcel_item = $parcelItemRepository->update($item_data, $item);
                                } else {
                                    $parcel_item = $parcelItemRepository->create($item, $parcel->id);
                                }
                                $parcel_total_price += $parcel_item->item_price;
                                $total_price += $parcel_item->item_price;
                            }
                        }
                        // $bus_total_price = ($bus_station) ? 0 : $parcel_total_price;
                        // $discount = ($extra_or_discount)?$parcel->delivery_amount+$parcel->discount_amount : $parcel->delivery_amount-$parcel->discount_amount;
                        $cal_label_price = $this->calculate_lable_price($voucher->payment_type_id, $discount_amount, $parcel_total_price, $delivery_amount, $coupon_amount, $bus_fee, $extra_or_discount);
                        $parcel->update([
                            'sub_total' => $cal_label_price['sub_total'],
                            'cal_parcel_price' => $cal_label_price['cal_parcel_price'],
                            'label_parcel_price' => $cal_label_price['label_parcel_price']
                        ]);
                        $parcel->save();
                    } else {
                        // $par['delivery_amount'] = $delivery_amount;
                        // $par['discount_amount'] = $discount_amount;
                        // $par['coupon_amount'] = $coupon_amount;
                        // $par['discount_type_id'] = $discount_type_id;
                        // $par['coupon_associate_id'] = $coupon_associate_id;
                        $parcel = $parcelRepository->create($par, $voucher->id);

                        $total_deli_amount += $parcel->cal_delivery_price;
                        $total_discount_amount += $parcel->discount_price;
                        $total_coupon_amount += $parcel->coupon_price;
                        $total_bus_fee += $parcel->cal_gate_price;

                        $parcel_total_price = 0;

                        foreach ($par["parcel_items"] as $item) {
                            $parcel_item = $parcelItemRepository->create($item, $parcel->id);
                            $parcel_total_price += $parcel_item->item_price;
                            $total_price += $parcel_item->item_price;
                        }
                        // $bus_total_price = ($bus_station) ? 0 : $parcel_total_price;
                        // $discount = ($extra_or_discount)?$parcel->delivery_amount-$parcel->discount_amount : $parcel->delivery_amount-$parcel->discount_amount;
                        $cal_label_price = $this->calculate_lable_price($voucher->payment_type_id, $discount_amount, $parcel_total_price, $delivery_amount, $coupon_amount, $bus_fee, $extra_or_discount);
                        $parcel->update([
                            'sub_total' => $cal_label_price['sub_total'],
                            'cal_parcel_price' => $cal_label_price['cal_parcel_price'],
                            'label_parcel_price' => $cal_label_price['label_parcel_price']
                        ]);
                        $parcel->save();
                    }
                }
            }
            $bus_total_price = ($bus_station) ? 0 : $total_price;
            $discount = ($extra_or_discount) ? $total_deli_amount + $total_discount_amount : $total_deli_amount - $total_discount_amount;
            $reduce_delivery_amount = ($total_coupon_amount) ? $total_deli_amount - $total_coupon_amount : $discount;
            $collect_amount = $this->calculate_amount_to_collect($voucher->payment_type_id, $bus_total_price, $reduce_delivery_amount, $total_bus_fee);

            $voucher->total_item_price = $total_price;
            $voucher->total_delivery_amount = $total_deli_amount;
            $voucher->total_coupon_amount = $total_coupon_amount;
            $voucher->total_amount_to_collect = $collect_amount;
            $voucher->total_discount_amount = $total_discount_amount;
            $voucher->total_bus_fee = $total_bus_fee;
            $voucher->save();
        } else {
            $discount = ($extra_or_discount) ? $voucher->total_delivery_amount + $voucher->total_discount_amount : $voucher->total_delivery_amount - $voucher->total_discount_amount;
            $reduce_delivery_amount = ($voucher->total_coupon_amount) ? $voucher->total_delivery_amount - $voucher->total_coupon_amount : $discount;
            $collect_amount = $this->calculate_amount_to_collect($voucher->payment_type_id, $voucher->total_item_price, $reduce_delivery_amount, $voucher->total_bus_fee);
            $voucher->total_amount_to_collect = $collect_amount;
            $voucher->save();
        }
        $voucher->refresh();
        // Calculate Transaction Fee
        $transaction_amount  = getTransactionAmount();
        if ($voucher->total_item_price >= $transaction_amount) {
            $transaction_fee     = getTransactionFee();
            $voucher->transaction_fee = $transaction_fee;
        }
        // Calculate Insurance Fee
        if (isset($data['take_insurance']) && $data['take_insurance']) {
            $insurance_fee  = getInsuranceFee();
            $insurance_fee  = $voucher->total_item_price * $insurance_fee / 100;
            $voucher->insurance_fee = $insurance_fee;
        } else {
            $insurance_fee = 0;
            $voucher->insurance_fee = 0;
        }
        $total_extra_amount =  $transaction_fee + $insurance_fee + $warehousing_fee;
        //calculate amount to collect for Sender and Receiver
        $discount = ($extra_or_discount) ? $voucher->total_delivery_amount + $voucher->total_discount_amount : $voucher->total_delivery_amount - $voucher->total_discount_amount;
        $total_reduce_delivery = ($voucher->total_coupon_amount > 0) ? $voucher->total_delivery_amount - $voucher->total_coupon_amount : $discount;
        $data = $this->calculate_atc_sender_receiver($voucher->payment_type_id, $voucher->total_item_price, $total_reduce_delivery, $voucher->total_bus_fee, $total_extra_amount);
        $voucher->sender_amount_to_collect = $data['sender_amount_to_collect'];
        $voucher->receiver_amount_to_collect = $data['receiver_amount_to_collect'];

        $voucher->warehousing_fee = $warehousing_fee;
        // $before_total_atc = $voucher->total_amount_to_collect + $total_extra_amount;
        // $after_total_atc = computeRoundingAmount($before_total_atc);
        // $voucher->total_amount_to_collect = $after_total_atc;

        $voucher->save();
        return $voucher->refresh();
    }

    /**
     * @param Voucher $voucher
     */
    public function update_status(Voucher $voucher, array $data): Voucher
    {
        $voucher->delivery_status_id = $data['delivery_status_id'];
        // record mobile action
        $Sheet = $voucher->delisheets()->latest()->first();
        $Sheet->is_came_from_mobile = 1;
        $Sheet->actby_mobile = auth()->user()->id;
        $Sheet->save();
        $delisheet_voucher = DeliSheetVoucher::where('delisheet_id', $Sheet->id)
                ->where('voucher_id', $voucher->id)->firstOrFail();

        if ($data['delivery_status_id'] == 8) {
            $voucher->delivered_date = date('Y-m-d H:i:s');
            $delisheet_voucher->delivery_status = 1;
            $delisheet_voucher->is_came_from_mobile = 1;
            
        }
        if ($data['delivery_status_id'] == 10) {
            $delisheet_voucher->cant_deliver = 1;
        }

        if ($delisheet_voucher->isDirty()) {
            $delisheet_voucher->updated_by = auth()->user()->id;
            $delisheet_voucher->save();
        }

        if (isset($data['note']) && $data['note']) {
            $separated_note = str_replace(',', '|', $data['note']);
            $note = getConvertedString($separated_note);

            if (isset($data['delisheet_id']) && $data['delisheet_id']) {
                $delisheet = DeliSheetVoucher::where('delisheet_id', $data['delisheet_id'])
                    ->where('voucher_id', $voucher->id)->firstOrFail();
                $delisheet->note = $note;
                $delisheet->is_came_from_mobile = 1;
                if ($data['delivery_status_id'] == 9) {
                    // $delisheet->delivery_status = 1;
                    $delisheet->delivery_status = 0;
                }

                if ($delisheet->isDirty()) {
                    $delisheet->updated_by = auth()->user()->id;
                    $delisheet->save();
                }
            }
            if (isset($data['bussheet_id']) && $data['bussheet_id']) {
                $bussheet = BusSheetVoucher::where('bussheet_id', $data['bussheet_id'])
                    ->where('voucher_id', $voucher->id)->firstOrFail();
                $bussheet->note = $note;
                $bussheet->actual_bus_fee = $data['actual_bus_fee'];
                if ($bussheet->isDirty()) {
                    $bussheet->updated_by = auth()->user()->id;
                    $bussheet->save();
                }
            }
        }
        // upload attachment
        $file_data = [
            'voucher_id' => $voucher->id,
            'file' => isset($data['file']) ? $data['file'] : null,
            'note' => isset($data['note']) ? $note : null,
            'latitude' => isset($data['latitude']) ? $data['latitude'] : null,
            'longitude' => isset($data['longitude']) ? $data['longitude'] : null,
            'is_sign' => 1,
        ];
        $attachmentRepository = new AttachmentRepository();
        $attachment = $attachmentRepository->create($file_data);

        if ($voucher->isDirty()) {
            // $calculate_pointRepository = new CalculateAmountRepository();
            // $points = $calculate_pointRepository->calculate_points($voucher);

            // $staff = auth()->user();
            // $staff->points += $points;
            // $staff->save();

            $voucher->updated_by = auth()->user()->id;
            $voucher->save();
        }

        return $voucher->refresh();
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
                    'receiver_amount_to_collect' => $total_delivery_amount + $total_extra_amount + $total_item_price + $bus_fee,
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
                    'receiver_amount_to_collect' => $total_delivery_amount + $total_extra_amount + $bus_fee,
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
                    'sender_amount_to_collect' => $bus_fee + $total_extra_amount,
                    'receiver_amount_to_collect' => 0,
                ];
                break;
                //bus Dropoff
            case 7: //Paid Delivery & Unpaid Bus fee
                return [
                    'sender_amount_to_collect' => $total_delivery_amount + $total_extra_amount, //+ $bus_fee,
                    'receiver_amount_to_collect' => 0,
                ];
                break;
            case 8: //Paid Delivery & Bus fee
                return [
                    'sender_amount_to_collect' => $total_delivery_amount + $total_extra_amount + $bus_fee,
                    'receiver_amount_to_collect' => 0,
                ];
                break;
            case 9: //Prepaid NTC
                return [
                    'sender_amount_to_collect' => $total_delivery_amount + $total_extra_amount + $bus_fee,
                    'receiver_amount_to_collect' => 0,
                ];
                break;

            case 10: //Prepaid Collect
                return [
                    'sender_amount_to_collect' => $total_delivery_amount + $total_extra_amount  + $bus_fee,
                    'receiver_amount_to_collect' => $total_item_price,
                ];
                break;
        }
    }

    public function get_merchant_discount($merchant, array $data)
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
        return $merchant_discounts;
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
