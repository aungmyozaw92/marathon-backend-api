<?php

namespace App\Http\Requests\Mobile\v2\Voucher;

use App\Http\Requests\FormRequest;

class CreateIncompleteRequest extends FormRequest
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

            'sender_id'               => 'required|integer',
            'payment_type_id'         => 'required|integer|exists:payment_types,id',
            'total_item_price'        => 'required_without:parcels|regex:/^\d{1,14}(\.\d{1,2})?$/',
            'total_delivery_amount'   => 'nullable|regex:/^\d{1,14}(\.\d{1,2})?$/',
            'total_amount_to_collect' => 'nullable|integer|exists:zones,id|required_if:department_id,5',
            'sender_city_id'          => 'required_if:bus_station,0|integer|exists:cities,id',


            'sender_bus_station_id'   => 'nullable|integer|required_if:bus_station,1',
            'receiver_bus_station_id' => 'nullable|integer|required_if:bus_station,1',
            'sender_gate_id'          => 'nullable|integer|required_if:bus_station,1',


            'postpone_date'           => 'nullable|after:today|date_format:Y-m-d',
            'take_insurance'          => 'nullable|boolean',


            'delegate_status_id'         => 'nullable|integer|exists:delegate_durations,id|required_if:call_status_id,5',
            'delegate_person'            => 'nullable|integer|exists:staffs,id',

            'qr_code'          => 'nullable|string',
        ];
    }
}
