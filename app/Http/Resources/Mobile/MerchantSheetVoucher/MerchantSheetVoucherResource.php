<?php

namespace App\Http\Resources\Mobile\MerchantSheetVoucher;

use App\Models\Pickup;
use App\Http\Resources\Mobile\Zone\ZoneResource;

// use App\Http\Resources\Parcel\ParcelCollection;
 use App\Http\Resources\Mobile\City\CityResource;
 // use App\Http\Resources\Mobile\Gate\GateResource;
//  use App\Http\Resources\Mobile\Zone\ZoneResource;
 use Illuminate\Http\Resources\Json\JsonResource;
 use App\Http\Resources\Customer\CustomerResource;
 // use App\Http\Resources\CallStatus\CallStatusResource;
// use App\Http\Resources\PaymentType\PaymentTypeResource;
// use App\Http\Resources\StoreStatus\StoreStatusResource;
// use App\Http\Resources\PaymentStatus\PaymentStatusResource;
// use App\Http\Resources\Mobile\BusStation\BusStationResource;
 use App\Http\Resources\DeliveryStatus\DeliveryStatusResource;

 class MerchantSheetVoucherResource extends JsonResource
 {
     /**
      * Transform the resource into an array.
      *
      * @param \Illuminate\Http\Request $request
      *
      * @return array
      */
     public function toArray($request)
     {
         if ($this->total_coupon_amount > 0) {
             $total_delivery_amount = $this->total_delivery_amount - $this->total_coupon_amount;
         } else {
             $total_delivery_amount = $this->discount_type == "extra" ?
                        $this->total_delivery_amount + $this->total_discount_amount : $this->total_delivery_amount - $this->total_discount_amount;
         }
         $total_extra_fee = $this->transaction_fee + $this->insurance_fee + $this->warehousing_fee;

         switch ($this->payment_type_id) {
                case 1:
                    if ($this->delivery_status_id == 9) {
                        $balance = 0 - $this->return_fee;
                    } else {
                        $balance = $this->total_amount_to_collect - ($total_delivery_amount + $total_extra_fee);
                    }
                    break;
                case 2:
                    if ($this->delivery_status_id == 9) {
                        $balance = 0 - $this->return_fee;
                    } else {
                        $balance = $this->total_amount_to_collect - ($total_delivery_amount + $total_extra_fee);
                    }
                    break;
                case 3:
                    if ($this->delivery_status_id == 9) {
                        $balance = 0 - $this->return_fee;
                    } else {
                        $balance = 0;//$this->total_amount_to_collect - $total_delivery_amount;
                    }
                    break;
                case 4:
                    if ($this->delivery_status_id == 9) {
                        $balance = 0 - $this->return_fee;
                    } else {
                        $balance = $this->total_amount_to_collect - $total_delivery_amount;
                    }
                    break;
                case 9:
                    if ($this->delivery_status_id == 9) {
                        $balance =  $total_delivery_amount - $this->return_fee;
                    } else {
                        $balance = 0;
                    }
                    break;
                case 10:
                    if ($this->delivery_status_id == 9) {
                        $balance = $total_delivery_amount - $this->return_fee;
                    } else {
                        $balance = $this->total_amount_to_collect - $total_extra_fee;
                    }
                    break;
                default:
                    $balance = 0;
            }


         return [
            'id' => $this->id,
            'voucher_no' => $this->voucher_invoice,
            'receiver' => CustomerResource::make($this->whenLoaded('customer')),
            'total_item_price' => $this->total_item_price,
            'receiver_city' => CityResource::make($this->whenLoaded('receiver_city')),
            'receiver_zone' => ZoneResource::make($this->whenLoaded('receiver_zone')),
            'delivery_status' => DeliveryStatusResource::make($this->whenLoaded('delivery_status')),
            'balance' => $balance,
            'bus_station' => $this->bus_station,
            'is_return' => $this->is_return,
        ];
     }

     /**
      * Get additional data that should be returned with the resource array.
      *
      * @param \Illuminate\Http\Request $request
      *
      * @return array
      */
     public function with($request)
     {
         return [
            'status' => 1,
        ];
     }
 }
