<?php

namespace App\Http\Requests\MerchantDashboard\IncompleteVoucher;

use App\Http\Requests\FormRequest;

class UpdateIncompletVoucherRequest extends FormRequest
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
            'receiver_name'           => 'nullable|string|max:255',
            'receiver_phone'          => 'nullable|numeric',
            //'receiver_phone'          => 'required|string|phone:MM',
            'receiver_address'        => 'nullable|string',

            'receiver_city_id'        => 'nullable|integer|exists:cities,id',
            'sender_id'               => 'required|integer|exists:merchants,id',
            'receiver_zone_id'        => 'nullable|integer|exists:zones,id',
        ];
    }
}
