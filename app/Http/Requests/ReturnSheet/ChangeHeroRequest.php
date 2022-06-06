<?php

namespace App\Http\Requests\ReturnSheet;

use Illuminate\Foundation\Http\FormRequest;

class ChangeHeroRequest extends FormRequest
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
            'return_sheet_id' => 'required|integer|exists:return_sheets,id',
        ];
    }
}
