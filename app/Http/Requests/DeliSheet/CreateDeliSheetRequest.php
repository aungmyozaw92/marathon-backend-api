<?php

namespace App\Http\Requests\DeliSheet;

use App\Http\Requests\FormRequest;

class CreateDeliSheetRequest extends FormRequest
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
            'qty' => 'required|integer',
            'zone_id' => 'required|integer|exists:zones,id',
            'delivery_id' => 'nullable|integer|exists:staffs,id',
            'staff_id' => 'nullable|integer|exists:staffs,id',
            'date' => 'nullable|after_or_equal:today|date_format:Y-m-d',
            'note' => 'nullable|string',
            // 'priority' => 'required|in:0,1,2',
            'vouchers' => 'nullable|array',
            'vouchers.*id' => 'nullable|integer|exists:vouchers,id',
            'vouchers.*.deli_sheet_voucher_note' => 'nullable|string',
            'vouchers.*.deli_sheet_voucher_priority' => 'required|in:0,1,2',
        ];
    }
}
