<?php

namespace App\Http\Resources\VoucherHistory;

use Illuminate\Http\Resources\Json\JsonResource;

class VoucherHistoryResource extends JsonResource
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
            'voucher_id' => $this->voucher_id,
            'status_id' => $this->log_status_id,
            'status' => $this->log_status->description,
            'status_mm' => $this->log_status->description_mm,
            'previous' => is_numeric($this->previous) ? loggedValue($this->log_status_id, $this->previous) : $this->previous,
            'next' => is_numeric($this->next) ? loggedValue($this->log_status_id, $this->next) : $this->next,
            'created_by' => ($this->createable) ? $this->createable->username : $this->createable,
            'created_time' => $this->created_at->format('H:i A'),
            'created_date' => date("jS F, Y", strtotime($this->created_at))
        ];
    }
    public function with($request)
    {
        return [
            'status' => 1,
        ];
    }
}
