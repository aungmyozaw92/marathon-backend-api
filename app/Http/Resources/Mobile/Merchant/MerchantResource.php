<?php

namespace App\Http\Resources\Mobile\Merchant;

use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Resources\Json\JsonResource;
// use App\Http\Resources\Mobile\Attachment\AttachmentResource;
// use App\Http\Resources\Mobile\Attachment\AttachmentCollection;
use App\Http\Resources\Mobile\Staff\StaffResource;
use App\Http\Resources\Mobile\MerchantDiscount\MerchantDiscountCollection;
use App\Http\Resources\Mobile\MerchantAssociate\MerchantAssociateCollection;

class MerchantResource extends JsonResource
{
    private $condition = true;
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $this->condition = ! str_contains($request->route()->uri(), 'pickups');
        $attachment = $this->attachments;
        if ($attachment->count() > 0) {
            $date_path = $attachment[0]->created_at->format('F-Y');
            $url = Storage::url('merchant' . '/' . $date_path . '/' . $attachment[0]->image);
        }else{
            $url = null;
        }

        return [
            'id'                 => $this->id,
            'name'               => $this->name,
            'username'           => $this->username,
            'city_id'            => $this->city_id,
            'is_allow_multiple_pickups'  => $this->is_allow_multiple_pickups,
            'rewards'            => $this->rewards,
            'is_ecommerce'            => $this->is_ecommerce,
            'image_url'          => $url,
            'is_root_merchant'   => $this->is_root_merchant,
            'max_withdraw_days'   => $this->max_withdraw_days,
            'balance'            => $this->account ? $this->account->balance : 0,
           // 'attachment'         => AttachmentCollection::make($this->whenLoaded('attachments')),
            'staff'              => StaffResource::make($this->whenLoaded('staff')),
            'discounts'          => MerchantDiscountCollection::make($this->whenLoaded('merchant_discounts')),
            'branches'           => $this->when(
                $this->condition,
                MerchantAssociateCollection::make(
                    $this->whenLoaded('merchant_associates')
                )
            ),
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
