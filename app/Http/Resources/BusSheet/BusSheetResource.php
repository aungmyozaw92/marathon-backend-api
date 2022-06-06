<?php

namespace App\Http\Resources\BusSheet;

use App\Http\Resources\Staff\StaffResource;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\BusStation\BusStationResource;
use App\Http\Resources\BusSheetVoucher\BusSheetVoucherCollection;

class BusSheetResource extends JsonResource
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
            'bus_sheet_invoice' => $this->bus_sheet_invoice,
            'vouchers_qty' => $this->qty,
            'from_bus_station' => BusStationResource::make($this->whenLoaded('from_bus_station')),
            'delivery' => StaffResource::make($this->whenLoaded('delivery')),
            'staff' => StaffResource::make($this->whenLoaded('staff')),
            'vouchers' => BusSheetVoucherCollection::make($this->whenLoaded('vouchers')),
            'is_closed' => $this->is_closed,
            'is_paid' => $this->is_paid,
            'payment' => $this->payment,
            'note' => $this->note,
            'created_at' => $this->created_at->format('Y-m-d'),
            'created_time' => $this->created_at->format('H:i A')
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
