<?php

namespace App\Http\Resources\Mobile\Operation\Pickup;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Customer\CustomerResource;
use App\Http\Resources\Merchant\MerchantResource;
use App\Http\Resources\Mobile\Operation\Staff\StaffResource;
use App\Http\Resources\Mobile\Operation\Voucher\VoucherCollection;
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
        $this->condition = !str_contains($request->route()->uri(), 'vouchers');

        if ($this->condition) {
            return [
                'id' => $this->id,
                'sender_type' => $this->sender_type,
                'sender' => $this->senderResource(),
                'sender_associate' => $this->when($this->sender_associate_id, MerchantAssociateResource::make($this->sender_associate)),
                'sender_associate_id' => $this->sender_associate_id,
                'pickup_invoice' => $this->pickup_invoice,
                // 'qty' => $this->qty,
                // 'total_delivery_amount' => $this->total_delivery_amount,
                // 'total_amount_to_collect' => $this->total_amount_to_collect,
                // 'pickup_fee' => $this->pickup_fee,
                'note' => $this->note,
                // 'note' => getConvertedUni2Zg($this->note),
                // 'opened_by' => StaffResource::make($this->whenLoaded('opened_by_staff')),
                'take_pickup_fee' => ($this->pickup_fee > 0) ? 1 : 0,
                'is_closed' => $this->is_closed,
                'total_vouchers_count' => $this->vouchers()->count(),
                'total_delivered_vouchers_count' => $this->vouchers()->where("is_closed", 1)->count(),
                'is_pickuped' => $this->is_pickuped,
                'created_at' => $this->created_at->format('d M Y'),
                'priority' => $this->priority,
                'created_by' => StaffResource::make($this->whenLoaded('created_by')),
                'created_by_type' => $this->created_by_type,
                'vouchers' => VoucherCollection::make($this->whenLoaded('vouchers')),
            ];
        } else {
            return [
                'id' => $this->id,
                'sender_associate' => $this->when($this->sender_associate_id, MerchantAssociateResource::make($this->sender_associate)),
                'pickup_invoice' => $this->pickup_invoice,
                'is_pickuped' => $this->is_pickuped,
                'priority' => $this->priority,
                'opened_by' => StaffResource::make($this->whenLoaded('opened_by_staff')),
                'created_by' => StaffResource::make($this->whenLoaded('created_by')),
                'created_by_type' => $this->created_by_type,
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

    protected function CreatedByResource()
    {
        if ($this->created_by_type == "Merchant") {
            return MerchantResource::make($this->whenLoaded('created_by'));
        } elseif ($this->created_by_type == "Staff") {
            return StaffResource::make($this->whenLoaded('created_by'));
        }
    }
}
