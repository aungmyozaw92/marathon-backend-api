<?php

namespace App\Http\Resources\TrackingStatus;

use Illuminate\Http\Resources\Json\JsonResource;

class TrackingStatusResource extends JsonResource
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
            'status' => $this->status,
            'status_en' => $this->status_en,
            'status_mm' => $this->status_mm,
            'description' => $this->description,
            'description_mm' => $this->description_mm,
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
