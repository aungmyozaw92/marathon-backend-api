<?php

namespace App\Http\Resources\FinanceConfig;

use App\Http\Resources\Branch\BranchResource;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\FinanceAccount\FinanceAccountResource;

class FinanceConfigResource extends JsonResource
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
            'from_finance_account_id' => $this->finance_account_id,
            'to_finance_account_id' => $this->to_finance_account_id,
            'from_finance_account' => FinanceAccountResource::make($this->whenLoaded('finance_account')),
            'to_finance_account' => FinanceAccountResource::make($this->whenLoaded('to_finance_account')),
            'branch_id' => $this->branch_id,
            'branch' => BranchResource::make($this->whenLoaded('branch')),
            'screen' => $this->screen
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
