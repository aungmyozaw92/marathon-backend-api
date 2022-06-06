<?php

namespace App\Http\Resources\Mobile\Delivery\Attachment;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Resources\Json\JsonResource;

class AttachmentResource extends JsonResource
{
    private $condition = true;
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return array
     */
    public function toArray($request)
    {
        $this->condition = str_contains($request->route()->uri(), 'pickups');

        $width = 0;
        $height = 0;
        $medium_url = null;
        $small_url = null;
        $path = ($this->is_sign) ? 'singature' : $this->condition ? 'pickup':'large';
        $date_path = $this->created_at->format('F-Y');
        
        $url = Storage::url($path . '/' . $date_path . '/' . $this->image);

        
        if (!$this->is_sign && !$this->condition) {
            $small_url = Storage::url('small/' . $date_path . '/' . $this->image);
            $medium_url = Storage::url('medium/' . $date_path . '/' . $this->image);
        }

        return [
            'id' => $this->id,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'longitude' => $this->longitude,
            'is_sign' => $this->is_sign,
            'note' => getConvertedUni2Zg($this->note),
            'image' => $this->image,
            'image_url' => $url,
            'medium_image_url' => $medium_url,
            'small_image_url' => $small_url,
            'width' => $width,
            'height' => $height,

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
