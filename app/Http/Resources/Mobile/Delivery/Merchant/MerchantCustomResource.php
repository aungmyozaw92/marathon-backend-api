<?php

namespace App\Http\Resources\Merchant;

use Illuminate\Support\Facades\Storage;
use App\Http\Resources\City\CityResource;
use App\Http\Resources\Staff\StaffResource;
use App\Http\Resources\Account\AccountResource;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\MerchantDiscount\MerchantDiscountCollection;
use App\Http\Resources\MerchantAssociate\MerchantAssociateCollection;
use App\Http\Resources\AccountInformation\AccountInformationCollection;

class MerchantCustomResource extends JsonResource
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
        $this->condition = !str_contains($request->route()->uri(), 'pickups');
        $attachment = $this->attachments;
        if ($attachment->count() > 0) {
            $date_path = $attachment[0]->created_at->format('F-Y');
            $url = Storage::url('merchant' . '/' . $date_path . '/' . $attachment[0]->image);
        } else {
            $url = null;
        }
        return [
            'id'                         => $this->id,
            'name'                       => $this->name,
            'username'                   => $this->username,
            'current_sale_count'         => $this->current_sale_count,
            'available_coupon'           => $this->available_coupon,
            'balance'                    => $this->account? $this->account->balance : 0,
            // 'pending_balance'            => $this->pending_balance(),
            'is_discount'                => $this->is_discount,
            'is_allow_multiple_pickups'  => $this->is_allow_multiple_pickups,
            'rewards'                    => $this->rewards,
            'is_root_merchant'           => $this->is_root_merchant,
            'image_url'                  => $url,

            'deleted_at'                 => optional($this->deleted_at)->format('Y-m-d'),
            // 'city'                       => CityResource::make($this->whenLoaded('city')),
            // 'staff'                      => StaffResource::make($this->whenLoaded('staff')),
            // 'branches'                   => $this->when($this->condition, MerchantAssociateCollection::make(
            //     $this->whenLoaded('merchant_associates')
            // )),
            // 'discounts'                  => MerchantDiscountCollection::make($this->whenLoaded('merchant_discounts')),
            'staff'                      => StaffResource::make($this->whenLoaded('staff')),
            // 'account_informations'       => AccountInformationCollection::make($this->whenLoaded('account_informations')),
            // 'account'                    =>  AccountResource::make($this->whenLoaded('account')),
            'static_price_same_city'     => $this->static_price_same_city,
            'static_price_diff_city'     => $this->static_price_diff_city,
            'static_price_branch'        => $this->static_price_branch,
            'is_corporate_merchant'      => $this->is_corporate_merchant,
            'facebook'                   => $this->facebook,
            'facebook_url'               => $this->facebook_url
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
