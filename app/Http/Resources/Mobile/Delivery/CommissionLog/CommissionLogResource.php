<?php

namespace App\Http\Resources\Mobile\Delivery\CommissionLog;

use App\Http\Resources\Staff\StaffResource;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Deduction\DeductionResource;
use App\Http\Resources\HeroBadge\HeroBadgeResource;
use App\Http\Resources\Attachment\AttachmentCollection;
use App\Http\Resources\ReturnSheet\ReturnSheetResource;
use App\Http\Resources\Mobile\Delivery\Zone\ZoneResource;
use App\Http\Resources\Mobile\Agent\Journal\JournalResource;
use App\Http\Resources\Mobile\Delivery\Pickup\PickupResource;
use App\Http\Resources\Mobile\Delivery\Waybill\WaybillResource;
use App\Http\Resources\Mobile\Delivery\DeliSheet\DeliSheetResource;

class CommissionLogResource extends JsonResource
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
            'id'                    => $this->id,
            'staff'                 => StaffResource::make($this->whenLoaded('staff')),
            'zone'                  => ZoneResource::make($this->whenLoaded('zone')),
            'zone_commission'       => $this->zone_commission,
            'commissionable_type'   => $this->commissionable_type,
            'commissionable_id'     => $this->commissionable_id,
            'commissionable'        => $this->commissionable,
            'voucher_commission'    => $this->voucher_commission,
            'num_of_vouchers'       => $this->num_of_vouchers,
            'created_at'            => optional($this->created_at)->format('Y-m-d'),
            'resource'              => $this->Resource(),
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

    protected function Resource()
    {
        if ($this->commissionable_type == "Pickup") {
            return PickupResource::make($this->whenLoaded('pickup'));
        // return PickupResource::make($this->loadMissing('pickup.vouchers'))->response();
        } elseif ($this->commissionable_type == "DeliSheet") {
            return DeliSheetResource::make($this->whenLoaded('deli_sheet'));
        // return (DeliSheetResource::make($this->loadMissing('deli_sheet.vouchers')))->response();
        } elseif ($this->commissionable_type == "Waybill") {
            return WaybillResource::make($this->whenLoaded('waybill'));
        // return (WaybillResource::make($this->loadMissing('waybill.vouchers')))->response();
        } elseif ($this->commissionable_type == "ReturnSheet") {
            return ReturnSheetResource::make($this->whenLoaded('return_sheet'));
            // return (ReturnSheetResource::make($this->loadMissing('return_sheet.vouchers')))->response();
        }
    }
}
