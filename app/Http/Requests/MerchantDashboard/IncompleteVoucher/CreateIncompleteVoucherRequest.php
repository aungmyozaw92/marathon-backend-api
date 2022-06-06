<?php

namespace App\Http\Requests\MerchantDashboard\IncompleteVoucher;

use App\Http\Requests\FormRequest;

class CreateIncompleteVoucherRequest extends FormRequest
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
            // 'sender_status'           => 'required|string',
            // 'sender_id'               => 'required|integer',

            'sender_city_id'          => 'required|integer|exists:cities,id',
            'sender_zone_id'          => 'nullable|integer|exists:zones,id',
            'payment_type_id'         => 'required|integer|exists:payment_types,id',
            'total_item_price'        => 'required_without:parcels|regex:/^\d{1,14}(\.\d{1,2})?$/',
            
            'parcels.*' => 'nullable|array',
            
            'parcels.*.global_scale_id' => 'nullable|integer|exists:global_scales,id',
            'parcels.*.weight'          => 'nullable',

            'parcels.*.parcel_items.*'   => 'nullable|array',
            'parcels.*.parcel_items.*.item_name'   => 'nullable|string',
            'parcels.*.parcel_items.*.item_qty'   => 'nullable|integer',
            'parcels.*.parcel_items.*.item_price'   => 'nullable|regex:/^\d{1,14}(\.\d{1,2})?$/',
            'parcels.*.parcel_items.*product_id'   => 'nullable|integer|exists:products,id',
            
            'call_status_id'          => 'nullable|integer|exists:call_statuses,id',
            'delivery_status_id'      => 'nullable|integer|exists:delivery_statuses,id',
            'store_status_id'         => 'nullable|integer|exists:store_statuses,id',
        ];
    }
}
