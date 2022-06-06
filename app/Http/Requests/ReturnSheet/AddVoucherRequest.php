<?php

namespace App\Http\Requests\ReturnSheet;

use App\Http\Requests\FormRequest;

class AddVoucherRequest extends FormRequest
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
            'vouchers' => 'required|array',
            'vouchers.*id' => 'nullable|integer|exists:vouchers,id',
            'vouchers.*.return_sheet_voucher_note' => 'nullable|string',
            'vouchers.*.return_sheet_voucher_priority' => 'nullable|in:0,1,2',
            'vouchers.*.return_status' => 'nullable|in:1,2,3,4,5',
        ];
    }
}
