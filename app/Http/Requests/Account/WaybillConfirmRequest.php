<?php

namespace App\Http\Requests\Account;

use App\Http\Requests\FormRequest;

class WaybillConfirmRequest extends FormRequest
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
            'waybill_id' => 'integer|exists:waybills,id',
            'vouchers_id'   => 'nullable|array',
            'vouchers_id.*' => 'integer|exists:vouchers,id',
        ];
    }
}
