<?php

namespace App\Http\Requests\DeliSheet;

use App\Http\Requests\FormRequest;

class UpdateDeliSheetRequest extends FormRequest
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
            'delivery_id' => 'required|integer|exists:staffs,id',
            'staff_id' => 'required|integer|exists:staffs,id',
            'note' => 'nullable|string',
            // 'priority' => 'required|in:0,1,2',
            'vouchers' => 'nullable|array',
            'vouchers.*.id' => 'integer|exists:vouchers,id',
            'vouchers.*.delivery_status_id' => 'integer|exists:delivery_statuses,id|in:8,9,10',
            // 'note' => 'bail|required_if:delivery_status_id,==,10|nullable|regex:/^[\p{L},\p{N},.!? ""]+(,[\p{L},\p{N},.!? ""]+){1}$/',
            'note' => 'bail|required_if:delivery_status_id,==,10|nullable|min:2',
        ];
    }
}
