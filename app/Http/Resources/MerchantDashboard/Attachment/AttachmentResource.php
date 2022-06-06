<?php

namespace App\Http\Resources\MerchantDashboard\Attachment;

use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Attachment\AttachmentCollection;

class AttachmentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $path = 'product/';
        
        
        $width = 0;
        $height = 0;
        // $path = ($this->condition) ? 'transaction' : (($this->is_sign) ? 'singature' : 'large');

        $date_path = $this->created_at->format('F-Y');
        
        $url = Storage::url($path . 'large/' . $date_path . '/' . $this->image);

        $medium_url = null;
        $small_url = null;
        if (!$this->is_sign && !$this->condition) {
            $medium_url = Storage::url($path .'medium/' . $date_path . '/' . $this->image);
            $small_url = Storage::url($path .'small/' . $date_path . '/' . $this->image);
        }

        return [
            'id' => $this->id,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'longitude' => $this->longitude,
            'is_sign' => $this->is_sign,
            'note' => $this->note,
            'image' => $this->image,
            'image_url' => $url,
            'medium_image_url' => $medium_url,
            'small_image_url' => $small_url,
            'width' => $width,
            'height' => $height,
            'is_show_merchant' => $this->is_show_merchant,

            //'parcels' => ParcelCollection::make($this->whenLoaded('parcels')),
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
