<?php

namespace App\Http\Resources\Attachment;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Resources\Json\JsonResource;

class AttachmentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return array
     */
    public function toArray($request)
    {
        $this->condition = str_contains($request->route()->uri(), 'transactions');
        $path = 'large';
        if (str_contains($request->route()->uri(), 'transactions')) {
            $path = 'transaction';
        }
        if (str_contains($request->route()->uri(), 'point_logs')) {
            $path = 'deduction';
        }
        if (str_contains($request->route()->uri(), 'vouchers')) {
            $path = 'voucher';
        }
        if (str_contains($request->route()->uri(), 'return_sheets')) {
            $path = 'return_sheet';
        }
        if (str_contains($request->route()->uri(), 'pickups')) {
            $path = 'pickup';
        }
        if (str_contains($request->route()->uri(), 'profile')) {
            $path = 'profile';
        }
        if (str_contains($request->route()->uri(), 'login')) {
            $path = 'profile';
        }
        if (str_contains($request->route()->uri(), 'finance_expenses')) {
            $path = 'finance_expense';
        }
        if (str_contains($request->route()->uri(), 'finance_advances')) {
            $path = 'finance_advance';
        }
        if (str_contains($request->route()->uri(), 'invoice')) {
            $path = 'invoice';
        }
        if (str_contains($request->route()->uri(), 'deli_sheets')) {
            if ($this->resource_type == 'DeliSheet') {
                $path = 'deli_sheet';
            } else {
                $path = 'voucher';
            }
        }
        if ($this->is_sign) {
            $path = 'singature';
        }

        $width = 0;
        $height = 0;
        // $path = ($this->condition) ? 'transaction' : (($this->is_sign) ? 'singature' : 'large');

        $date_path = $this->created_at->format('F-Y');

        $exists = Storage::disk('dospace')->exists($path . '/' . $date_path . '/' . $this->image);
        if ($exists) {
            $url = Storage::url($path . '/' . $date_path . '/' . $this->image);
        } else {
            $url = Storage::url('large/' . $date_path . '/' . $this->image);
        }


        $medium_url = null;
        $small_url = null;
        if (!$this->is_sign && !$this->condition) {
            $medium_url = Storage::url('medium/' . $date_path . '/' . $this->image);
            $small_url = Storage::url('small/' . $date_path . '/' . $this->image);
        }

        return [
            'id' => $this->id,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'longitude' => $this->longitude,
            'is_sign' => $this->is_sign,
            'note' => $this->note,
            'image' => $this->image,
            'image_url' => $url,
            'medium_image_url' => $medium_url,
            'small_image_url' => $small_url,
            'width' => $width,
            'height' => $height,
            'is_show_merchant' => $this->is_show_merchant,

            //'parcels' => ParcelCollection::make($this->whenLoaded('parcels')),
        ];
    }

    /**
     * Get additional data that should be returned with the resource array.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return array
     */
    public function with($request)
    {
        return [
            'status' => 1,
        ];
    }
}
