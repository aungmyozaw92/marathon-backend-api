<?php

namespace App\Http\Resources\Mobile\v2\Merchant\Attachment;

use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Resources\Json\JsonResource;

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
        if (str_contains($request->route()->uri(), 'transactions')) {
            $path = 'transaction';
        } elseif (str_contains($request->route()->uri(), 'products')|| str_contains($request->route()->uri(), 'vouchers')) {
            $path = 'product/large';
        }else {
            $path = 'return_sheet';
        }
        
        $date_path = $this->created_at->format('F-Y');
        
        $url = Storage::url($path . '/' .$date_path . '/' . $this->image);

        return [
            'id' => $this->id,
            // 'latitude' => $this->latitude,
            // 'longitude' => $this->longitude,
            'image' => $this->image,
            'image_url' => $url
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
