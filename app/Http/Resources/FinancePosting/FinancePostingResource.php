<?php

namespace App\Http\Resources\FinancePosting;

use App\Models\FinanceExpenseItem;
use App\Http\Resources\Branch\BranchResource;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\FinanceAccount\FinanceAccountResource;
use App\Http\Resources\FinanceAdvance\FinanceAdvanceResource;
use App\Http\Resources\FinanceExpenseItem\FinanceExpenseItemResource;

class FinancePostingResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        if ($this->posting_type == 'FinanceExpenseItem') {
           $postingable = FinanceExpenseItemResource::make($this->whenLoaded('postingable'));
        }elseif($this->posting_type == 'FinanceAdvance'){
            $postingable = FinanceAdvanceResource::make($this->whenLoaded('postingable'));
        }else{
            $postingable = $this->postingable;
        }
        return [
            'id' => $this->id,
            'invoice_no' => $this->posting_invoice,
            'amount' => $this->amount,
            'description' => $this->description,
            'status' => $this->status,
            'posting_type' => $this->posting_type,
            'posting_type_id' => $this->posting_type_id,
            'postingable' => $postingable,
            'from_actor_type' => $this->from_actor_type,
            'from_actor_type_id' => $this->from_actor_type_id,
            'from_actorable' => $this->from_actorable,
            'to_actor_type' => $this->to_actor_type,
            'to_actor_type_id' => $this->to_actor_type_id,
            'to_actorable' => $this->to_actorable,
            'branch' => BranchResource::make($this->whenLoaded('branch')),
            'from_finance_account' => FinanceAccountResource::make($this->whenLoaded('from_finance_account')),
            'to_finance_account' => FinanceAccountResource::make($this->whenLoaded('to_finance_account')),
            'posting' => FinancePostingResource::make($this->whenLoaded('posting')),
            
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
