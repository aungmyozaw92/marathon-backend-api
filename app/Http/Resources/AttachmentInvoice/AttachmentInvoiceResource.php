<?php

namespace App\Http\Resources\AttachmentInvoice;

use Illuminate\Support\Facades\Storage;
use App\Http\Resources\City\CityResource;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Merchant\MerchantResource;
use App\Http\Resources\Merchant\MerchantCustomResource;
use App\Http\Resources\AttachmentInvoiceJournal\AttachmentInvoiceJournalResource;
use App\Http\Resources\AttachmentInvoiceJournal\AttachmentInvoiceJournalCollection;

class AttachmentInvoiceResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {

        $path = 'invoice';

        $date_path = $this->created_at->format('F-Y');

        $exists = Storage::disk('dospace')->exists($path . '/' . $date_path . '/' . $this->image);
        if ($exists) {
            $url = Storage::url($path . '/' . $date_path . '/' . $this->image);
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
            // 'medium_image_url' => $medium_url,
            // 'small_image_url' => $small_url,
            // 'width' => $width,
            // 'height' => $height,
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
