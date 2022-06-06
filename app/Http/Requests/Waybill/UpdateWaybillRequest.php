<?php

namespace App\Http\Requests\Waybill;

use App\Http\Requests\FormRequest;

class UpdateWaybillRequest extends FormRequest
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
            // 'voucher_id' => 'required|integer|exists:vouchers,id',
            // 'from_bus_station_id' => 'required|integer',
            // 'to_bus_station_id' => 'required|integer',
            // 'gate_id' => 'required|integer',
            // 'city_id' => 'required|integer',
            // 'from_id' => 'required|integer',
            // 'to_id' => 'required|integer',
            // 'delivery_id' => 'required|integer|exists:staffs,id',
            // 'staff_id' => 'required|integer|exists:staffs,id',
            // 'note' => 'nullable|string',
            'to_city_id' => 'nullable|integer|exists:cities,id',
            'from_bus_station_id' => 'nullable|integer|exists:bus_stations,id',
            'to_bus_station_id' => 'nullable|integer|exists:bus_stations,id',
            'gate_id' => 'nullable|integer|exists:gates,id',
            'delivery_id' => 'nullable|integer|exists:staffs,id',
            'staff_id' => 'nullable|integer|exists:staffs,id',
            'note' => 'nullable|string',
            'actual_bus_fee' => 'nullable|required_if:is_closed,true|regex:/^\d{1,14}(\.\d{1,2})?$/',
            'is_closed' => 'nullable|boolean',
            'voucher_id' => 'nullable|array',
            'voucher_id.*' => 'integer|exists:vouchers,id'
        ];
    }
}
