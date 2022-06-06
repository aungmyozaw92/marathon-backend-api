<?php

namespace App\Http\Requests\SuperMerchant;

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
            'merchant_id'                          => 'required|integer|exists:merchants,id',
            'merchant_associate_id'                => 'required|integer|exists:merchant_associates,id',
            'receiver_name'                        => 'required|string|max:255',
            'receiver_phone'                       => 'required|numeric',
            'receiver_address'                     => 'nullable|string',
            'payment_type_id'                      => 'required|integer|exists:payment_types,id|in:1,2,3,4,9,10',
            
            'total_item_price'                     => 'required_without:parcels|regex:/^\d{1,14}(\.\d{1,2})?$/',
            'total_delivery_amount'                => 'nullable|regex:/^\d{1,14}(\.\d{1,2})?$/',
            'receiver_city_id'                     => 'required|integer|exists:cities,id|required_if:bus_station,==,false',
            'receiver_zone_id'                     => 'required|integer|exists:zones,id|required_if:bus_station,==,false',
            'take_insurance'                       => 'nullable|boolean',
            'parcels'                              => 'nullable|array',
            'parcels.*.global_scale_id'            => 'required_with:parcels|integer|exists:global_scales,id',
            'parcels.*.parcel_items'               => 'required_with:parcels|array',
            'parcels.*.parcel_items.*.item_name'   => 'required_with:parcels|string',
            'parcels.*.parcel_items.*.item_qty'    => 'required_with:parcels|integer',
            'parcels.*.parcel_items.*.item_price'  => 'required_with:parcels|regex:/^\d{1,14}(\.\d{1,2})?$/',
        ];
    }
}
