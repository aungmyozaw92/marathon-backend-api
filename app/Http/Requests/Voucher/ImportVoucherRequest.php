<?php

namespace App\Http\Requests\Voucher;

use App\Http\Requests\FormRequest;

class ImportVoucherRequest extends FormRequest
{
    protected $casts = ['bus_station' => 'boolean'];
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
        // To do multiple validation rule for bus station and normal
        return [
            'vouchers' => 'required|array',
            'vouchers.*.pickup_id' => 'required|integer|exists:pickups,id',

            'vouchers.*.receiver_name'           => 'required|string|max:255',
            'vouchers.*.receiver_phone'          => 'required|numeric',
            'vouchers.*.receiver_address'        => 'nullable|string',
            'vouchers.*.payment_type_id'         => 'required|integer|exists:payment_types,id',
            'vouchers.*.total_item_price'        => 'required_without:parcels|regex:/^\d{1,14}(\.\d{1,2})?$/|max:2000000',
            'vouchers.*.receiver_city_id'       => 'nullable|integer|exists:cities,id|required_if:bus_station,==,false',
            
            'vouchers.*.sender_bus_station_id'   => 'nullable|integer|required_if:bus_station,1',
            'vouchers.*.receiver_bus_station_id' => 'nullable|integer|required_if:bus_station,1',
            'vouchers.*.sender_gate_id'          => 'nullable|integer|required_if:bus_station,1',

            'vouchers.*.postpone_date'           => 'nullable|after:today|date_format:Y-m-d',
            'vouchers.*.take_insurance'          => 'nullable|boolean',
            
            'vouchers.*.call_status_id'          => 'nullable|integer|exists:call_statuses,id',
            'vouchers.*.delivery_status_id'      => 'nullable|integer|exists:delivery_statuses,id',
            'vouchers.*.store_status_id'         => 'nullable|integer|exists:store_statuses,id',
        ];
    }
}
