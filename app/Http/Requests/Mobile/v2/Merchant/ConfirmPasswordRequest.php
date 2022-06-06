<?php

namespace App\Http\Requests\Mobile\v2\Merchant;

use App\Http\Requests\FormRequest;

class ConfirmPasswordRequest extends FormRequest
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
            // 'username' => 'required|string|exists:merchants,username',
            'password' => 'required|string|min:6'
        ];
    }
}
