<?php

namespace App\Http\Resources\BusDropOff;

use App\Http\Resources\Gate\GateResource;
use App\Http\Resources\Route\RouteResource;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\GlobalScale\GlobalScaleResource;

class BusDropOffResource extends JsonResource
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
            'route' => RouteResource::make($this->whenLoaded('route')),
            'gate' => GateResource::make($this->whenLoaded('gate')),
            'global_scale' => GlobalScaleResource::make($this->whenLoaded('global_scale')),
            // 'base_rate' => number_format($this->base_rate),
            // 'agent_base_rate' => number_format($this->agent_base_rate),
            // 'salt' => number_format($this->salt),
            'base_rate' => $this->base_rate,
            'agent_base_rate' => $this->agent_base_rate,
            'salt' => $this->salt,
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
