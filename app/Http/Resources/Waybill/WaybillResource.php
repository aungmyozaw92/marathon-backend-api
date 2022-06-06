<?php

namespace App\Http\Resources\Waybill;

use App\Http\Resources\City\CityResource;
use App\Http\Resources\Gate\GateResource;
use App\Http\Resources\Agent\AgentResource;
use App\Http\Resources\Staff\StaffResource;
use App\Http\Resources\Agent\AgentCollection;
use App\Http\Resources\Voucher\VoucherResource;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\BusStation\BusStationResource;
use App\Http\Resources\Attachment\AttachmentCollection;
use App\Http\Resources\WayBillVoucher\WayBillVoucherResource;
use App\Http\Resources\WayBillVoucher\WayBillVoucherCollection;

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
            // 'vouchers_qty' => $this->vouchers()->count(),
            'vouchers_qty' => $this->vouchers_count,
            // 'actual_bus_fee' => number_format($this->actual_bus_fee),
            'actual_bus_fee' => $this->actual_bus_fee,
            'from_bus_station_id' => $this->from_bus_station_id,
            'from_bus_station' => BusStationResource::make($this->whenLoaded('from_bus_station')),
            'to_bus_station_id' => $this->to_bus_station_id,
            'to_bus_station' => BusStationResource::make($this->whenLoaded('to_bus_station')),
            'gate_id' => $this->gate_id,
            'gate' => GateResource::make($this->whenLoaded('gate')),
            'from_city_id' => $this->from_city_id,
            'from_city' => CityResource::make($this->whenLoaded('from_city')),
            'to_city_id' => $this->to_city_id,
            'to_city' => CityResource::make($this->whenLoaded('to_city')),
            'from_agent_id' => $this->from_agent_id,
            'from_agent' => AgentResource::make($this->whenLoaded('from_agent')),
                                        
            'to_agent_id' => $this->to_agent_id,
            // 'to_agents' => AgentCollection::make($this->whenLoaded('to_city.agents')),
            'to_agent' => AgentResource::make($this->whenLoaded('to_agent')),
            'delivery' => StaffResource::make($this->whenLoaded('delivery')),
            'issuer' => StaffResource::make($this->whenLoaded('staff')),
            'note' => $this->note,
            'vouchers' => WayBillVoucherCollection::make($this->whenLoaded('vouchers')),
            'attachments' => AttachmentCollection::make($this->whenLoaded('attachments')),
            'created_at' => $this->created_at->format('Y-m-d'),
            'created_time' => $this->created_at->format('H:i A'),
            'is_closed' => $this->is_closed,
            'is_paid' => $this->is_paid,
            'is_delivered' => $this->is_delivered,
            'is_received' => $this->is_received,
            'received_date' => $this->received_date,
            'received_by_type' => $this->received_by_type,
            'received_by_id' => $this->received_by_id,
            //'receivable' => $this->receivable,
            'is_scanned' => $this->is_scanned,
            'is_confirm' => $this->is_confirm,
            'commission_amount' => $this->commission_amount,
            'courier_type_id' => $this->courier_type_id,
            'is_commissionable' => $this->is_commissionable,
            'is_pointable'      => $this->is_pointable
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
