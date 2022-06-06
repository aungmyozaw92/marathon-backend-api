<?php

namespace App\Http\Resources\Mobile\v2\Merchant\ReturnSheet;

use App\Http\Resources\Staff\StaffResource;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Merchant\MerchantResource;
use App\Http\Resources\MerchantAssociate\MerchantAssociateResource;
use App\Http\Resources\Mobile\v2\Merchant\Attachment\AttachmentCollection;
use App\Http\Resources\Mobile\v2\Merchant\ReturnSheetVoucher\ReturnSheetVoucherCollection;

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
            'is_paid' => $this->is_paid,
            'is_returned' => $this->is_returned,
            'is_closed' => $this->is_closed,
            'returned_date' => $this->returned_date,
            'closed_date' => $this->closed_date,
            'created_at' => $this->created_at->format('Y-m-d'),
            'return_branch_name' => $this->merchant_associate->label,
            // 'merchant_associate' => $this->when($this->merchant_associate_id, MerchantAssociateResource::make($this->merchant_associate)),
            'vouchers' => ReturnSheetVoucherCollection::make($this->whenLoaded('vouchers')),
            'attachments' => AttachmentCollection::make($this->whenLoaded('attachments'))
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
