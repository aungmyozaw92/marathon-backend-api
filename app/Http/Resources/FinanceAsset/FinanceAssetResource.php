<?php

namespace App\Http\Resources\FinanceAsset;

use App\Http\Resources\Branch\BranchResource;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\FinanceAccount\FinanceAccountResource;
use App\Http\Resources\FinanceAssetType\FinanceAssetTypeResource;

class FinanceAssetResource extends JsonResource
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
            'asset_no' => $this->asset_no,
            'name' => $this->name,
            'branch_id' => BranchResource::make($this->whenLoaded('branch')),  
            'asset_type' => FinanceAssetTypeResource::make($this->whenLoaded('finance_asset_type')),
            'depreciation_expense_account' => FinanceAccountResource::make($this->whenLoaded('depreciation_expense_account')),
            'accumulated_depreciation_account' => FinanceAccountResource::make($this->whenLoaded('accumulated_depreciation_account')),
            'asset_type' => FinanceAssetTypeResource::make($this->whenLoaded('finance_asset_type')),
            'description' => $this->description,
            'serial_no' => $this->serial_no,
            'purchase_price' => $this->purchase_price,
            'purchase_date' => $this->purchase_date,
            'depreciation_start_date' => $this->depreciation_start_date,
            'warranty_month' => $this->warranty_month,
            'depreciation_month' => $this->depreciation_month,
            'depreciation_rate' => $this->depreciation_rate,
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
