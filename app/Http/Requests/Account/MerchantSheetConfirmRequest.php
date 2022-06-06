<?php

namespace App\Http\Requests\Account;

use App\Http\Requests\FormRequest;

class MerchantSheetConfirmRequest extends FormRequest
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
            'merchant_sheet_id' => 'integer|exists:merchant_sheets,id',
            'vouchers_id'   => 'nullable|array',
            'vouchers_id.*' => 'integer|exists:vouchers,id',
           // 'pickups_id'   => 'required|array',
           // 'pickups_id.*' => 'integer|exists:pickups,id',
        ];
    }
}
