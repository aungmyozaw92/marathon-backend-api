<?php

namespace App\Http\Resources\Mobile\Operation\Waybill;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Mobile\Operation\City\CityResource;
use App\Http\Resources\Mobile\Operation\Gate\GateResource;
use App\Http\Resources\Mobile\Operation\Staff\StaffResource;
use App\Http\Resources\Mobile\Operation\BusStation\BusStationResource;
use App\Http\Resources\Mobile\Operation\WayBillVoucher\WayBillVoucherCollection;

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
            'actual_bus_fee' => $this->actual_bus_fee,
            // 'from_bus_station_id' => $this->from_bus_station_id,
            'from_bus_station' => BusStationResource::make($this->whenLoaded('from_bus_station')),
            // 'to_bus_station_id' => $this->to_bus_station_id,
            'to_bus_station' => BusStationResource::make($this->whenLoaded('to_bus_station')),
            // 'gate_id' => $this->gate_id,
            'gate' => GateResource::make($this->whenLoaded('gate')),
            // 'from_city_id' => $this->from_city_id,
            'from_city' => CityResource::make($this->whenLoaded('from_city')),
            // 'to_city_id' => $this->to_city_id,
            'to_city' => CityResource::make($this->whenLoaded('to_city')),
            'delivery' => StaffResource::make($this->whenLoaded('delivery')),
            'issuer' => StaffResource::make($this->whenLoaded('staff')),
            'note' => $this->note,
            'vouchers' => WayBillVoucherCollection::make($this->whenLoaded('vouchers')),
            //'attachments' => AttachmentCollection::make($this->whenLoaded('attachments')),
            'created_at' => $this->created_at->format('Y-m-d'),
            'created_time' => $this->created_at->format('H:i A'),
            'is_closed' => $this->is_closed,
            'is_confirm' => $this->is_confirm,
            'is_paid' => $this->is_paid,
            'is_delivered' => $this->is_delivered,
            'is_received' => $this->is_received,
            'is_scanned' => $this->is_scanned
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
