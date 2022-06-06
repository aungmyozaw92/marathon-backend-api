<?php

namespace App\Http\Resources\Mobile\Delivery\Pickup;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Mobile\Staff\StaffResource;
use App\Http\Resources\Mobile\Customer\CustomerResource;
use App\Http\Resources\Mobile\Merchant\MerchantResource;
use App\Http\Resources\Mobile\Delivery\Voucher\VoucherCollection;
use App\Http\Resources\Mobile\Delivery\Attachment\AttachmentCollection;
use App\Http\Resources\Mobile\Delivery\MerchantAssociate\MerchantAssociateResource;

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
        $this->condition = !str_contains($request->route()->uri(), 'vouchers');

        if ($this->condition) {
            return [
                'id' => $this->id,
                'sender_type' => $this->sender_type,
                'priority' => $this->priority,
                'sender' => $this->senderResource(),
                'sender_associate' => $this->when($this->sender_associate_id, MerchantAssociateResource::make($this->sender_associate)),
                'pickup_invoice' => $this->pickup_invoice,
                'note' => $this->note,
                // 'note' => getConvertedUni2Zg($this->note),
                'total_vouchers_count' => $this->vouchers()->count(),
                'total_delivered_vouchers_count' => $this->vouchers()->where("is_closed", 1)->count(),
                'is_pickuped' => $this->is_pickuped,
                'pickup_fee' => $this->pickup_fee,
                'total_collect_amount' => $this->vouchers()->prepaidAmount() + $this->pickup_fee,
                'created_at' => $this->created_at->format('d M Y'),
                'created_by' => StaffResource::make($this->whenLoaded('created_by')),
                'created_by_type' => $this->created_by_type,
                'vouchers' => VoucherCollection::make($this->whenLoaded('vouchers')),
                // 'vouchers' => VoucherCollection::make($this->vouchers),
                'prepaid_vouchers' => VoucherCollection::make($this->whenLoaded('prepaid_vouchers')),
                'attachments' => AttachmentCollection::make($this->whenLoaded('attachments')),
                'commission_amount' => $this->commission_amount

            ];
        } else {
            return [
                'id' => $this->id,
                'sender_type' => $this->sender_type,
                'priority' => $this->priority,
                'sender' => $this->senderResource(),
                'sender_associate' => $this->when($this->sender_associate_id, MerchantAssociateResource::make($this->sender_associate)),
                'pickup_invoice' => $this->pickup_invoice,
                'is_pickuped' => $this->is_pickuped,
                'pickup_fee' => $this->pickup_fee,
                'total_collect_amount' => $this->vouchers()->prepaidAmount() + $this->pickup_fee,
                'opened_by' => StaffResource::make($this->whenLoaded('opened_by_staff')),
                'created_by' => StaffResource::make($this->whenLoaded('created_by')),
                'created_by_type' => $this->created_by_type,
                'prepaid_vouchers' => VoucherCollection::make($this->whenLoaded('prepaid_vouchers')),
               // 'attachments' => AttachmentCollection::make($this->whenLoaded('attachments')),
               'commission_amount' => $this->commission_amount
            ];
        }
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
}
