<?php

namespace App\Http\Resources\Mobile\Delivery\Voucher;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use App\Http\Resources\Pickup\PickupResource;
use App\Http\Resources\Parcel\ParcelCollection;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Customer\CustomerResource;
use App\Http\Resources\CallStatus\CallStatusResource;
use App\Http\Resources\PaymentType\PaymentTypeResource;
use App\Http\Resources\StoreStatus\StoreStatusResource;
use App\Http\Resources\Mobile\Delivery\City\CityResource;
use App\Http\Resources\Mobile\Delivery\Gate\GateResource;
use App\Http\Resources\Mobile\Delivery\Zone\ZoneResource;
use App\Http\Resources\DeliveryStatus\DeliveryStatusResource;
use App\Http\Resources\Mobile\Delivery\BusStation\BusStationResource;
use App\Http\Resources\Mobile\Delivery\Attachment\AttachmentCollection;

class VoucherResource extends JsonResource
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
        $this->condition = !str_contains($request->route()->uri(), 'finance_vouchers');
        // $url = Storage::url('uploads/vouchers/' . $this->voucher_invoice . '.png');
        // $file = (File::exists($url) ? $url : null);
        $is_prepaid = 0;
        if (!$this->condition) {
            if (!$this->is_closed && ($this->payment_type_id == 9 || $this->payment_type_id == 10)) {
                $is_prepaid = 1;
            }
        }
// dd(!str_contains($request->route()->uri(), 'delivery_vouchers'));
        $last_delisheet_vouchers = null;
        if(!str_contains($request->route()->uri(), 'delivery_vouchers') 
        && !str_contains($request->route()->uri(), 'cant_deliver_vouchers')
        && !str_contains($request->route()->uri(), 'pickups'))
        {
            $last_delisheet_vouchers = $this->delisheets->last()['deli_sheet_vouchers'];
        }

        return [
            'id' => $this->id,
            'is_prepaid' => $is_prepaid,
            'voucher_no' => $this->voucher_invoice,
            "pickup" => PickupResource::make($this->whenLoaded('pickup')),
            'receiver' => CustomerResource::make($this->customer),

            'sender_city' => CityResource::make($this->whenLoaded('sender_city')),
            'sender_zone' => ZoneResource::make($this->whenLoaded('sender_zone')),
            'receiver_city' => CityResource::make($this->whenLoaded('receiver_city')),
            'receiver_zone' => ZoneResource::make($this->whenLoaded('receiver_zone')),
            // 'cbm' => $this->global_scale->cbm,GlobalScaleResource::make($this->whenLoaded('global_scale'))
            // 'max_weight' => $this->global_scale->max_weight,
            'call_status' => CallStatusResource::make($this->whenLoaded('call_status')),
            //'delivery_status_id' => DeliveryStatusResource::make($this->whenLoaded('delivery_status')),
            'delivery_status_id' => $this->delivery_status_id,
            'store_status' => StoreStatusResource::make($this->whenLoaded('store_status')),
            'amount_to_collect_sender' => $this->sender_amount_to_collect,
            'amount_to_collect_receiver' => $this->receiver_amount_to_collect,
            'payment_type' => PaymentTypeResource::make($this->payment_type),
            'created_at' => $this->created_at->format('Y-m-d'),
            'postpone_date' => $this->postpone_date ? $this->postpone_date->format('Y-m-d') : null,
            'note' => $this->remark,
            // 'note' => getConvertedUni2Zg($this->remark),
            'bus_station' => $this->bus_station,
            'sender_bus_station' => BusStationResource::make($this->whenLoaded('sender_bus_station')),
            'receiver_bus_station' => BusStationResource::make($this->whenLoaded('receiver_bus_station')),
            'sender_gate' => GateResource::make($this->whenLoaded('sender_gate')),
            'receiver_gate' => GateResource::make($this->whenLoaded('receiver_gate')),
            // 'delivery_status' => ($this->deli_sheet_vouchers) ? $this->deli_sheet_vouchers->delivery_status : null,
            'delisheet' => $last_delisheet_vouchers,
            // 'bus_sheet' => $this->bussheets->last()['bus_sheet_vouchers'],
            //'image' => $file,
            'parcels' => ParcelCollection::make($this->whenLoaded('parcels')),
            'attachments' => AttachmentCollection::make($this->whenLoaded('attachments')),
            'is_return' => $this->is_return,
            'thirdparty_invoice' => $this->thirdparty_invoice,
            'delivery_commission' => $this->delivery_commission
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
