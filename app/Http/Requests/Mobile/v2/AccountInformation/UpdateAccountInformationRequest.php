<?php

namespace App\Http\Requests\Mobile\v2\AccountInformation;

use App\Http\Requests\FormRequest;

class UpdateAccountInformationRequest extends FormRequest
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
            'account_name' => 'required|string|max:255',
            'account_no' => 'required|numeric',
            'bank_id' => 'required|integer|exists:banks,id'
        ];
    }
}
