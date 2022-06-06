<?php

namespace App\Http\Requests\Mobile\Operation\DeliSheet;

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
            'delivery_id' => 'required|integer|exists:staffs,id',
            'note' => 'nullable|string',
        ];
    }
}
