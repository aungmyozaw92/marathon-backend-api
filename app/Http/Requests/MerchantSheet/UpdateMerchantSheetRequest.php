<?php

namespace App\Http\Requests\MerchantSheet;

use App\Http\Requests\FormRequest;

class UpdateMerchantSheetRequest extends FormRequest
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
            // 'merchant_id' => 'required|integer|exists:merchants,id',
            // 'merchant_associate_id' => 'required|integer|exists:merchant_associates,id',
            // 'voucher_id' => 'nullable|array',
            // 'voucher_id.*' => 'integer|exists:vouchers,id',
            // 'qty' => 'required|integer',
            'note' => 'nullable|string'
        ];
    }
}
