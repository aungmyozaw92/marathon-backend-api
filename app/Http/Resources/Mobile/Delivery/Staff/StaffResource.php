<?php

namespace App\Http\Resources\Mobile\Delivery\Staff;

use App\Http\Resources\Mobile\Zone\ZoneResource;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\HeroBadge\HeroBadgeResource;
use App\Http\Resources\Mobile\Department\DepartmentResource;
use App\Http\Resources\Mobile\CourierType\CourierTypeResource;

class StaffResource extends JsonResource
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
            'id'           => $this->id,
            'name'         => $this->name,
            // 'name'         => getConvertedUni2Zg($this->name),
            'points'       => $this->points,
            'username'     => $this->username,
            'phone'        => $this->phone,
            'car_no'       => $this->car_no,
            'daily_commission' => ($this->daily_commission) ? $this->daily_commission->sum('voucher_commission') + $this->daily_commission->sum('zone_commission') : 0,
            'department'   => DepartmentResource::make($this->whenLoaded('department')),
            'zone'         => ZoneResource::make($this->whenLoaded('zone')),
            'courier_type' => CourierTypeResource::make($this->whenLoaded('courier_type')),
            'hero_badge'   => HeroBadgeResource::make($this->whenLoaded('hero_badge'))
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
