<?php

namespace App\Http\Resources\PointLog;

use App\Http\Resources\Staff\StaffResource;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\HeroBadge\HeroBadgeResource;
use App\Http\Resources\Attachment\AttachmentCollection;

class PointLogResource extends JsonResource
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
            'id'                => $this->id,
            'staff'             => StaffResource::make($this->whenLoaded('staff')),
            'points'            => $this->points,
            'status'            => $this->status,
            'resourceable_type' => $this->resourceable_type,
            'resourceable_id'   => $this->resourceable_id,
            'resourceable'      => $this->resourceable,
            'note'              => $this->note,
            'created_at'        => optional($this->created_at)->format('Y-m-d'),
            'hero_badge'        => HeroBadgeResource::make($this->whenLoaded('hero_badge')),
            'attachments'       => AttachmentCollection::make($this->whenLoaded('attachments')),
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
