<?php

namespace App\Http\Resources\Mobile\Agent\Waybill;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\City\CityResource;
use App\Http\Resources\Gate\GateResource;
use App\Http\Resources\Staff\StaffResource;
use App\Http\Resources\Mobile\Agent\Voucher\VoucherCollection;
use App\Http\Resources\BusStation\BusStationResource;
use App\Http\Resources\Attachment\AttachmentCollection;

class WaybillResource extends JsonResource
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
            'id' => $this->id,
            'waybill_invoice' => $this->waybill_invoice,
            'vouchers_qty' => $this->vouchers()->count(),
            'delivered_vouchers_qty' => $this->vouchers()->where('delivery_status_id',8)->count(),
            'actual_bus_fee' => $this->actual_bus_fee,
            'from_bus_station' => BusStationResource::make($this->whenLoaded('from_bus_station')),
            'to_bus_station' => BusStationResource::make($this->whenLoaded('to_bus_station')),
            'gate' => GateResource::make($this->whenLoaded('gate')),
            'from_city' => CityResource::make($this->whenLoaded('from_city')),
            'to_city' => CityResource::make($this->whenLoaded('to_city')),
            'delivery' => StaffResource::make($this->whenLoaded('delivery')),
            'issuer' => StaffResource::make($this->whenLoaded('staff')),
            // 'note' => getConvertedUni2Zg($this->note),
            'note' => $this->note,
            'attachments' => AttachmentCollection::make($this->whenLoaded('attachments')),
            'vouchers' => VoucherCollection::make($this->whenLoaded('vouchers')),
            'created_at' => $this->created_at->format('Y-m-d'),
            // 'status' => $this->closed === 0 ? 'close' : 'open'
            'is_closed' => $this->is_closed,
            'is_confirm' => $this->is_confirm,
            'is_paid' => $this->is_paid,
            'is_delivered' => $this->is_delivered,
            'is_received' => $this->is_received,
            'received_date' => $this->received_date,
            'received_by_type' => $this->received_by_type,
            'received_by_id' => $this->received_by_id,
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
