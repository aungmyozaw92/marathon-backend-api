<?php

namespace App\Http\Requests\CallStatus;

use App\Http\Requests\FormRequest;

class CreateCallStatusRequest extends FormRequest
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
            'status' => 'required|string|unique:call_statuses,status',
            'status_mm' => 'required|string|unique:call_statuses,status_mm'
        ];
    }

    // public function storePaymentStatus()
    // {
    //     return PaymentStatus::create([
    //                 'status' => $this->status,
    //                 'status_mm' => $this->status_mm
    //             ]);
    // }
}
