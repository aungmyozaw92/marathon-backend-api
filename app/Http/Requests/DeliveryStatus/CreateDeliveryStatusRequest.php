<?php

namespace App\Http\Requests\DeliveryStatus;

use App\Models\DeliveryStatus;
use App\Http\Requests\FormRequest;

class CreateDeliveryStatusRequest extends FormRequest
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
            'status' => 'required|string|unique:delivery_statuses,status',
            'status_mm' => 'required|string|unique:delivery_statuses,status_mm'
        ];
    }

    // public function storeDeliveryStatus()
    // {
    //     return DeliveryStatus::create([
    //                 'status' => $this->status,
    //                 'status_mm' => $this->status_mm
    //             ]);
    // }
}
