<?php

namespace App\Http\Requests\BusSheet;

use App\Http\Requests\FormRequest;

class CreateBusSheetRequest extends FormRequest
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
            'vouchers_qty' => 'required|integer',
            'from_bus_station_id' => 'required|integer|exists:bus_stations,id',
            'delivery_id' => 'required|integer|exists:staffs,id',
            'staff_id' => 'nullable|integer|exists:staffs,id',
            // 'voucher_id' => 'required|array',
            // 'voucher_id.*' => 'integer|exists:vouchers,id',
            'vouchers' => 'required|array',
            'vouchers.*.id' => 'required|integer|exists:vouchers,id',
            'vouchers.*.bus_sheet_voucher_note' => 'nullable|string',
            'vouchers.*.bus_sheet_voucher_priority' => 'required|in:0,1,2'
        ];
    }
}
