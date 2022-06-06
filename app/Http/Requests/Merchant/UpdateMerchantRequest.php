<?php

namespace App\Http\Requests\Merchant;

use App\Models\Merchant;
use Illuminate\Validation\Rule;
use App\Models\ContactAssociate;
use App\Models\MerchantAssociate;
use App\Http\Requests\FormRequest;

class UpdateMerchantRequest extends FormRequest
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
        // dd($this->route('merchant')->id);
        return [
            'name'               => 'required|string|max:255',
            'username'           => 'required|string|unique:merchants,username,' . $this->route('merchant')->id,
            'account_code'       => 'required|numeric|starts_with:8|digits:6|unique:merchants,account_code,' . $this->route('merchant')->id,
            'password'           => 'nullable|string|min:6',
            'city_id'            => 'required|integer|exists:cities,id',
            'staff_id'           => 'required|integer|exists:staffs,id',
            'fix_pickup_price'   => 'nullable|regex:/^\d{1,14}(\.\d{1,2})?$/',
            'fix_dropoff_price'  => 'nullable|regex:/^\d{1,14}(\.\d{1,2})?$/',
            'fix_delivery_price' => 'nullable|regex:/^\d{1,14}(\.\d{1,2})?$/',
            'static_price_same_city'      => 'nullable|numeric|regex:/^\d{1,14}(\.\d{1,2})?$/',
            'static_price_diff_city'      => 'nullable|numeric|regex:/^\d{1,14}(\.\d{1,2})?$/',
            'static_price_branch'         => 'nullable|numeric|regex:/^\d{1,14}(\.\d{1,2})?$/',
            'is_corporate_merchant'       => 'nullable|boolean',
            'facebook'                    => 'nullable|string',
            'facebook_url'                => 'nullable|string',
            // 'branch'                 => 'required|array',
            'branch'                           => 'nullable|array',
            'branch.*.phone.*.phone'           => 'required|numeric',
            'branch.*.email.*.email'           => 'required|email',
            'branch.*.phone.*.phone.id'        => 'nullable|integer|exists:contact_associates,id',
            'branch.*.email.*.email.id'        => 'nullable|integer|exists:contact_associates,id',
            'branch.*.phone.*.phone.is_delete' => 'nullable|boolean',
            'branch.*.email.*.email.is_delete' => 'nullable|boolean',
            'branches.*.is_default'            => 'nullable|boolean',
            'branches.*.label'                 =>  'required_without:branches.*.is_delete|string',
            'branches.*.address'               =>  'required_without:branches.*.is_delete|string',
            'branches.*.city_id'               =>  'required_without:branches.*.is_delete|integer|exists:cities,id',
            'branches.*.zone_id'               =>  'required_without:branches.*.is_delete|integer|exists:zones,id',
            'branches.*.account_no'            =>  'nullable|string',
            'branches.*.account_name'          =>  'nullable|string',
            'account_informations'                    => 'nullable|array',
            'account_informations.*.account_no'       =>  'required_without:account_informations.*.is_delete|string',
            'account_informations.*.account_name'     =>  'required_without:account_informations.*.is_delete|string',
            'account_informations.*.is_default'       =>  'nullable|boolean',
            // 'balance' => 'nullable|string',
            // 'credit'  => 'nullable|string',
            // 'debit'   => 'nullable|string',
        ];
    }
}
