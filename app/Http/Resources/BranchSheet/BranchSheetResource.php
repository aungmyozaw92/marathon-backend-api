<?php

namespace App\Http\Resources\BranchSheet;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Branch\BranchResource;
use App\Http\Resources\BranchSheetVoucher\BranchSheetVoucherCollection;

class BranchSheetResource extends JsonResource
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
            'branchsheet_invoice' => $this->branchsheet_invoice,
            'qty' => $this->qty,
            // 'credit' => number_format($this->credit),
            // 'debit' => number_format($this->debit),
            // 'balance' => number_format($this->balance),
            'credit' => $this->credit,
            'debit' => $this->debit,
            'balance' => $this->balance,
            'branch' => BranchResource::make($this->whenLoaded('branch')),
            'vouchers' => BranchSheetVoucherCollection::make($this->whenLoaded('vouchers')),
            'is_paid' => $this->is_paid,
            'created_at' => $this->created_at->format('Y-m-d'),
            'created_time' => $this->created_at->format('H:i A')
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
