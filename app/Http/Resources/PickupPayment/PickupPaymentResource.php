<?php

namespace App\Http\Resources\PickupPayment;

use App\Http\Resources\Agent\AgentResource;
use App\Http\Resources\Staff\StaffResource;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Customer\CustomerResource;
use App\Http\Resources\Merchant\MerchantResource;
use App\Http\Resources\Voucher\VoucherCollection;
use App\Http\Resources\Attachment\AttachmentCollection;
use App\Http\Resources\Merchant\MerchantCustomResource;
use App\Http\Resources\MerchantAssociate\MerchantAssociateResource;

class PickupPaymentResource extends JsonResource
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
                // 'voucher_count' => $this->vouchers()->count(),
                'sender_type' => $this->sender_type,
                'priority' => $this->priority,
                'sender' => $this->senderResource(),
                'sender_associate' => $this->when($this->sender_associate_id, MerchantAssociateResource::make($this->sender_associate)),
                'pickup_invoice' => $this->pickup_invoice,
                //'opened_by' => StaffResource::make($this->whenLoaded('opened_by_staff')),
                'created_by' => $this->CreatedByResource(),
                'created_by_type' => $this->created_by_type,
                'assigned_by' => $this->AssignedByResource(),
                'assigned_by_type' => $this->assigned_by_type,
                'pickuped_by' => $this->PickupedByResource(),
                'pickuped_by_type' => $this->pickuped_by_type,
                'created_by' => $this->CreatedByResource(),
                'created_by_type' => $this->created_by_type,
                'payment_receive_by_type' => $this->payment_receive_by_type,
                'payment_receive_by' => $this->paymentReceiveResource(),
                'qty' => $this->qty,
                'note' => $this->note,
                'pickup_fee' => $this->pickup_fee,
                'is_closed' => $this->is_closed,
                'is_paid' => $this->is_paid,
                'is_pickuped' => $this->is_pickuped,
                // 'total_prepaid_amount' => number_format($this->vouchers()->prepaidAmount()),
                'total_prepaid_amount' => $this->vouchers()->prepaidAmount(),
                'created_at' => $this->created_at->format('Y-m-d'),
                'created_time' => $this->created_at->format('H:i A'),
                'pickup_date' =>  $this->pickup_date ?  $this->pickup_date->format('Y-m-d') : null,
                'pickup_time' =>  $this->pickup_date ? $this->pickup_date->format('H:i A') : null,
                // 'is_called' => $this->is_called,
                // 'requested_date' => $this->requested_date ?  $this->requested_date->format('Y-m-d') : null,
                'commission_amount' => $this->commission_amount
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
     protected function paymentReceiveResource()
    {
        if ($this->payment_receive_by_type == "Agent" && $this->payment_receive_by_id) {
            // return AgentResource::make($this->whenLoaded('payment_receive_by'));
             return $this->payment_receive_by->only(['id','name','phone','address','city']);
        } elseif ($this->payment_receive_by_type == "Staff" && $this->payment_receive_by_id) {
            // return StaffResource::make($this->whenLoaded('payment_receive_by_staff'));
            return $this->payment_receive_by->only(['id','name','phone','city','department','staff_type']);
        }else{
            return null;
        }
    }
}
