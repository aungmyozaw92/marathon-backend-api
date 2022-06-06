<?php

namespace App\Http\Requests\Mobile\Agent\Topup;

use App\Http\Requests\FormRequest;

class CreateTopupRequest extends FormRequest
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
            'note' => 'nullable|string',
            //'type' => 'required|in:Topup,Withdraw',
            'amount' => 'required|numeric|regex:/^\d{1,14}(\.\d{1,2})?$/',
            //'city_id' => 'required|integer|exists:cities,id',
        ];
    }
}
