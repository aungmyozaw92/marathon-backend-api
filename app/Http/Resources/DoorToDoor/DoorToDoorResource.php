<?php

namespace App\Http\Resources\DoorToDoor;

use App\Http\Resources\Route\RouteResource;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\GlobalScale\GlobalScaleResource;

class DoorToDoorResource extends JsonResource
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
            'route' => RouteResource::make($this->route),
            'global_scale' => GlobalScaleResource::make($this->global_scale),
            'base_rate' => $this->base_rate,
            'agent_base_rate' => $this->agent_base_rate,
            'salt' => $this->salt,
            'agent_salt' => $this->agent_salt
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
