<?php

namespace App\Http\Resources\DeliSheetHistory;

use Illuminate\Http\Resources\Json\JsonResource;

class DeliSheetHistoryResource extends JsonResource
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
            'delisheet_id' => $this->delisheet_id,
            'status_id' => $this->log_status_id,
            'status' => $this->log_status->description,
            'status_mm' => $this->log_status->description_mm,
            'previous' => is_numeric($this->previous) ? loggedValue($this->log_status_id, $this->previous) : ($this->log_status_id === 68 && $this->previous != null ? date("jS F, Y", strtotime($this->previous)) : $this->previous),
            'next' => is_numeric($this->next) ? loggedValue($this->log_status_id, $this->next) : ($this->log_status_id === 68 ? date("jS F, Y", strtotime($this->next)) : $this->next),
            'created_by' => $this->created_by_staff->username,
            'created_time' => $this->created_at->format('H:i A'),
            'created_date' => date("jS F, Y", strtotime($this->created_at))
        ];
    }
}
