<?php

namespace App\Http\Requests\MerchantRateCard;

use App\Http\Requests\FormRequest;

class CreateMerchantRateCardRequest extends FormRequest
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
            'merchant_id' => 'nullable|integer|exists:merchants,id',
            'merchant_associate_id' => 'nullable|integer|exists:merchant_associates,id',
            'discount_type_id' => 'required|integer|exists:discount_types,id',
            'normal_or_dropoff' => 'nullable|boolean',
            'extra_or_discount' => 'nullable|boolean',
            'sender_city_id' => 'nullable|integer|exists:cities,id',
            'receiver_city_id' => 'nullable|integer|exists:cities,id',
            'sender_zone_id' => 'nullable|integer|exists:zones,id',
            'receiver_zone_id' => 'nullable|integer|exists:zones,id',
            // 'from_bus_station_id' => 'nullable|integer|required_if:normal_or_dropoff,1',
            // 'to_bus_station_id'   => 'nullable|integer|required_if:normal_or_dropoff,1',
            'note' => 'nullable|string',
            'from_weight' => 'required|regex:/^\d{1,14}(\.\d{1,2})?$/',
            'to_weight' => 'required|regex:/^\d{1,14}(\.\d{1,2})?$/|gt:from_weight',
            'incremental_weight' => 'nullable|regex:/^\d{1,14}(\.\d{1,2})?$/',
            'platform' => 'nullable|string',

            // 'start_date' => ,
            // 'end_date' => ,
        ];
    }
}
