<?php

namespace App\Http\Requests\Coupon;

use App\Http\Requests\FormRequest;

class UpdateCouponRequest extends FormRequest
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
            'amount'           => 'required|regex:/^\d{1,14}(\.\d{1,2})?$/',
            'discount_type_id' => 'required|integer|exists:discount_types,id',
            'valid_date'       => 'required|after:today|date_format:Y-m-d',
        ];
    }
}
