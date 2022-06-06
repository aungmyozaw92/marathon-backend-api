<?php

namespace App\Http\Resources\Qr;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\QrAssociate\QrAssociateCollection;

class QrResource extends JsonResource
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
            'qty'  => $this->qty,
            'actor_type' => $this->actor_type,
            'actor_id'  => $this->actor_id,
            'qr_associates' => QrAssociateCollection::make($this->whenLoaded('qr_associates')),
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
