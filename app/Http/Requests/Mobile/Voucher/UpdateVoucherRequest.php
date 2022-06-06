<?php

namespace App\Http\Requests\Mobile\Voucher;

use App\Http\Requests\FormRequest;

use App\Models\Voucher;

class UpdateVoucherRequest extends FormRequest
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
            'receiver_id'             => 'required|integer|exists:customers,id',
            'receiver_name'           => 'required|string|max:255',
            'receiver_phone'          => 'required|numeric',
            'receiver_address'        => 'nullable|string',
            'receiver_latitude'          => 'nullable|string',
            'receiver_longitude'          => 'nullable|string',
            'receiver_email'          => 'nullable|email',
            // 'receiver_id'             => 'required|integer',
            // 'pickup_id'               => 'required|integer|exists:pickups,id',
            'payment_type_id'         => 'required|integer|exists:payment_types,id',
            // 'total_item_price'        => 'required|regex:/^\d{1,14}(\.\d{1,2})?$/',
            // 'total_delivery_amount'   => 'nullable|regex:/^\d{1,14}(\.\d{1,2})?$/',
            // 'total_amount_to_collect'  => 'nullable|integer|exists:zones,id|required_if:department_id,5',
            // 'sender_city_id'          => 'nullable|integer|exists:cities,id',
            'receiver_city_id'        => 'nullable|integer|exists:cities,id|required_if:bus_station,==,false',
            //  'receiver_city_id_zone'        => 'required_without:receiver_zone_id|integer|exists:cities,id|required_if:bus_station,==,true',
            // 'receiver_city_id'        => 'nullable|integer|exists:cities,id',

            'sender_bus_station_id'   => 'nullable|integer|required_if:bus_station,1',
            'receiver_bus_station_id' => 'nullable|integer|required_if:bus_station,1',
            'sender_gate_id'          => 'nullable|integer|required_if:bus_station,1',
           // 'receiver_gate_id'        => 'nullable|integer|required_if:bus_station,1',
            'bus_credit'              => 'nullable|boolean',
            'bus_fee'                 => 'nullable|regex:/^\d{1,14}(\.\d{1,2})?$/',
            'deposit_amount'          => 'nullable|regex:/^\d{1,14}(\.\d{1,2})?$/',
            'postpone_date'           => 'nullable|after:today|date_format:Y-m-d',
            'take_insurance'          => 'nullable|boolean',
            'parcels'                 => 'required|array',
            'delegate_status_id'         => 'nullable|integer|exists:delegate_durations,id|required_if:call_status_id,5',
            'delegate_person'            => 'nullable|integer|exists:staffs,id',
            // 'call_status_id'          => 'required|integer|exists:call_statuses,id',
            // 'delivery_status_id'      => 'required|integer|exists:delivery_statuses,id',
            // 'store_status_id'         => 'required|integer|exists:store_statuses,id',
        ];
    }
}
