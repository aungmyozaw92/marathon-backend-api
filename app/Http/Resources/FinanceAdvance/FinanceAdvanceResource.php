<?php

namespace App\Http\Resources\FinanceAdvance;

use App\Http\Resources\Staff\StaffResource;
use App\Http\Resources\Branch\BranchResource;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\FinanceAccount\FinanceAccountResource;
use App\Http\Resources\FinanceExpense\FinanceExpenseResource;

class FinanceAdvanceResource extends JsonResource
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
            'invoice_no' => $this->advance_invoice,
            'amount' => $this->amount,
            'reason' => $this->reason,
            'status' => $this->status,
            'url' => 'finance_advances',
            'is_approved' => $this->is_approved,
            'is_paid' => $this->is_paid,
            'total_expense' => $this->total_expense,
            'total_advance' => $this->total_advance,
            'refund_reimbursements' => $this->refund_reimbursements,
            'finance_expense_id' => $this->finance_expense_id,
            'finance_expense' => FinanceExpenseResource::make($this->whenLoaded('finance_expense')),
            'branch' => BranchResource::make($this->whenLoaded('branch')),
            'staff' => StaffResource::make($this->whenLoaded('staff')),
            'from_finance_account' => FinanceAccountResource::make($this->whenLoaded('from_finance_account')),
            'to_finance_account' => FinanceAccountResource::make($this->whenLoaded('to_finance_account')),
            'issuer' => $this->issuer ? $this->issuer->name : null

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
