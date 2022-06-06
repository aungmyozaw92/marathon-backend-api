<?php

namespace App\Http\Resources\FinancePettyCash;

use App\Http\Resources\Staff\StaffResource;
use App\Http\Resources\Branch\BranchResource;
use App\Http\Resources\FinancePettyCashItem\FinancePettyCashItemCollection;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\FinancePettyCashItem\FinancePettyCashItemResource;

class FinancePettyCashResource extends JsonResource
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
            'invoice_no' => $this->invoice_no,
            'spend_on' => $this->spend_on,
            'total' => $this->total,
            'fn_paymant_option' => $this->fn_paymant_option,
            'staff_id' => $this->staff_id,
            'branch_id' => $this->branch_id,
            'branch' => BranchResource::make($this->whenLoaded('branch')),
            'staff' => StaffResource::make($this->whenLoaded('staff')),
            'actor_by' => StaffResource::make($this->whenLoaded('actor_by')),
            'finance_petty_cash_items' => FinancePettyCashItemCollection::make($this->whenLoaded('finance_petty_cash_items')),
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
