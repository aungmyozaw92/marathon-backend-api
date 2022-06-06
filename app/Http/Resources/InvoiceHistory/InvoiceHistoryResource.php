<?php

namespace App\Http\Resources\InvoiceHistory;

use Illuminate\Http\Resources\Json\JsonResource;

class InvoiceHistoryResource extends JsonResource
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
            'invoice_id' => $this->invoice_id,
            'invoice_journal_id' => $this->invoice_journal_id,
            'status_id' => $this->log_status_id,
            'status' => $this->log_status->description,
            'status_mm' => $this->log_status->description_mm,
            'remark' => $this->remark,
            'previous' => is_numeric($this->previous) ? loggedValue($this->log_status_id, $this->previous) : $this->previous,
            'next' => is_numeric($this->next) ? loggedValue($this->log_status_id, $this->next) : $this->next,
            'created_by' => ($this->created_by_staff) ? $this->created_by_staff->name : $this->created_by_staff,
            'created_time' => $this->created_at->format('H:i A'),
            'created_date' => date("jS F, Y", strtotime($this->created_at))
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
