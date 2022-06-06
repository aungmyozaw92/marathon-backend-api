<?php

namespace App\Http\Requests\Customer;

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
            'phone' => 'required|phone:MM|unique:customers,phone,' . $this->route('customer')->id,
            'address' => 'required|string',
            'point' => 'nullable|digits_between:1,14',
            'city_id' => 'required|integer|exists:cities,id',
            'zone_id' => 'required|integer|exists:zones,id',
            'badge_id' => 'required|integer|exists:badges,id',
        ];
    }
}
