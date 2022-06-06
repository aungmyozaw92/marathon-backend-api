<?php

namespace App\Http\Resources\VoucherMessage;

use App\Http\Resources\Staff\StaffResource;
use Illuminate\Http\Resources\Json\JsonResource;

class VoucherMessageResource extends JsonResource
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
            // 'staff' => StaffResource::make($this->whenLoaded('staff')),
            'message_text' => $this->message_text,
            'messenger' => optional($this->messenger)->name,
            'messenger_type' => $this->messenger_type,
            // 'created_at' => $this->created_at->format('Y-m-d'),
            'created_time' => $this->created_at->format('H:i A'),
            'created_at' => date("jS F, Y", strtotime($this->created_at)),
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
