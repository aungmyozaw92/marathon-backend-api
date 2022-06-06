<?php

namespace App\Http\Requests\Invoice;

use App\Http\Requests\FormRequest;

class AddInvoiceRequest extends FormRequest
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
            'temp_journal_id' => 'required|string',
        ];
    }
}
