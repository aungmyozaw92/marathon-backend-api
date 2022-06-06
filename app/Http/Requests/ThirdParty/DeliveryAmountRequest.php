<?php

namespace App\Http\Requests\ThirdParty;

use App\Http\Requests\FormRequest;

class DeliveryAmountRequest extends FormRequest
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
            'global_scale_id'                      => 'nullable|integer|exists:global_scales,id',
            'receiver_city_id'                     => 'required|integer|exists:cities,id',
            'receiver_zone_id'                     => 'nullable|integer|exists:zones,id',
            // 'parcels'                              => 'nullable|array',
            // 'parcels.*.global_scale_id'            => 'required_with:parcels|integer|exists:global_scales,id',
            // 'parcels.*.parcel_items'               => 'required_with:parcels|array',
            // 'parcels.*.parcel_items.*.item_name'   => 'required_with:parcels|string',
            // 'parcels.*.parcel_items.*.item_qty'    => 'required_with:parcels|integer',
            // 'parcels.*.parcel_items.*.item_price'  => 'required_with:parcels|regex:/^\d{1,14}(\.\d{1,2})?$/',
        ];

        // return [
        //     'global_scale_id'                      => 'required_without_all:parcels,lwh|integer|exists:global_scales,id',
        //     'lwh'                                  => 'required_without_all:parcels,global_scale_id|string',
        //     'receiver_city_id'                     => 'required|integer|exists:cities,id',
        //     'receiver_zone_id'                     => 'nullable|integer|exists:zones,id',
        //     'parcels'                              => 'nullable|array',
        //     'parcels.*.global_scale_id'            => 'required_without:parcels.*.lwh|integer|exists:global_scales,id',
        //     'parcels.*.lwh'                        => 'required_without:parcels.*.global_scale_id|string',
        //     'parcels.*.parcel_items'               => 'required_with:parcels|array',
        //     'parcels.*.parcel_items.*.item_name'   => 'required_with:parcels|string',
        //     'parcels.*.parcel_items.*.item_qty'    => 'required_with:parcels|integer',
        //     'parcels.*.parcel_items.*.item_price'  => 'required_with:parcels|regex:/^\d{1,14}(\.\d{1,2})?$/',
        // ];

    }
}
