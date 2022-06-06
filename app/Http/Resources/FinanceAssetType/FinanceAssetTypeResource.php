<?php

namespace App\Http\Resources\FinanceAssetType;

use App\Http\Resources\Branch\BranchResource;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\FinanceAccount\FinanceAccountResource;

class FinanceAssetTypeResource extends JsonResource
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
            'name' => $this->name,
            'accumulated_depreciation_account' => FinanceAccountResource::make($this->whenLoaded('accumulated_depreciation_account')),
            'depreciation_expense_account' => FinanceAccountResource::make($this->whenLoaded('depreciation_expense_account')),
            'depreciation_rate' => $this->depreciation_rate,
            'branch' => BranchResource::make($this->whenLoaded('branch')),    
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
