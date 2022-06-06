<?php

namespace App\Http\Resources\MerchantDashboard\Order;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\City\CityResource;
use App\Http\Resources\Gate\GateResource;
use App\Http\Resources\Zone\ZoneResource;
use App\Http\Resources\MerchantDashboard\OrderItem\OrderItemCollection;

class OrderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id'    => $this->id,
            'order_no' => $this->order_no,
            'receiver_name' => $this->receiver_name,
            'receiver_phone' => $this->receiver_phone,
            'receiver_address' => $this->receiver_address,
            'receiver_email' => $this->receiver_email,
            'sender_city_id' => $this->sender_city_id,
            'receiver_city_id' => $this->receiver_city_id,
            'receiver_zone_id' => $this->receiver_zone_id,
            'sender_city' => CityResource::make($this->whenLoaded('sender_city')),
            'sender_zone' => ZoneResource::make($this->whenLoaded('sender_zone')),
            'receiver_city' => CityResource::make($this->whenLoaded('receiver_city')),
            'receiver_zone' => ZoneResource::make($this->whenLoaded('receiver_zone')),
            'payment_type_id' => $this->payment_type_id,
            'global_scale_id' => $this->global_scale_id,
            'payment_option' => $this->payment_option,
            'payment_method' => $this->payment_method,
            'is_paid' => $this->is_paid,
            'is_receive' => $this->is_receive,            
            'remark' => $this->remark,
            'thirdparty_invoice' => $this->thirdparty_invoice,
            'total_weight' => $this->total_weight,
            'total_delivery_amount' => $this->total_delivery_amount,
            'total_price' => $this->total_price,
            'platform' => $this->platform,
            'status' => $this->status,
            'good_agent_id' => $this->good_agent_id,
            'order_items'  => OrderItemCollection::make($this->whenLoaded('order_items')),
        ];
    }

    /**
     * Get additional data that should be returned with the resource array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function with($request)
    {
        return [
            'status' => 1,
        ];
    }
}
