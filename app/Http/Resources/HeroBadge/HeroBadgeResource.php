<?php

namespace App\Http\Resources\HeroBadge;

use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Resources\Json\JsonResource;

class HeroBadgeResource extends JsonResource
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
            'id'                   => $this->id,
            'name'                 => $this->name,
            'logo'                 => Storage::url('hero_badge_logo/' . $this->logo),
            'description'          => $this->description,
            'multiplier_point'     => $this->multiplier_point,
            'maintainence_point'   => $this->maintainence_point
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
