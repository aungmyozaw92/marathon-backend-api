<?php

namespace App\Http\Requests\Mobile\Delivery\ReturnSheet;

use App\Http\Requests\FormRequest;

class UploadReturnSheetRequest extends FormRequest
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
            'return_sheet_id'  => 'required|integer|exists:return_sheets,id',
            'note'  => 'nullable|string',
        ];
    }
}
