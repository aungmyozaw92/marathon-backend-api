<?php

namespace App\Http\Requests\Mobile\v2\Voucher;

use App\Http\Requests\FormRequest;

class CreateVoucherRequest extends FormRequest
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
            'receiver_name'             => 'required|string|max:255',
            'receiver_phone'            => 'required|numeric',
            'receiver_address'          => 'nullable|string',
            // 'receiver_id'             => 'required|integer',
            // 'pickup_id'               => 'required|integer|exists:pickups,id',
            // 'sender_id'               => 'required|integer|exists:merchants,id',
            // 'sender_id'               => 'required|integer',
            'payment_type_id'         => 'required|integer|exists:payment_types,id',
            'total_item_price'        => 'required_without:parcels|regex:/^\d{1,14}(\.\d{1,2})?$/',
            'total_delivery_amount'   => 'nullable|regex:/^\d{1,14}(\.\d{1,2})?$/',
            'total_amount_to_collect' => 'nullable|integer|exists:zones,id|required_if:department_id,5',
            // 'sender_city_id'          => 'required_if:bus_station,0|integer|exists:cities,id',
            // 'sender_zone_id'      => 'required_if:bus_station,0|integer|exists:zone,id',
            'receiver_city_id'        => 'required|integer|exists:cities,id|required_if:bus_station,==,false',
            'receiver_zone_id'        => 'required|integer|exists:zones,id|required_if:bus_station,==,false',
            // 'receiver_city_id'        => 'nullable|integer|exists:cities,id',

            'sender_bus_station_id'   => 'nullable|integer|required_if:bus_station,1',
            'receiver_bus_station_id' => 'nullable|integer|required_if:bus_station,1',
            'sender_gate_id'          => 'nullable|integer|required_if:bus_station,1',
            //'receiver_gate_id'        => 'nullable|integer|required_if:bus_station,1',

            // 'bus_credit'              => 'nullable|boolean',
            // 'bus_fee'                 => 'nullable|regex:/^\d{1,14}(\.\d{1,2})?$/',
            // 'deposit_amount'          => 'nullable|regex:/^\d{1,14}(\.\d{1,2})?$/',

            // 'parcels.*.parcel_items.*.bus_fee'   => 'nullable|',
            // 'parcels.*.global_scale_id' => 'nullable|integer|exists:pickups,id',
            // 'parcels.*.weight'          => 'nullable|integer|exists:pickups,id',

            'postpone_date'           => 'nullable|after:today|date_format:Y-m-d',
            'take_insurance'          => 'nullable|boolean',

            // 'discount_id'             => 'nullable|integer|min:1',
            // 'discount_amount'         => 'nullable|required_with:discount_id',
            // 'coupon_id'               => 'nullable|required_with:discount_id',
            'delegate_status_id'         => 'nullable|integer|exists:delegate_durations,id|required_if:call_status_id,5',
            'delegate_person'            => 'nullable|integer|exists:staffs,id',
            //'call_status_id'          => 'required|integer|exists:call_statuses,id',
            //'delivery_status_id'      => 'required|integer|exists:delivery_statuses,id',
            //'store_status_id'         => 'required|integer|exists:store_statuses,id',
            'qr_code'          => 'nullable|string',
        ];
    }
}
