<?php

namespace App\Http\Requests\ThirdParty\Pickup;

use App\Http\Requests\FormRequest;

class AddVoucherToPickupRequest extends FormRequest
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
            'voucher_id' => 'required|array',
            'voucher_id.*' => 'integer|exists:vouchers,id'
        ];
    }
}
