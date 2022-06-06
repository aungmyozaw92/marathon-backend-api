<?php

namespace App\Http\Resources\CommissionLog;

use App\Http\Resources\Zone\ZoneResource;
use App\Http\Resources\Staff\StaffResource;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\HeroBadge\HeroBadgeResource;
use App\Http\Resources\Attachment\AttachmentCollection;

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
            'created_at'            => optional($this->created_at)->format('Y-m-d')
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
