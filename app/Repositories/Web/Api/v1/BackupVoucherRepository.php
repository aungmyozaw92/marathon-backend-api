<?php

namespace App\Repositories\Web\Api\v1;

use App\Models\Gate;
use App\Models\Parcel;
use App\Models\Pickup;
use App\Models\Voucher;
use App\Models\Customer;
use App\Models\Merchant;
use App\Models\Zone;
use App\Models\ParcelItem;
use App\Models\PaymentType;
use App\Models\MerchantDiscount;
use App\Models\MerchantAssociate;
use App\Repositories\BaseRepository;
use App\Repositories\Web\Api\v1\ParcelRepository;
use App\Repositories\Web\Api\v1\CustomerRepository;
use App\Repositories\Web\Api\v1\ParcelItemRepository;

class BackupVoucherRepository extends BaseRepository
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
        $item_price = $data['total_item_price'];
        $payment_type = PaymentType::find($data['payment_type_id']);
        $pickup = Pickup::find($data['pickup_id']);

        $bus_station = isset($data['bus_station'])? $data['bus_station']:null;
        $delivery_amount = 0;
        $discount_amount = 0;
        $discount_id = null;

        if ($pickup->sender_type == 'Merchant') {
            $merchant_associate = MerchantAssociate::find($pickup->sender_associate_id);
            $sender_city_id     = $merchant_associate->city_id;
            $sender_zone_id = ($merchant_associate->zone_id)? $merchant_associate->zone_id:null;

            $merchant = Merchant::find($pickup->sender_id);
            $merchant_discounts = MerchantDiscount::where('merchant_id', $merchant->id)->get();

            if ($bus_station) {
                if ($merchant->fix_dropoff_price > 0) {
                    $delivery_amount = $merchant->fix_dropoff_price;
                } else {
                    $delivery_amount = Gate::find($data['sender_gate_id'])->delivery_rate;
                }
            } else {
                if ($merchant->fix_delivery_price > 0) {
                    $delivery_amount = $merchant->fix_delivery_price;
                } else {
                    $delivery_amount = isset($data['receiver_zone_id']) ? Zone::find($data['receiver_zone_id'])->delivery_rate : City::find($data['receiver_city_id'])->delivery_rate;
                }
            }
            

            foreach ($merchant_discounts as $mer_discount) {
                if ($mer_discount->discount_type_id == 3) {
                    if ($merchant->foc == 0) {
                        ++$mer_discount->current_counter;
                    }
                    if ($mer_discount->current_counter > $mer_discount->target_sale_count) {
                        $mer_discount->current_counter -= $mer_discount->target_sale_count;

                        $merchant->foc = $mer_discount->foc_count;
                        $merchant->save();
                        $merchant->refresh();
                    }
                    $mer_discount->save();
                }
            }

            if ($merchant->foc > 0) {
                $discount_amount = $delivery_amount;
                $delivery_amount = 0;
                --$merchant->foc; //reduce foc_count 1
                $merchant->save();
                if ($merchant->foc == 0) {
                    $m_discount = MerchantDiscount::where('merchant_id', $merchant->id)->where('discount_type_id', 3)->first();
                    $m_discount->update(['current_counter' => 0]);
                    $discount_id = $m_discount->id;
                }
            } else {
                foreach ($merchant_discounts as $mer_discount) {
                    if (!($merchant->fix_dropoff_price > 0 || $merchant->fix_delivery_price > 0)) {
                        if ($mer_discount->discount_type_id == 1) {
                            $discount_amount = $delivery_amount * $mer_discount->amount / 100;
                            $delivery_amount -= $discount_amount;
                            $discount_id = $mer_discount->id;
                        } elseif ($mer_discount->discount_type_id == 2) {
                            $discount_amount = $mer_discount->amount;
                            $delivery_amount -= $discount_amount;
                            $discount_id = $mer_discount->id;
                        }
                    }
                    if ($mer_discount->discount_type_id == 4) {
                        if ($bus_station) {
                            // if ($mer_discount->sender_city_id == $data['sender_city_id'] && $mer_discount->receiver_city_id == $data['receiver_city_id']) {
                            //     $discount_amount = $mer_discount->amount;
                            //     $delivery_amount -= $discount_amount;
                            //     $discount_id = $mer_discount->id;
                            // }
                        } else {
                            if ($mer_discount->sender_city_id == $sender_city_id && $mer_discount->receiver_city_id == $data['receiver_city_id']) {
                                $discount_amount = $mer_discount->amount;
                                $delivery_amount -= $discount_amount;
                                $discount_id = $mer_discount->id;
                            }
                        }
                    }
                }
            }
        } else {
            $customer = Customer::find($pickup->sender_id);
           
            $sender_city_id     = $customer->city_id;
            $sender_zone_id = ($customer->zone_id)?$customer->zone_id:null;

            if ($bus_station) {
                $delivery_amount = Gate::find($data['sender_gate_id'])->delivery_rate;
            } else {
                $delivery_amount = isset($data['receiver_zone_id']) ? Zone::find($data['receiver_zone_id'])->delivery_rate : City::find($data['receiver_city_id'])->delivery_rate;
            }
        }
        //calculate total amount to collect
        $deposit_amount = isset($data['deposit_amount']) ? $data['deposit_amount'] : 0;
        $amount_to_collect = $this->calculate_amount_to_collect($payment_type->id, $item_price, $delivery_amount, $deposit_amount);

        // Customer find or Create
        $customerRepository = new CustomerRepository();
        $customer = Customer::phone($data['receiver_phone'])->first();
        $receiver = [
                'name' => $data['receiver_name'],
                'phone' => $data['receiver_phone'],
                'address' => $data['receiver_address'],
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
       
        //Create new Voucher
        $voucher = new Voucher();

        $voucher->receiver_id = $this->receiver_id;
        $voucher->pickup_id = $data['pickup_id'];
        // $voucher->voucher_invoice             = $voucher_no;
        $voucher->total_item_price = $data['total_item_price'];
        $voucher->total_delivery_amount = $delivery_amount;
        $voucher->total_amount_to_collect = $amount_to_collect;
        $voucher->payment_type_id = $data['payment_type_id'];
        $voucher->remark = isset($data['remark']) ? $data['remark'] : null;
        $voucher->sender_city_id = $sender_city_id;
        $voucher->sender_zone_id = $sender_zone_id;
        $voucher->receiver_city_id = isset($data['receiver_city_id']) ? $data['receiver_city_id'] : null;
        $voucher->receiver_zone_id = isset($data['receiver_zone_id']) ? $data['receiver_zone_id'] : null;
        
        if ($bus_station) {
            $voucher->bus_station = isset($data['bus_station']) ? $data['bus_station'] : null;
            $voucher->sender_bus_station_id = isset($data['sender_bus_station_id']) ? $data['sender_bus_station_id'] : null;
            $voucher->receiver_bus_station_id = isset($data['receiver_bus_station_id']) ? $data['receiver_bus_station_id'] : null;
            $voucher->sender_gate_id = isset($data['sender_gate_id']) ? $data['sender_gate_id'] : null;
            $voucher->receiver_gate_id = isset($data['receiver_gate_id']) ? $data['receiver_gate_id'] : null;
            $voucher->bus_credit = isset($data['bus_credit']) ? $data['bus_credit'] : 0;
            $voucher->bus_fee = isset($data['bus_fee']) ? $data['bus_fee'] : 0;
            $voucher->deposit_amount = isset($data['deposit_amount']) ? $data['deposit_amount'] : 0;
        }
        $voucher->discount_id = $discount_id;
        $voucher->coupon_id = isset($data['coupon_id']) ? $data['coupon_id'] : null;
        $voucher->discount_amount = $discount_amount;

        $voucher->call_status_id = isset($data['call_status_id']) ? $data['call_status_id'] : 1;
        $voucher->delivery_status_id = isset($data['delivery_status_id']) ? $data['delivery_status_id'] : 1;
        $voucher->store_status_id = isset($data['store_status_id']) ? $data['store_status_id'] : 1;
        $voucher->created_by = auth()->user()->id;

        $voucher->save();
        
        $parcelRepository = new ParcelRepository();
        $parcelItemRepository = new ParcelItemRepository();

        if (isset($data['parcels'])) {
            $total_deli_amount = 0 ;
            $total_price = 0;

            foreach ($data['parcels'] as $key => $par) {
                $par['delivery_amount'] = $voucher->total_delivery_amount;
                $par['discount_amount'] = $voucher->discount_amount;

                $parcel = $parcelRepository->create($par, $voucher->id);

                $total_deli_amount += $parcel->delivery_amount;

                $parcel_total_price = 0;
                foreach ($par["parcel_items"] as $item) {
                    $item = $parcelItemRepository->create($item, $parcel->id);
                    $parcel_total_price += $item->item_price;
                    $total_price        += $item->item_price;
                }

                $parcel_amount_to_collect = $this->calculate_amount_to_collect($voucher->payment_type_id, $parcel_total_price, $total_deli_amount);
                $parcel->update(['amount_to_collect' => $parcel_amount_to_collect]);
                $parcel->save();
            }
            $collect_amount = $this->calculate_amount_to_collect($voucher->payment_type_id, $total_price, $total_deli_amount);
            $voucher->total_item_price = $total_price ;
            $voucher->total_delivery_amount = $total_deli_amount;
            $voucher->total_amount_to_collect = $collect_amount;
            $voucher->save();
        } else {
            $parcels = [
                'global_scale_id'   => isset($data['global_scale_id']) ? $data['global_scale_id'] : 1,
                'delivery_amount'   => $voucher->total_delivery_amount,
                'discount_amount'   => $voucher->discount_amount,
                'amount_to_collect' => $voucher->total_amount_to_collect,
                'weight'            => isset($data['weight']) ? $data['weight'] : 0,
            ];

            $parcel = $parcelRepository->create($parcels, $voucher->id);

            $parcel_item = [
                'item_name'     => isset($data['item_name']) ? $data['item_name'] : 'Parcel',
                'item_qty'      => isset($data['item_qty']) ? $data['item_qty'] : 1,
                'item_price'    => isset($data['item_price']) ? $data['item_price'] : $voucher->total_item_price,
                'item_status'   => isset($data['item_status']) ? $data['item_status'] : null,
            ];

            $item = $parcelItemRepository->create($parcel_item, $parcel->id);
        }

        return $voucher;
    }

    /**
     * @param Voucher $voucher
     * @param array   $data
     *
     * @return mixed
     */
    public function update(Voucher $voucher, array $data): Voucher
    {
        $payment_type_id = isset($data['payment_type_id']) ? $data['payment_type_id'] : $voucher->payment_type_id;
        $pickup_id       = isset($data['pickup_id']) ? $data['pickup_id'] : $voucher->pickup_id;
        $payment_type    = PaymentType::find($payment_type_id);
        $pickup          = Pickup::find($pickup_id);
        $bus_station     = isset($data['bus_station']) ? $data['bus_station'] : $voucher->bus_station;

        $sender_gate_id        = isset($data['sender_gate_id'])? $data['sender_gate_id']:$voucher->sender_gate_id;
        $receiver_zone_id  = isset($data['receiver_zone_id'])? $data['receiver_zone_id']:$voucher->receiver_zone_id;
        $receiver_zone_id  = isset($data['receiver_zone_id'])? $data['receiver_zone_id']:$voucher->receiver_zone_id;
        $receiver_city_id      = isset($data['receiver_city_id'])? $data['receiver_city_id']:$voucher->receiver_city_id;

        if ($pickup->sender_type == 'Merchant') {
            $merchant = Merchant::find($pickup->sender_id);
            // $merchant_discounts = MerchantDiscount::where('merchant_id', $merchant->id)->get();

            if ($bus_station) {
                if ($merchant->fix_dropoff_price > 0) {
                    $delivery_amount = $merchant->fix_dropoff_price;
                } else {
                    $delivery_amount = Gate::find($sender_gate_id)->delivery_rate;
                }
            } else {
                if ($merchant->fix_delivery_price > 0) {
                    $delivery_amount = $merchant->fix_delivery_price;
                } else {
                    $delivery_amount = isset($receiver_zone_id) ? Zone::find($receiver_zone_id)->delivery_rate : City::find($receiver_city_id)->delivery_rate;
                }
            }
        } else {
            if ($bus_station) {
                $delivery_amount = Gate::find($sender_gate_id)->delivery_rate;
            } else {
                $delivery_amount = isset($receiver_zone_id) ? Zone::find($receiver_zone_id)->delivery_rate : City::find($receiver_city_id)->delivery_rate;
            }
        }
        //calculate total amount to collect
        $deposit_amount = isset($data['deposit_amount']) ? $data['deposit_amount'] : $voucher->deposit_amount;
        $item_price = isset($data['total_item_price']) ? $data['total_item_price'] : $voucher->total_item_price;

        $amount_to_collect = $this->calculate_amount_to_collect($payment_type->id, $item_price, $delivery_amount, $deposit_amount);

        $customerRepository = new CustomerRepository();
        $customer = Customer::phone($data['receiver_phone'])->first();
        $receiver = [
            'name'    => $data['receiver_name'],
            'phone'   => $data['receiver_phone'],
            'address' => $data['receiver_address'],
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
        $voucher->pickup_id = isset($data['pickup_id']) ? $data['pickup_id'] : $voucher->pickup_id;
        // $voucher->total_item_price = isset($data['total_item_price']) ? $data['total_item_price'] : $voucher->total_item_price;
        // $voucher->total_delivery_amount = isset($data['total_delivery_amount']) ? $data['total_delivery_amount'] : $voucher->total_delivery_amount;
        // $voucher->total_amount_to_collect = isset($data['total_amount_to_collect']) ? $data['total_amount_to_collect'] : $voucher->total_amount_to_collect;
        $voucher->remark = isset($data['remark']) ? $data['remark'] : $voucher->remark;
        $voucher->payment_type_id = isset($data['payment_type_id']) ? $data['payment_type_id'] : $voucher->payment_type_id;
        $voucher->sender_city_id = isset($data['sender_city_id']) ? $data['sender_city_id'] : $voucher->sender_city_id;
        $voucher->sender_zone_id = isset($data['sender_zone_id']) ? $data['sender_zone_id'] : $voucher->sender_zone_id;
        $voucher->receiver_city_id = isset($data['receiver_city_id']) ? $data['receiver_city_id'] : $voucher->receiver_city_id;
        $voucher->receiver_zone_id = isset($data['receiver_zone_id']) ? $data['receiver_zone_id'] : $voucher->receiver_zone_id;
        $voucher->bus_station = isset($data['bus_station']) ? $data['bus_station'] : $voucher->bus_station;
        $voucher->sender_bus_station_id = isset($data['sender_bus_station_id']) ? $data['sender_bus_station_id'] : $voucher->sender_bus_station_id;
        $voucher->receiver_bus_station_id = isset($data['receiver_bus_station_id']) ? $data['receiver_bus_station_id'] : $voucher->receiver_bus_station_id;
        $voucher->sender_gate_id = isset($data['sender_gate_id']) ? $data['sender_gate_id'] : $voucher->sender_gate_id;
        $voucher->receiver_gate_id = isset($data['receiver_gate_id']) ? $data['receiver_gate_id'] : $voucher->receiver_gate_id;
        $voucher->bus_credit = isset($data['bus_credit']) ? $data['bus_credit'] : $voucher->bus_credit;
        $voucher->bus_fee = isset($data['bus_fee']) ? $data['bus_fee'] : $voucher->bus_fee;
        $voucher->discount_id = isset($data['discount_id']) ? $data['discount_id'] : $voucher->discount_id;
        $voucher->coupon_id = isset($data['coupon_id']) ? $data['coupon_id'] : $voucher->coupon_id;
        $voucher->discount_amount = isset($data['discount_amount']) ? $data['discount_amount'] : $voucher->discount_amount;
        $voucher->call_status_id = isset($data['call_status_id']) ? $data['call_status_id'] : $voucher->call_status_id;
        $voucher->delivery_status_id = isset($data['delivery_status_id']) ? $data['delivery_status_id'] : $voucher->delivery_status_id;
        $voucher->store_status_id = isset($data['store_status_id']) ? $data['store_status_id'] : $voucher->store_status_id;

        if ($voucher->isDirty()) {
            $voucher->updated_by = auth()->user()->id;
            $voucher->save();
        }

        $parcelRepository = new ParcelRepository();
        $parcelItemRepository = new ParcelItemRepository();

        if (isset($data['parcels'])) {
            $total_deli_amount = 0;
            $total_price = 0;

            foreach ($data['parcels'] as $key => $par) {
                if (isset($par['id']) && isset($par['is_delete']) && $par['is_delete'] == true) {
                    $parcel_data = Parcel::findOrFail($par['id']);
                    $parcelRepository->destroy($parcel_data);
                } else {
                    if (isset($par['id'])) {
                        $parcel_data = Parcel::findOrFail($par['id']);
                        $parcel = $parcelRepository->update($parcel_data, $par);
                        $total_deli_amount += $parcel->delivery_amount;

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
                        $parcel_amount_to_collect = $this->calculate_amount_to_collect($voucher->payment_type_id, $parcel_total_price, $total_deli_amount);
                        $parcel->update(['amount_to_collect' => $parcel_amount_to_collect]);
                        $parcel->save();
                    } else {
                        $par['delivery_amount'] = $delivery_amount;
                        $par['discount_amount'] = $voucher->discount_amount;

                        $parcel = $parcelRepository->create($par, $voucher->id);

                        $total_deli_amount += $parcel->delivery_amount;

                        $parcel_total_price = 0;

                        foreach ($par["parcel_items"] as $item) {
                            $parcel_item = $parcelItemRepository->create($item, $parcel->id);
                            $parcel_total_price += $parcel_item->item_price;
                            $total_price += $parcel_item->item_price;
                        }
                        $parcel_amount_to_collect = $this->calculate_amount_to_collect($voucher->payment_type_id, $parcel_total_price, $parcel->delivery_amount);
                        $parcel->update(['amount_to_collect' => $parcel_amount_to_collect]);
                        $parcel->save();
                    }
                }
            }
            $collect_amount = $this->calculate_amount_to_collect($voucher->payment_type_id, $total_price, $total_deli_amount);
            $voucher->total_item_price = $total_price;
            $voucher->total_delivery_amount = $total_deli_amount;
            $voucher->total_amount_to_collect = $collect_amount;
            $voucher->save();
        }

        return $voucher->refresh();
    }

    /**
     * @param Voucher $voucher
     */
    public function destroy(Voucher $voucher)
    {
        $deleted = $this->deleteById($voucher->id);

        if ($deleted) {
            $voucher->deleted_by = auth()->user()->id;
            $voucher->save();
        }
    }

    /**
     * @param Voucher $voucher
     *
     * @return array
     */
    public function history(Voucher $voucher): array
    {
        $histories = $voucher->voucher_histories;
        $from = '` from `';
        $to = '` to `';
        $arr = [];

        foreach ($histories as $history) {
            $status = '`'.$voucher->voucher_invoice.'` `'.$history->log_status->description.$from.
                        $history->previous.$to.$history->next.'` by `'.
                        $history->created_by_staff->username.'`';
            $arr[] = $status;
        }

        return $arr;
    }

    public function calculate_amount_to_collect($payment_type_id, $total_item_price, $total_delivery_amount, $deposit_amount=null)
    {
        switch ($payment_type_id) {
            case 1:
                $amount_to_collect = $total_item_price + $total_delivery_amount;
                break;
            case 2:
                $amount_to_collect = $total_item_price;
                break;
            case 3:
                $amount_to_collect = $total_delivery_amount;
                break;
            case 4:
                $amount_to_collect = 0;
                break;
            case 5:
                $amount_to_collect = 0;
                break;
            case 6:
                $amount_to_collect = $total_delivery_amount;
                break;
            case 7:
                $amount_to_collect = $deposit_amount;
                break;
            default:
                $amount_to_collect = 0;
        }
        return $amount_to_collect;
    }
}
