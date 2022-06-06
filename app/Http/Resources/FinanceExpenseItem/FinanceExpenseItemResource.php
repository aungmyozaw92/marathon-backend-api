<?php

namespace App\Http\Resources\FinanceExpenseItem;

use App\Http\Resources\Branch\BranchResource;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\FinanceAccount\FinanceAccountResource;
use App\Http\Resources\FinanceExpense\FinanceExpenseResource;

class FinanceExpenseItemResource extends JsonResource
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
            'invoice_no' => $this->expense_item_invoice,
            'spend_at' => $this->spend_at,
            'description' => $this->description,
            'qty' => $this->qty,
            'url' => 'finance_expense_items',
            'amount' => $this->amount,
            'from_finance_account' => FinanceAccountResource::make($this->whenLoaded('from_finance_account')),
            'to_finance_account' => FinanceAccountResource::make($this->whenLoaded('to_finance_account')),
            'finance_expense' => FinanceExpenseResource::make($this->whenLoaded('finance_expense')), 
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
