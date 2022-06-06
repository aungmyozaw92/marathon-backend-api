<?php

namespace App\Http\Resources\FinanceExpense;

use App\Http\Resources\Staff\StaffResource;
use App\Http\Resources\Branch\BranchResource;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Attachment\AttachmentCollection;
use App\Http\Resources\FinanceExpenseItem\FinanceExpenseItemResource;
use App\Http\Resources\FinanceExpenseItem\FinanceExpenseItemCollection;

class FinanceExpenseResource extends JsonResource
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
            'spend_on' => $this->spend_on,
            'invoice_no' => $this->expense_invoice,
            'url' => 'finance_expenses',
            'total' => $this->total,
            'fn_paymant_option' => $this->fn_paymant_option,
            'is_approved' => $this->is_approved,
            'branch' => BranchResource::make($this->whenLoaded('branch')),
            'staff' => StaffResource::make($this->whenLoaded('staff')),
            'finance_expense_items' => FinanceExpenseItemCollection::make($this->whenLoaded('finance_expense_items')),
            'attachments' => AttachmentCollection::make($this->whenLoaded('attachments')),
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
