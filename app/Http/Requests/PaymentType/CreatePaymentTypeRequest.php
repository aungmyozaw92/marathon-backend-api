<?php

namespace App\Http\Requests\PaymentType;

use App\Http\Requests\FormRequest;

class CreatePaymentTypeRequest extends FormRequest
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
            'name' => 'required|string|unique:payment_types,name',
            'name_mm' => 'required|string|unique:payment_types,name_mm',
            'description' => 'nullable|string',
        ];
    }
}
