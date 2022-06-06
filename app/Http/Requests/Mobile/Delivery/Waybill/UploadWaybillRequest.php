<?php

namespace App\Http\Requests\Mobile\Delivery\Waybill;

use App\Http\Requests\FormRequest;

class UploadWaybillRequest extends FormRequest
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
            'waybill_id'  => 'required|integer|exists:waybills,id',
            'is_delivered' => 'required|in:0,1,2',
            'note'  => 'nullable|string',
            'actual_bus_fee' => 'nullable|required_with:is_delivered',
        ];
    }
}
