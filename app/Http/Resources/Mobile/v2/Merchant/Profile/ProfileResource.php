<?php

namespace App\Http\Resources\Mobile\v2\Merchant\Profile;

use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Mobile\Staff\StaffResource;
use App\Http\Resources\Mobile\City\CityResource;
use App\Http\Resources\Mobile\PaymentType\PaymentTypeResource;
use App\Http\Resources\Mobile\v2\Merchant\Branch\BranchCollection;
use App\Http\Resources\Mobile\v2\Merchant\Attachment\AttachmentResource;
use App\Http\Resources\Mobile\MerchantAssociate\MerchantAssociateCollection;
use App\Http\Resources\Mobile\v2\Merchant\AccountInformation\AccountInformationCollection;

class ProfileResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
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
            'pending_balance'            => $this->pending_balance(),
            'is_discount'                => $this->is_discount,
            'is_allow_multiple_pickups'  => $this->is_allow_multiple_pickups,
            'rewards'                    => $this->rewards,
            'is_root_merchant'           => $this->is_root_merchant,
            'max_withdraw_days'          => $this->max_withdraw_days,
            'image_url'                  => $url,
            'default_payment_type'       => $this->when($this->relationLoaded('payment_type'), 
											function () {
												$payment_type = PaymentTypeResource::make($this->payment_type);
												return $payment_type->resource != null ? $payment_type->only('name', 'name_mm') : null;
											}),
            'city'                       => CityResource::make($this->whenLoaded('city')),
            'staff'                      => StaffResource::make($this->whenLoaded('staff'))->only(['name','phone']),
            'branches'                   => BranchCollection::make($this->whenLoaded('merchant_associates')),
            'banks'                      => AccountInformationCollection::make($this->whenLoaded('account_informations')),
            // 'account'                    => AccountResource::make($this->whenLoaded('account')),
            'static_price_same_city'     => $this->static_price_same_city,
            'static_price_diff_city'     => $this->static_price_diff_city,
            'static_price_branch'        => $this->static_price_branch,
            'is_corporate_merchant'      => $this->is_corporate_merchant,
            'facebook'                   => $this->facebook,
            'facebook_url'               => $this->facebook_url,
            'created_at'                 => $this->created_at->format('Y-m-d'),
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
