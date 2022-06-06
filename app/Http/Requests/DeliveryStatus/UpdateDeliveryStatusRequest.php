<?php

namespace App\Http\Requests\DeliveryStatus;

use App\Http\Requests\FormRequest;

class UpdateDeliveryStatusRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'status' => 'required|string|unique:delivery_statuses,status,' . $this->route('delivery_status')->id,
            'status_mm' => 'required|string|unique:delivery_statuses,status_mm,' . $this->route('delivery_status')->id,
        ];
    }

    // public function updateDeliveryStatus($deliveryStatus)
    // {
    //     $deliveryStatus->status = $this->status;
    //     $deliveryStatus->status_mm = $this->status_mm;

    //     if($deliveryStatus->isDirty()) {
    //         $deliveryStatus->save();
    //     }
        
    //     return $deliveryStatus;
    // }
}
