<?php

namespace App\Http\Resources\Mobile\Delivery\DeliSheet;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Mobile\Delivery\DeliSheetVoucher\DeliSheetVoucherCollection;

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
        return [
            'id' => $this->id,
            'delisheet_invoice' => $this->delisheet_invoice,
            'qty' => $this->qty,
            'note' =>  getConvertedUni2Zg($this->note),
            'priority' => $this->priority,
            'vouchers' => DeliSheetVoucherCollection::make($this->vouchers),
            'is_closed' => $this->is_closed,
            'is_paid' => $this->is_paid,
            'lunch_amount' => $this->lunch_amount,
            'commission_amount' => $this->commission_amount,
            'collect_amount' => $this->collect_amount,
            'total_amount' => $this->total_amount,
            'created_at' => $this->created_at->format('Y-m-d'),
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
