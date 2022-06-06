<?php

namespace App\Http\Requests\Pickup;

use App\Http\Requests\FormRequest;

class UpdatePickupFeeRequest extends FormRequest
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
            'take_pickup_fee'          => 'required|boolean',
        ];
    }
}
