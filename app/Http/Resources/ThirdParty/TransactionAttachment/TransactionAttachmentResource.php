<?php

namespace App\Http\Resources\ThirdParty\TransactionAttachment;

use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\TransactionAttachment\TransactionAttachmentCollection;

class TransactionAttachmentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $path = 'transaction/';
    
        $date_path = $this->created_at->format('F-Y');
        
        $url = Storage::url($path . $date_path . '/' . $this->image);

        return [
            // 'id' => $this->id,
            // 'latitude' => $this->latitude,
            // 'longitude' => $this->longitude,
            // 'longitude' => $this->longitude,
            // 'is_sign' => $this->is_sign,
            // 'note' => $this->note,
            'image' => $this->image,
            'image_url' => $url,
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
