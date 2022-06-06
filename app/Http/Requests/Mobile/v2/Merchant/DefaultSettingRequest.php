<?php

namespace App\Http\Requests\Mobile\v2\Merchant;

use App\Http\Requests\FormRequest;

class DefaultSettingRequest extends FormRequest
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
            //
            'default_name'    => 'required|string|in:branch,bank,payment_type,cash',
            'default_value'      => 'required_if:default_name,in:branch|integer|exists:merchant_associates,id',
            'default_value'      => 'required_if:default_name,in:bank|integer|exists:account_informations,id',
            'default_value'      => 'required_if:default_name,in:payment_type|integer|exists:payment_types,id',
            'default_value'      => 'required_if:default_name,in:cash|integer',

        ];
    }
}
