<?php

namespace App\Http\Requests\MerchantDashboard\Customer;

use App\Http\Requests\FormRequest;

class UpdateCustomerRequest extends FormRequest
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
            'name' => 'required|string',
            'email' => 'nullable|email|unique:customers,email,' . $this->route('customer')->id,
            'phone' => 'required|regex:/^\d{1,14}(\.\d{1,2})?$/|unique:customers,phone,' . $this->route('customer')->id,
            'other_phone' => 'nullable|regex:/^\d{1,14}(\.\d{1,2})?$/',
            'address' => 'required|string',
            'point' => 'nullable|digits_between:1,14',
            'city_id' => 'required|integer|exists:cities,id',
            'zone_id' => 'required|integer|exists:zones,id',
            // 'badge_id' => 'required|integer|exists:badges,id',
        ];
    }
}
