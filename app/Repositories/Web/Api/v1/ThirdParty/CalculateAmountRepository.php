<?php

namespace App\Repositories\Web\Api\v1\ThirdParty
;

use App\Models\City;
use App\Models\Gate;
use App\Models\Zone;
use App\Models\Route;
use App\Models\BusDropOff;
use App\Models\BusStation;
use App\Models\DoorToDoor;
use App\Models\GlobalScale;
use App\Repositories\BaseRepository;
use App\Repositories\Web\Api\v1\MerchantDashboard\VoucherRepository;

class CalculateAmountRepository extends BaseRepository
{
    public function model()
    {
        return City::class;
    }

    public function calculate_delivery_amount(array $data)
    {        
        $route = Route::where('origin_id', auth()->user()->city_id)
                        ->where('destination_id', $data['receiver_city_id'])->firstOrFail();
        // Get route rate for CBM Formula
        $zone_rate = 0;
        if (isset($data['receiver_zone_id']) && $data['receiver_zone_id']) {
            $zone = Zone::findOrFail($data['receiver_zone_id']);
            if ($data['receiver_city_id'] != $zone->city_id) {
                $responses = [
                    'status' => 2,
                    'message' => 'Failed, Please select related city and zone'
                ];
                return $responses;
            }
            $zone_rate = $zone->zone_rate;
        }

        // Checking D2D and BD off
        $total_delivery_amount = 0;
        $total_item_price = 0;
        $merchant = auth()->user();
        $merchant_discount = null;
        $discount_amount = 0;
        $extra_or_discount = 0;
        if (isset($data['parcels'])) { 
            foreach ($data['parcels'] as $key => $par) {
                $cbm = GlobalScale::findOrFail($par['global_scale_id']);
                $data_weight = isset($par['weight']) ? $par['weight'] : 1;
                if ($data_weight > $cbm->max_weight) {
                    $responses = [
                        'status' => 2,
                        'message' => 'Over maximun weight.'
                    ];
                    return $responses;
                }
                $base_data = DoorToDoor::where('route_id', $route->id)->where('global_scale_id', $cbm->id)->firstOrFail();
                $weight = ($data_weight > $cbm->support_weight) ? ($data_weight - $cbm->support_weight) * $base_data->salt : 0;
                $delivery_amount = $base_data->base_rate + $weight + $zone_rate;
                $total_delivery_amount += $delivery_amount;

                foreach ($par["parcel_items"] as $item) {
                    $total_item_price += $item['item_price']*$item['item_qty'];
                }
            }
        } else {
            $global_scale_id = isset($data['global_scale_id']) ? $data['global_scale_id'] : 1;
            $cbm = GlobalScale::findOrFail($global_scale_id);
            $data_weight = isset($data['weight']) ? $data['weight'] : 1;
            if ($data_weight > $cbm->max_weight) {
                $responses = [
                    'status' => 2,
                    'message' => 'Over maximun weight.'
                ];
                return $responses;
            }
            
            $base_data = DoorToDoor::where('route_id', $route->id)->where('global_scale_id', $cbm->id)->firstOrFail();
            $weight = ($data_weight > $cbm->support_weight) ? ($data_weight - $cbm->support_weight) * $base_data->salt : 0;

            $delivery_amount = $base_data->base_rate + $weight + $zone_rate;
            $total_delivery_amount = $delivery_amount;
            $total_item_price = 0;
        }
        $voucherRepository = new VoucherRepository();
        $data['sender_city'] = $merchant->city_id;
        if ($merchant->is_discount) {
            $merchant_discount    = $voucherRepository->get_merchant_discount($merchant, $data);
        }
        if ($merchant_discount) {
            $discount_amount    = $voucherRepository->calculate_merchant_discount($merchant_discount, $total_delivery_amount);
            $extra_or_discount   = $merchant_discount['extra_or_discount'];
        }

        $insurance_fee = 0;
        if (isset($data['take_insurance']) && $data['take_insurance']) {
            $insurance_fee  = getInsuranceFee();
            $insurance_fee  = $total_item_price * $insurance_fee / 100;
        }
        $total_delivery_amount = ($extra_or_discount) ? $total_delivery_amount + $discount_amount : $total_delivery_amount - $discount_amount;

        $total_delivery_amount = computeRoundingAmount($total_delivery_amount);
        $responses = [
                    'status' => 1,
                    'message' => 'Success',
                    'total_delivery_amount' => $total_delivery_amount,
                    'insurance_amount' => $insurance_fee
                ];
        return $responses;



    }
}
