<?php

namespace App\Http\Resources\FinancePettyCashItem;

use App\Http\Resources\Staff\StaffResource;
use App\Http\Resources\Branch\BranchResource;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\FinanceAccount\FinanceAccountResource;
use App\Http\Resources\FinancePettyCash\FinancePettyCashResource;

class FinancePettyCashItemResource extends JsonResource
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
            'spend_at' => $this->spend_at,
            'description' => $this->description,
            'remark' => $this->remark,
            'amount' => $this->amount,
            'tax_amount' => $this->tax_amount,
            'from_finance_account' => FinanceAccountResource::make($this->whenLoaded('from_finance_account')),
            'to_finance_account' => FinanceAccountResource::make($this->whenLoaded('to_finance_account')),
            'finance_petty_cash' => FinancePettyCashResource::make($this->whenLoaded('finance_petty_cash')),
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
