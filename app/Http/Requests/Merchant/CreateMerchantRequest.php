<?php

namespace App\Http\Requests\Merchant;

use App\Models\Merchant;
use App\Models\MerchantAssociate;

use App\Http\Requests\FormRequest;

class CreateMerchantRequest extends FormRequest
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
            'name'                        => 'required|string|max:255',
            'username'                    => 'required|string|unique:merchants,username',
            'account_code'                => 'required|numeric|starts_with:8|digits:6|unique:merchants,account_code',
            'password'                    => 'required|string|min:6',
            'city_id'                     => 'required|integer|exists:cities,id',
            'staff_id'                    => 'required|integer|exists:staffs,id',
            'fix_pickup_price'            => 'nullable|numeric|regex:/^\d{1,14}(\.\d{1,2})?$/',
            'fix_dropoff_price'           => 'nullable|numeric|regex:/^\d{1,14}(\.\d{1,2})?$/',
            'fix_delivery_price'          => 'nullable|numeric|regex:/^\d{1,14}(\.\d{1,2})?$/',
            'static_price_same_city'      => 'nullable|numeric|regex:/^\d{1,14}(\.\d{1,2})?$/',
            'static_price_diff_city'      => 'nullable|numeric|regex:/^\d{1,14}(\.\d{1,2})?$/',
            'static_price_branch'         => 'nullable|numeric|regex:/^\d{1,14}(\.\d{1,2})?$/',
            'is_corporate_merchant'       => 'nullable|boolean',
            'facebook'                    => 'nullable|string',
            'facebook_url'                => 'nullable|string',
            // 'branches'                 => 'required|array',
            'branches'                    => 'required|array',
            'branches.*.phones.*.phone'   => 'required|numeric',
            'branches.*.emails.*.email'   => 'nullable|email',
            'branches.*.is_default'       => 'nullable|boolean',
            'branches.*.label'            =>  'required|string',
            'branches.*.address'          =>  'required|string',
            'branches.*.city_id'          =>  'required|integer|exists:cities,id',
            'branches.*.zone_id'          =>  'required|integer|exists:zones,id',
            'branches.*.account_no'       =>  'nullable|string',
            'branches.*.account_name'     =>  'nullable|string',
            'account_informations'        => 'nullable|array',
            'account_informations.*.account_no'     =>  'required|string',
            'account_informations.*.account_name'   =>  'required|string',
            'account_informations.*.is_default'     =>  'nullable|boolean',
        ];
    }
}
