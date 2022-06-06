<?php

namespace App\Http\Resources\Mobile\Attachment;

use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Resources\Json\JsonResource;

class AttachmentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return array
     */
    public function toArray($request)
    {

        $width = 0;
        $height = 0;
        $path = 'merchant';

        $date_path = $this->created_at->format('F-Y');
        
        $url = Storage::url($path . '/' . $date_path . '/' . $this->image);

        return [
            'id' => $this->id,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'longitude' => $this->longitude,
            'is_sign' => $this->is_sign,
            'note' => $this->note,
            'image' => $this->image,
            'image_url' => $url,
            'width' => $width,
            'height' => $height,
            'is_show_merchant' => $this->is_show_merchant,

            //'parcels' => ParcelCollection::make($this->whenLoaded('parcels')),
        ];
    }

    /**
     * Get additional data that should be returned with the resource array.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return array
     */
    public function with($request)
    {
        return [
            'status' => 1,
        ];
    }
}
