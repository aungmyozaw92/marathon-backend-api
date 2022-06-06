<?php

namespace App\Http\Requests\Mobile\v2\Merchant;

use App\Http\Requests\FormRequest;

class ForgetPasswordRequest extends FormRequest
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
            'username' => 'required|string',
            'phone_number' => 'required|phone:MM'
        ];
    }

    public function messages()
    {
        return [
            'phone_number.phone' => 'The phone field contains an invalid number'
        ];
    }
}
