<?php

namespace App\Http\Resources\Staff;

use App\Models\Zone;
use App\Http\Resources\City\CityResource;
use App\Http\Resources\Role\RoleResource;
use App\Http\Resources\Zone\ZoneResource;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\HeroBadge\HeroBadgeResource;
use App\Http\Resources\Department\DepartmentResource;
use App\Http\Resources\CourierType\CourierTypeResource;

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
            'id'                => $this->id,
            'name'              => $this->name,
            'phone'              => $this->phone,
            'role'              => RoleResource::make($this->whenLoaded('role')),
            'username'          => $this->username,
            'department'        => DepartmentResource::make($this->whenLoaded('department')),
            'city'              => CityResource::make($this->whenLoaded('city')),
            'zone'              => ZoneResource::make($this->whenLoaded('zone')),
            'courier_type'      => CourierTypeResource::make($this->whenLoaded('courier_type')),
            'car_no'            => $this->car_no,
            'is_present'        => $this->is_present,
            'points'            => $this->points,
            'staff_type'        => $this->staff_type,
            'is_commissionable' => $this->is_commissionable,
            'hero_badge'        => HeroBadgeResource::make($this->whenLoaded('hero_badge')),
            'is_pointable'      => $this->is_pointable
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
