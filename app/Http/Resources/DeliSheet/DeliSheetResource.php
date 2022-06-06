<?php

namespace App\Http\Resources\DeliSheet;

use App\Http\Resources\Zone\ZoneResource;
use App\Http\Resources\Staff\StaffResource;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Attachment\AttachmentCollection;
use App\Http\Resources\DeliSheetVoucher\DeliSheetVoucherCollection;

class DeliSheetResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        //   dd($this->date);
        return [
            'id' => $this->id,
            'delisheet_invoice' => $this->delisheet_invoice,
            'qty' => $this->vouchers->count(),
            // 'qty' => $this->vouchers_count,
            'zone' => ZoneResource::make($this->whenLoaded('zone')),
            'delivery' => StaffResource::make($this->whenLoaded('delivery')),
            'staff' => StaffResource::make($this->whenLoaded('staff')),
            'note' => $this->note,
            'priority' => $this->priority,
            'vouchers' => DeliSheetVoucherCollection::make($this->whenLoaded('vouchers')),
            'is_closed' => $this->is_closed,
            'is_paid' => $this->is_paid,
            // 'lunch_amount' => number_format($this->lunch_amount),
            // 'commission_amount' => number_format($this->commission_amount),
            // 'collect_amount' => number_format($this->collect_amount),
            // 'total_amount' => number_format($this->total_amount),
            'lunch_amount' => $this->lunch_amount,
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
            'is_pointable'      => $this->is_pointable,
            'attachments' => AttachmentCollection::make($this->whenLoaded('attachments'))
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
