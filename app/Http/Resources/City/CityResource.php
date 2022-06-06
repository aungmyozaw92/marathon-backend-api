<?php

namespace App\Http\Resources\City;

use App\Models\Branch;
use App\Http\Resources\Agent\AgentResource;
use App\Http\Resources\Zone\ZoneCollection;
use App\Http\Resources\Agent\AgentCollection;
use App\Http\Resources\Branch\BranchResource;
use Illuminate\Http\Resources\Json\JsonResource;

class CityResource extends JsonResource
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
        
        // $is_branch = Branch::whereCityId($this->id)->exists();
        $this->condition = !str_contains($request->route()->uri(), 'waybills');
        if ($this->condition) {
            $agent = AgentResource::make($this->whenLoaded('agent'));
            
            $branch = BranchResource::make($this->whenLoaded('branch'));
        }else{
            $agent = null;
            // $agents = null;
            $branch = null;
        }
        
        return [
            'id' => $this->id,
            'name' => $this->name,
            'name_mm' => $this->name_mm,
            'is_collect_only' => $this->is_collect_only,
            'is_on_demand' => $this->is_on_demand,
            'is_available_d2d' => $this->is_available_d2d,
            'is_available_ecom' => $this->is_available_ecom,
            'zones' => ZoneCollection::make($this->whenLoaded('zones')),
            'is_branch' => ($this->branch) ? 1 : 0,
            'agent' => $agent,
            'agents' => AgentCollection::make($this->whenLoaded('agents')),
            'branch' => $branch,
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
