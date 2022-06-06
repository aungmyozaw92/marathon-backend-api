<?php

namespace App\Http\Resources\ThirdParty\City;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\ThirdParty\Zone\ZoneCollection;

class CityResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        // $is_branch = Branch::whereCityId($this->id)->exists();

        return [
            'id' => $this->id,
            'name' => $this->name,
            'name_mm' => $this->name_mm,
            // 'is_collect_only' => $this->is_collect_only,
            // 'is_on_demand' => $this->is_on_demand,
            // 'is_available_d2d' => $this->is_available_d2d,
            'zones' => ZoneCollection::make($this->whenLoaded('zones'))
            // 'is_branch' => ($is_branch) ? 1 : 0,
            // 'agent' => AgentResource::make($this->whenLoaded('agent')),
            // 'branch' => BranchResource::make($this->whenLoaded('branch')),
            // 'is_active' => $this->is_active,
            // 'locking' => $this->locking,
            // 'locked_by' => $this->locked_by
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
