<?php

namespace App\Http\Requests\FinanceCode;

use Illuminate\Http\Request;
use App\Http\Requests\FormRequest;

class UpdateFinanceCodeRequest extends FormRequest
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
            'name' => 'required|string',
            'code'      => 'required|string|unique:finance_codes,code,' . $this->route('finance_code')->id,
            'description' => 'nullable|string'
        ];
    }
}
