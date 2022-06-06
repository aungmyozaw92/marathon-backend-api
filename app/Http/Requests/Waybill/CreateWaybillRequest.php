<?php

namespace App\Http\Requests\Waybill;

use App\Http\Requests\FormRequest;

class CreateWaybillRequest extends FormRequest
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
            'vouchers_qty' => 'nullable|integer',
            'from_city_id' => 'nullable|integer|exists:cities,id',
            'to_city_id' => 'required|integer|exists:cities,id',
            'from_bus_station_id' => 'required|integer|exists:bus_stations,id',
            'to_bus_station_id' => 'required|integer|exists:bus_stations,id',
            'gate_id' => 'required|integer|exists:gates,id',
            'from_agent_id' => 'nullable|integer|exists:agents,id',
            'to_agent_id' => 'nullable|integer|exists:agents,id',
            // 'city_id' => 'required|integer|exists:cities,id',
            'delivery_id' => 'required|integer|exists:staffs,id',
            'staff_id' => 'nullable|integer|exists:staffs,id',
            'note' => 'nullable|string',
            // 'voucher_id' => 'required|array',
            // 'voucher_id.*' => 'integer|exists:vouchers,id'
            'vouchers' => 'nullable|array',
            'vouchers.*.id' => 'nullable|integer|exists:vouchers,id',
            'vouchers.*.waybill_voucher_note' => 'nullable|string',
            'vouchers.*.waybill_voucher_priority' => 'nullable|in:0,1,2',
        ];
    }
}
