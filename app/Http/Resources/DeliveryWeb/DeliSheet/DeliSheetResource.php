<?php

namespace App\Http\Resources\DeliveryWeb\DeliSheet;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\CourierType\CourierTypeResource;
use App\Http\Resources\Mobile\Delivery\Zone\ZoneResource;
use App\Http\Resources\Mobile\Delivery\Staff\StaffResource;
use App\Http\Resources\DeliveryWeb\Voucher\VoucherCollection;

class DeliSheetResource extends JsonResource
{
    private $condition = true;

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
            'delisheet_invoice' => $this->delisheet_invoice,
            'qty' => $this->qty,
            'cant_delivered_count' => $this->deli_sheet_vouchers->where('cant_deliver',1)->count(),
            'delivered_count' => $this->deli_sheet_vouchers->where('delivery_status',1)->count(),
            'zone' => ZoneResource::make($this->whenLoaded('zone')),
            // 'delivery' => StaffResource::make($this->whenLoaded('delivery')),
            'staff' => StaffResource::make($this->whenLoaded('staff')),
            'courier_type' => CourierTypeResource::make($this->whenLoaded('courier_type')),
            'note' => $this->note,
            'priority' => $this->priority,
            'vouchers' => VoucherCollection::make($this->whenLoaded('vouchers')),
            'is_closed' => $this->is_closed,
            'is_paid' => $this->is_paid,
            // 'lunch_amount' => number_format($this->lunch_amount),
            // 'commission_amount' => number_format($this->commission_amount),
            // 'collect_amount' => number_format($this->collect_amount),
            // 'total_amount' => number_format($this->total_amount),
            'lunch_amount' => $this->lunch_amount,
            'points' => $this->point_logs->sum('points'),
            'commission_amount' => $this->commission_amount,
            'collect_amount' => $this->collect_amount,
            'total_amount' => $this->total_amount,
            'date' => ($this->date) ? $this->date->format('Y-m-d') : null,
            'created_at' => $this->created_at->format('Y-m-d'),
            'updated_at' => $this->updated_at->format('Y-m-d'),
            'created_time' => $this->created_at->format('H:i A'),
            'is_scanned' => $this->is_scanned,
            'is_commissionable' => $this->is_commissionable,
            'courier_type_id' => $this->courier_type_id,
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
