<?php

namespace App\Http\Requests\Mobile\Delivery\CalculateAmount;

use App\Http\Requests\FormRequest;

use App\Models\City;

class CalculateAmountRequest extends FormRequest
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
            'from_city_id' => 'required|integer|exists:cities,id',
            'to_city_id' => 'required|integer|exists:cities,id',
           // 'from_zone_id' => 'required|integer|exists:zones,id',
           // 'to_zone_id' => 'required|integer|exists:zones,id',
            'from_bus_station_id' => 'required|integer|exists:bus_stations,id',
           // 'to_bus_station_id' => 'required|integer|exists:bus_stations,id',
            //'from_gate_id' => 'required|integer|exists:gates,id',
            'from_gate_id' => 'required|integer|exists:gates,id',
            'global_scale_id' => 'required|integer|exists:global_scales,id',
            'weight' => 'required|regex:/^\d{1,14}(\.\d{1,2})?$/',
            // 'locked_by' => 'nullable|integer|exists:users,id'
        ];
    }
}
