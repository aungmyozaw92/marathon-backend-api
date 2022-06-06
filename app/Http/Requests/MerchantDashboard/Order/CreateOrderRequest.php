<?php

namespace App\Http\Requests\MerchantDashboard\Order;

use App\Http\Requests\FormRequest;

class CreateOrderRequest extends FormRequest
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
            'receiver_name' => 'required|string',
            'receiver_phone' => 'required|regex:/^\d{1,14}(\.\d{1,2})?$/',
            'receiver_address' => 'required|string',
            'receiver_email' => 'nullable|string',
            'receiver_city_id' => 'required|integer|exists:cities,id',
            'receiver_zone_id' => 'required|integer|exists:zones,id',
            'sender_zone_id' => 'nullable|integer|exists:zones,id',
            'sender_city_id' => 'nullable|integer|exists:cities,id',
            'payment_type_id' => 'nullable|integer|exists:payment_types,id',
            'global_scale_id' => 'nullable|integer|exists:global_scales,id',
            'remark' => 'nullable|string',
            'thirdparty_invoice' => 'nullable|string',
            'total_weight' => 'required|regex:/^\d{1,14}(\.\d{1,2})?$/',
            'total_qty' => 'required|regex:/^\d{1,14}(\.\d{1,2})?$/',
            'total_price' => 'required|regex:/^\d{1,14}(\.\d{1,2})?$/',
            'order_items.*' => 'required|array',
            'order_items.*.product_id' => 'nullable|integer|exists:products,id',
            'order_items.*.name' => 'required|string',
            'order_items.*.qty' => 'required|integer',
            'order_items.*.price' => 'required|regex:/^\d{1,14}(\.\d{1,2})?$/',
            'order_items.*.weight' => 'required|regex:/^\d{1,14}(\.\d{1,2})?$/',
            'payment_option' => 'required|string',
            'payment_method' => 'required|string'
        ];
    }
}