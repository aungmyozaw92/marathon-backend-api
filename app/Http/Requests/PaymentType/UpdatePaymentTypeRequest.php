<?php

namespace App\Http\Requests\PaymentType;

use App\Http\Requests\FormRequest;

class UpdatePaymentTypeRequest extends FormRequest
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
            'name' => 'required|string|unique:payment_types,name,' . $this->route('payment_type')->id,
            'name_mm' => 'required|string|unique:payment_types,name_mm,' . $this->route('payment_type')->id,
            'description' => 'nullable|string',
        ];
    }
}
