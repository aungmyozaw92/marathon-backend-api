<?php

namespace App\Http\Requests\BusSheet;

use App\Http\Requests\FormRequest;

class UpdateBusSheetRequest extends FormRequest
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
            'voucher.*.id' => 'integer|exists:vouchers,id',
            'voucher.*.actual_bus_fee' => 'required|regex:/^\d{1,14}(\.\d{1,2})?$/'
        ];
    }
}
