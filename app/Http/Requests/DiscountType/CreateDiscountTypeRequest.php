<?php

namespace App\Http\Requests\DiscountType;

use App\Http\Requests\FormRequest;

class CreateDiscountTypeRequest extends FormRequest
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
            'name' => 'required|string|unique:discount_types,name',
        ];
    }
}
