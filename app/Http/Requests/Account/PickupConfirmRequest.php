<?php

namespace App\Http\Requests\Account;

use App\Http\Requests\FormRequest;

class PickupConfirmRequest extends FormRequest
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
            'pickups.*' => 'integer|exists:pickups,id',
            'prepaid_vouchers_id'   => 'nullable|array',
            'prepaid_vouchers_id.*' => 'integer|exists:vouchers,id',
           // 'pickups_id'   => 'required|array',
           // 'pickups_id.*' => 'integer|exists:pickups,id',
        ];
    }
}
