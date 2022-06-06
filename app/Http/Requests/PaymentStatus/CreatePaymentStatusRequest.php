<?php

namespace App\Http\Requests\PaymentStatus;

use App\Http\Requests\FormRequest;

class CreatePaymentStatusRequest extends FormRequest
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
            'name' => 'required|string|unique:payment_statuses,name',
            'name_mm' => 'required|string|unique:payment_statuses,name_mm'
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
