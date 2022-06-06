<?php

namespace App\Http\Resources\DeliveryUnpaidSheet;

use App\Http\Resources\Zone\ZoneResource;
use App\Http\Resources\Staff\StaffResource;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Attachment\AttachmentCollection;
use App\Http\Resources\DeliveryUnpaidSheet\DeliveryUnpaidSheetCollection;

class DeliveryUnpaidSheetResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $model = class_basename(get_class($this->resource));
        $invoice_no = null;
        $cost = null;
        $prepaid_amount = null;
        $collect_amount = null;
        $created_by = null;
        if($model == 'DeliSheet'){
            $invoice_no = $this->delisheet_invoice;
            $collect_amount = $this->collect_amount;
            $created_by = $this->created_by_staff->name;
        }elseif($model == 'Waybill'){
            $invoice_no = $this->waybill_invoice; 
            $cost = $this->actual_bus_fee; 
            $created_by = $this->created_by_staff->name;
        }elseif($model == 'ReturnSheet'){
            $invoice_no = $this->return_sheet_invoice;
            $created_by = $this->created_by_staff->name;
        }elseif($model == 'Pickup'){
            $invoice_no = $this->pickup_invoice; 
            $created_by = $this->created_by->name; 
            $prepaid_amount = $this->vouchers()->prepaidAmount();

        }

        return [
            'id' => $this->id,
            'model' => $model,
            'is_closed' => $this->is_closed,
            'is_paid' => $this->is_paid,
            'actby_mobile' => $this->actby_mobile,
            'invoice_no' => $invoice_no,
            'cost' => $cost,
            'prepaid_amount' => $prepaid_amount,
            'collect_amount' => $collect_amount,
            'created_by' => $created_by,
            'commission_amount' => $this->commission_amount,
            'points' => $this->point_logs->sum('points'),
            'qty' => $this->vouchers()->count(),
            'created_at' => date('Y-m-d', strtotime($this->created_at)),

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
