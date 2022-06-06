<?php

namespace App\Http\Resources\FinanceAccount;

use App\Http\Resources\Branch\BranchResource;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\FinanceTax\FinanceTaxResource;
use App\Http\Resources\FinanceCode\FinanceCodeResource;
use App\Http\Resources\FinanceGroup\FinanceGroupResource;
use App\Http\Resources\FinanceNature\FinanceNatureResource;
use App\Http\Resources\FinanceMasterType\FinanceMasterTypeResource;
use App\Http\Resources\FinanceAccountType\FinanceAccountTypeResource;

class FinanceAccountResource extends JsonResource
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
            'code' => $this->code,
            'description' => $this->description,
            'actor_type' => $this->actor_type,
            'actor_id' => $this->actor_id,
            'actorable' => $this->actorable,
            'finance_nature' => FinanceNatureResource::make($this->whenLoaded('finance_nature')),
            'finance_master_type' => FinanceMasterTypeResource::make($this->whenLoaded('finance_master_type')),
            'finance_account_type' => FinanceAccountTypeResource::make($this->whenLoaded('finance_account_type')),
            'finance_group' => FinanceGroupResource::make($this->whenLoaded('finance_group')),
            'branch' => BranchResource::make($this->whenLoaded('branch')),
            'finance_tax' => FinanceTaxResource::make($this->whenLoaded('finance_tax')),
            'finance_code' => FinanceCodeResource::make($this->whenLoaded('finance_code')),
            
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
