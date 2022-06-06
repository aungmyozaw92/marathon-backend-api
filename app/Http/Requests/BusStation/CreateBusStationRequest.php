<?php

namespace App\Http\Requests\BusStation;

use App\Http\Requests\FormRequest;

class CreateBusStationRequest extends FormRequest
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
            'name' => 'required|string|unique:bus_stations,name,NULL,id,zone_id,' . $this->zone_id,
            // 'lat' => [ 'required_with:long', 'regex:/^[-]?(([0-8]?[0-9])\.(\d+))|(90(\.0+)?)$/' ],
            // 'long' => [ 'required_with:lat', 'regex:/^[-]?((((1[0-7][0-9])|([0-9]?[0-9]))\.(\d+))|180(\.0+)?)$/' ],
            //'number_of_gates' => 'required|integer',
            'city_id' => 'required|integer|exists:cities,id',
            'zone_id' => 'required|integer|exists:zones,id',
            'delivery_rate'   => 'required|regex:/^\d{1,14}(\.\d{1,2})?$/',
        ];
    }
}
