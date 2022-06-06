<?php

namespace App\Http\Resources\WaybillHistory;

use Illuminate\Http\Resources\Json\JsonResource;

class WaybillHistoryResource extends JsonResource
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
            'waybill_id' => $this->waybill_id,
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
}
