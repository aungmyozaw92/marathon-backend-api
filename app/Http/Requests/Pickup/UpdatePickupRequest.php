<?php

namespace App\Http\Requests\Pickup;

use App\Http\Requests\FormRequest;

class UpdatePickupRequest extends FormRequest
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
            'sender_type' => 'required_without:pickup_fee|in:Merchant,Customer',
            'sender_name' => 'required_if:sender_type,Customer|string',
            'sender_phone' => 'required_if:sender_type,Customer|phone:MM|unique:customers,phone,' . $this->customer_id,
            'sender_city_id' => 'required_if:sender_type,Customer|integer|exists:cities,id',
            'sender_zone_id' => 'nullable|integer|exists:zones,id',
            // 'sender_phone' => 'required_if:sender_type,Customer|phone:MM',
            // 'sender_address' => 'required_if:sender_type,Customer|string',
            'sender_address' => 'nullable|string',
            'sender_id' => 'required_if:sender_type,Merchant|integer|exists:merchants,id',
            'customer_id' => 'required_if:sender_type,Customer|integer|exists:customers,id',
            'sender_associate_id' => 'required_if:sender_type,Merchant|integer|exists:merchant_associates,id',
            // 'qty' => 'required|integer',
            // 'total_delivery_amount' => 'required|numeric',
            // 'total_amount_to_collect' => 'required|numeric',
            'pickup_fee' => 'required_without_all:sender_type,opened_by|regex:/^\d{1,14}(\.\d{1,2})?$/',
            'note' => 'nullable|string',
            // 'type' => 'required|integer',
            // 'opened_by' => 'required_without:pickup_fee|integer|exists:staffs,id'
            'opened_by' => 'nullable|integer|exists:staffs,id',
            'agent_city_id' => 'nullable|integer|exists:cities,id',
            'requested_date'          => 'nullable|date_format:Y-m-d|after_or_equal:' . date('m/d/Y'),
        ];
    }
}
