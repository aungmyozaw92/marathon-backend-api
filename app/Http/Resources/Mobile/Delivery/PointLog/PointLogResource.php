<?php

namespace App\Http\Resources\Mobile\Delivery\PointLog;

use App\Http\Resources\Staff\StaffResource;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Deduction\DeductionResource;
use App\Http\Resources\HeroBadge\HeroBadgeResource;
use App\Http\Resources\Attachment\AttachmentCollection;
use App\Http\Resources\ReturnSheet\ReturnSheetResource;
use App\Http\Resources\Mobile\Agent\Journal\JournalResource;
use App\Http\Resources\Mobile\Delivery\Pickup\PickupResource;
use App\Http\Resources\Mobile\Delivery\Waybill\WaybillResource;
use App\Http\Resources\Mobile\Delivery\DeliSheet\DeliSheetResource;

class PointLogResource extends JsonResource
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
            'id'                => $this->id,
            'staff'             => StaffResource::make($this->whenLoaded('staff')),
            'points'            => $this->points,
            'status'            => $this->status,
            'resourceable_type' => $this->resourceable_type,
            'resourceable_id'   => $this->resourceable_id,
            'resourceable'      => $this->resourceable,
            // 'resource'          => $this->Resource(),
            'note'              => $this->note,
            'created_at'        => optional($this->created_at)->format('Y-m-d'),
            'hero_badge'        => HeroBadgeResource::make($this->whenLoaded('hero_badge')),
            'attachments'       => AttachmentCollection::make($this->whenLoaded('attachments')),
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

    // protected function Resource()
    // {
    //     if ($this->resourceable_type == "Deduction") {
    //         return DeductionResource::make($this->deduction);
    //     } elseif ($this->created_by_type == "Pickup") {
    //         return PickupResource::make($this->pickup);
    //     } elseif ($this->created_by_type == "DeliSheet") {
    //         return DeliSheetResource::make($this->deli_sheet);
    //     } elseif ($this->created_by_type == "Waybill") {
    //         return WaybillResource::make($this->waybill);
    //     } elseif ($this->created_by_type == "ReturnSheet") {
    //         return ReturnSheetResource::make($this->return_sheet);
    //     } elseif ($this->created_by_type == "Journal") {
    //         return JournalResource::make($this->journal);
    //     }
    // }
}
