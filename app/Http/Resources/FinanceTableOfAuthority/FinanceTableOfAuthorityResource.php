<?php

namespace App\Http\Resources\FinanceTableOfAuthority;

use App\Http\Resources\Staff\StaffResource;
use Illuminate\Http\Resources\Json\JsonResource;

class FinanceTableOfAuthorityResource extends JsonResource
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
            'petty_amount' => $this->petty_amount,
            'expense_amount' => $this->expense_amount,
            'advance_amount' => $this->advance_amount,
            'staff_id' => $this->staff_id,
            'staff' => StaffResource::make($this->whenLoaded('staff')),
            'manager_id' => $this->manager_id,
            'manager' => StaffResource::make($this->whenLoaded('manager')),
            'is_need_approve' => $this->is_need_approve,
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
