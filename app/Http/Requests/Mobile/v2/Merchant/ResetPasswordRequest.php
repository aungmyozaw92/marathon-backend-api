<?php

namespace App\Http\Requests\Mobile\v2\Merchant;

use App\Http\Requests\FormRequest;

class ResetPasswordRequest extends FormRequest
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
            'verified_key' => 'required|integer|exists:merchants,id',
            'new_password' => 'required|string|min:6',
        ];
    }

}
