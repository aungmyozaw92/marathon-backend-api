<?php

namespace App\Http\Resources\Mobile\Delivery\GlobalScale;

use Illuminate\Http\Resources\Json\JsonResource;

class GlobalScaleResource extends JsonResource
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
        $this->condition = !str_contains($request->route()->uri(), 'vouchers');
       
        return [
            'id' => $this->id,
            'cbm' => $this->cbm,
            'support_weight' => $this->support_weight,
            'max_weight' => $this->max_weight,
            'description' => $this->description . ' (' . $this->cbm . ')',
            'description_mm' => $this->description_mm,
            // 'description_mm' => getConvertedUni2Zg($this->description_mm),
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
