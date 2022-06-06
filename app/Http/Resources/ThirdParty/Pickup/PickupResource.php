<?php

namespace App\Http\Resources\Thirdparty\Pickup;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Merchant\MerchantResource;
use App\Http\Resources\Mobile\Staff\StaffResource;
use App\Http\Resources\ThirdParty\Voucher\VoucherCollection;
use App\Http\Resources\Mobile\MerchantAssociate\MerchantAssociateResource;

class PickupResource extends JsonResource
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
        return [
                'id' => $this->id,
                'voucher_count' => $this->vouchers()->count(),
                // 'sender_associate' => $this->when($this->sender_associate_id, MerchantAssociateResource::make($this->sender_associate)),
                // 'sender_associate_id' => $this->sender_associate_id,
                'pickup_invoice' => $this->pickup_invoice,
                // 'sender' => $this->senderResource(),
                'total_delivery_amount' => $this->total_delivery_amount,
                'total_amount_to_collect' => $this->total_amount_to_collect,
                'pickup_fee' => $this->pickup_fee,
                'total_prepaid_amount' => $this->vouchers()->prepaidAmount(),
                'note' => $this->note,
                'take_pickup_fee' => ($this->pickup_fee > 0) ? 1 : 0,
                // 'opened_by' => StaffResource::make($this->whenLoaded('opened_by_staff')),
                'total_vouchers_count' => $this->vouchers()->count(),
                'total_delivered_vouchers_count' => $this->vouchers()->where("is_closed", 1)->count(),
                'is_pickuped' => $this->is_pickuped,
                'is_closed' => $this->is_closed,
                'created_at' => $this->created_at->format('d M Y'),
                'priority' => $this->priority,
                // 'created_by' => $this->CreatedByResource(),
                // 'created_by_type' => $this->created_by_type,
                'created_at' => $this->created_at->format('Y-m-d'),
                'created_time' => $this->created_at->format('H:i A'),
                'pickup_date' => $this->pickup_date ? $this->pickup_date->format('Y-m-d') : $this->pickup_date,
                'pickup_time' =>  $this->pickup_date ? $this->pickup_date->format('H:i A') :  $this->pickup_date,
                'vouchers' => VoucherCollection::make($this->whenLoaded('vouchers')),
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

    protected function senderResource()
    {
        if ($this->sender_type == "Merchant") {
            return MerchantResource::make($this->whenLoaded('sender'));
        } elseif ($this->sender_type == "Customer") {
            return CustomerResource::make($this->whenLoaded('sender'));
        }
    }

    protected function CreatedByResource()
    {
        if ($this->created_by_type == "Merchant") {
            return MerchantResource::make($this->whenLoaded('created_by'));
        } elseif ($this->created_by_type == "Staff") {
            return StaffResource::make($this->whenLoaded('created_by'));
        }
    }
}
