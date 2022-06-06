<?php

namespace App\Http\Resources\DeliveryWeb\Pickup;

use App\Http\Resources\Agent\AgentResource;
use App\Http\Resources\Staff\StaffResource;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Customer\CustomerResource;
use App\Http\Resources\Merchant\MerchantResource;
use App\Http\Resources\Attachment\AttachmentCollection;
use App\Http\Resources\Merchant\MerchantCustomResource;
use App\Http\Resources\DeliveryWeb\Voucher\VoucherCollection;
use App\Http\Resources\MerchantAssociate\MerchantAssociateResource;
use App\Http\Resources\MerchantAssociate\MerchantAssociateCustomResource;

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
            'sender_type' => $this->sender_type,
            'priority' => $this->priority,
            'sender' => $this->senderResource(),
            'sender_associate' => $this->when($this->sender_associate_id, MerchantAssociateResource::make($this->sender_associate)),
            'pickup_invoice' => $this->pickup_invoice,
            'qty' => $this->qty,
            'total_delivery_amount' => $this->total_delivery_amount,
            'total_amount_to_collect' => $this->total_amount_to_collect,
            'take_pickup_fee' => ($this->pickup_fee > 0) ? 1 : 0,
            'pickup_fee' => $this->pickup_fee,
            'note' => $this->note,
            'created_by' => $this->CreatedByResource(),
            'created_by_type' => $this->created_by_type,
            'assigned_by' => $this->AssignedByResource(),
            'assigned_by_type' => $this->assigned_by_type,
            'pickuped_by' => $this->PickupedByResource(),
            'pickuped_by_type' => $this->pickuped_by_type,
            'created_at' => $this->created_at->format('Y-m-d'),
            'created_time' => $this->created_at->format('H:i A'),
            'vouchers' => VoucherCollection::make($this->whenLoaded('vouchers')),
            'is_closed' => $this->is_closed,
            'is_paid' => $this->is_paid,
            'is_pickuped' => $this->is_pickuped,
            'total_prepaid_amount' => $this->vouchers()->prepaidAmount(),
            'pickup_date' =>  $this->pickup_date ?  $this->pickup_date->format('Y-m-d') : null,
            'pickup_time' =>  $this->pickup_date ? $this->pickup_date->format('H:i A') : null,
            'commission_amount' => $this->commission_amount,
            'courier_type_id' => $this->courier_type_id,
            'is_commissionable' => $this->is_commissionable,
            'is_pointable'      => $this->is_pointable,
            'point' => $this->point_logs->sum('points'),
            'platform'      => $this->platform,
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
            return MerchantCustomResource::make($this->whenLoaded('sender'));
        } elseif ($this->sender_type == "Customer") {
            return CustomerResource::make($this->whenLoaded('sender'));
        }
    }

    protected function CreatedByResource()
    {
        if ($this->created_by_type == "Merchant") {
            return MerchantCustomResource::make($this->whenLoaded('created_by'));
        } elseif ($this->created_by_type == "Staff") {
            return StaffResource::make($this->whenLoaded('created_by'));
        }
    }
    protected function AssignedByResource()
    {
        if ($this->assigned_by_type == "Merchant") {
            return MerchantCustomResource::make($this->whenLoaded('assigned_by'));
        } elseif ($this->assigned_by_type == "Staff") {
            return StaffResource::make($this->whenLoaded('assigned_by'));
        } elseif ($this->assigned_by_type == "Agent") {
            return AgentResource::make($this->whenLoaded('assigned_by'));
        }
    }
    protected function PickupedByResource()
    {
        if ($this->pickuped_by_type == "Agent") {
            return AgentResource::make($this->whenLoaded('pickuped_by'));
        } elseif ($this->pickuped_by_type == "Staff") {
            return StaffResource::make($this->whenLoaded('pickuped_by'));
        }
    }
}
