<?php

namespace App\Http\Resources\Mobile\Delivery\ReturnSheet;

use App\Http\Resources\Staff\StaffResource;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Attachment\AttachmentCollection;
use App\Http\Resources\Mobile\Delivery\Merchant\MerchantResource;
use App\Http\Resources\Mobile\Delivery\Voucher\VoucherCollection;
use App\Http\Resources\ReturnSheetVoucher\ReturnSheetVoucherCollection;
use App\Http\Resources\Mobile\Delivery\MerchantAssociate\MerchantAssociateResource;

class ReturnSheetResource extends JsonResource
{
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
            'return_sheet_invoice' => $this->return_sheet_invoice,
            'qty' => $this->vouchers()->count(),
            'credit' => $this->credit,
            'debit' => $this->debit,
            'balance' => $this->balance,
            'is_paid' => $this->is_paid,
            'is_returned' => $this->is_returned,
            'merchant' => MerchantResource::make($this->whenLoaded('merchant')),
            // 'merchant_associate' => $this->when($this->merchant_associate_id, MerchantAssociateResource::make($this->merchant_associate)),
            'vouchers' => ReturnSheetVoucherCollection::make($this->whenLoaded('vouchers')),
            'delivery' => StaffResource::make($this->whenLoaded('delivery')),
            'created_at' => $this->created_at->format('Y-m-d'),
            'created_time' => $this->created_at->format('H:i A'),
            'commission_amount' => $this->commission_amount,
            'attachments' => AttachmentCollection::make($this->whenLoaded('attachments')),
            'courier_type_id' => $this->courier_type_id,
            'is_commissionable' => $this->is_commissionable,
            'is_pointable'      => $this->is_pointable,
            'is_came_from_mobile'      => $this->is_came_from_mobile,
            'issuer'        => StaffResource::make($this->whenLoaded('issuer'))
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
