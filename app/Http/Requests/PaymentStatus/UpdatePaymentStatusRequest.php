<?php

namespace App\Http\Requests\PaymentStatus;

use App\Http\Requests\FormRequest;

class UpdatePaymentStatusRequest extends FormRequest
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
            'name' => 'required|string|unique:payment_statuses,name,' . $this->route('payment_status')->id,
            'name_mm' => 'required|string|unique:payment_statuses,name_mm,' . $this->route('payment_status')->id,
        ];
    }

    // public function updatePaymentStatus($PaymentStatus)
    // {
    //     $PaymentStatus->status = $this->status;
    //     $PaymentStatus->status_mm = $this->status_mm;

    //     if($PaymentStatus->isDirty()) {
    //         $PaymentStatus->save();
    //     }
        
    //     return $PaymentStatus;
    // }
}
