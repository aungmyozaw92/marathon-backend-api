<?php

namespace App\Http\Resources\Product;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Merchant\MerchantResource;
use App\Http\Resources\Attachment\AttachmentCollection;
use App\Http\Resources\ProductType\ProductTypeResource;
use Illuminate\Support\Facades\Storage;

class ProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        if($this->attachments()->count()>0) {
        $path = 'product';
        $date_path = $this->attachments[0]->created_at->format('F-Y');
        $exists = Storage::disk('dospace')->exists($path . '/' . $date_path . '/' . $this->attachments[0]->image);
        $large = Storage::disk('dospace')->exists($path . '/large/' . $date_path . '/' . $this->attachments[0]->image);
        $medium = Storage::disk('dospace')->exists($path . '/medium/' . $date_path . '/' . $this->attachments[0]->image);
        $small = Storage::disk('dospace')->exists($path . '/small/' . $date_path . '/' . $this->attachments[0]->image);
        if ($exists) {
            $url = Storage::url($path . '/' . $date_path . '/' . $this->attachments[0]->image);
        } else if ($large) {
            $url = Storage::url($path . '/large/' . $date_path . '/' . $this->attachments[0]->image);
        } else if ($medium) {
            $url = Storage::url($path . '/medium/' . $date_path . '/' . $this->attachments[0]->image);
        } else if ($small) {
            $url = Storage::url($path . '/small/' . $date_path . '/' . $this->attachments[0]->image);
        } else {
            $url = null;
        }
        }else{
            $url =null;
        }
        return [
            'id'                => $this->id,
            'uuid'              => $this->uuid,
            'sku'               => $this->sku,
            'item_name'         => $this->item_name,
            'item_price'        => $this->item_price,
            'is_seasonal'       => $this->is_seasonal,
            'is_feature'        => $this->is_feature,
            'lwh'               => $this->lwh,
            'weight'            => $this->weight,
            'merchant'          => MerchantResource::make($this->whenLoaded('merchant')),
            'product_type'      => ProductTypeResource::make($this->whenLoaded('product_type')),
            // 'attachments'       => AttachmentCollection::make($this->whenLoaded('attachments')),
            'image'             => $url
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
