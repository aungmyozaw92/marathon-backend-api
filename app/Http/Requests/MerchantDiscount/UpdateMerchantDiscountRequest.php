<?php

namespace App\Http\Requests\MerchantDiscount;

use App\Http\Requests\FormRequest;

class UpdateMerchantDiscountRequest extends FormRequest
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
            'amount' => 'required|regex:/^\d{1,14}(\.\d{1,2})?$/',
            'merchant_id' => 'required|integer|exists:merchants,id',
            'discount_type_id' => 'required|integer|exists:discount_types,id',
            'normal_or_dropoff' => 'required|boolean',
            'extra_or_discount' => 'required|boolean',
            // 'sender_city_id' => 'required_if:discount_type_id,0|integer|exists:cities,id',
            // 'receiver_city_id' => 'required_if:discount_type_id,0|integer|exists:cities,id',
            // 'sender_zone_id' => 'nullable|integer|exists:zones,id',
            // 'receiver_zone_id' => 'nullable|integer|exists:zones,id',
            'from_bus_station_id' => 'nullable|integer|required_if:normal_or_dropoff,1',
            'to_bus_station_id'   => 'nullable|integer|required_if:normal_or_dropoff,1',
            'note' => 'nullable|string',
        ];
    }
}
