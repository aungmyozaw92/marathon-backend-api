<?php

namespace App\Http\Requests\DeliSheet;

use App\Http\Requests\FormRequest;

class ChangeDeliveryRequest extends FormRequest
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
            'delivery_id' => 'required|integer|exists:staffs,id',
            'deli_sheet_id' => 'required|integer|exists:deli_sheets,id',
            'date' => 'nullable|after_or_equal:today|date_format:Y-m-d',
            'note' => 'nullable|string'
        ];
    }
}
