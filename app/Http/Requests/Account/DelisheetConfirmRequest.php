<?php

namespace App\Http\Requests\Account;

use App\Http\Requests\FormRequest;

class DelisheetConfirmRequest extends FormRequest
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
            'delisheet_id' => 'integer|exists:deli_sheets,id',
            'payment_token' => 'required|string',
            'vouchers_id'   => 'nullable|array',
            'vouchers_id.*' => 'integer|exists:vouchers,id',
           // 'pickups_id'   => 'required|array',
           // 'pickups_id.*' => 'integer|exists:pickups,id',
        ];
    }
}
