<?php

namespace App\Http\Requests\Gate;

use App\Http\Requests\FormRequest;

class UpdateGateRequest extends FormRequest
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
            'name' => 'required|string|unique:gates,name,' . $this->route('gate')->id . ',id,bus_station_id,' . $this->bus_station_id,
            // 'gate_rate' => 'required|regex:/^\d{1,14}(\.\d{1,2})?$/',
            'bus_station_id' => 'required|integer|exists:bus_stations,id|unique:gates,bus_station_id,' . $this->route('gate')->id . ',id,bus_id,' . $this->bus_id,
            'gate_debit'          => 'nullable|boolean',
        ];
    }
}
