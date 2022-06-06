<?php

namespace App\Http\Requests\Bank;

use Illuminate\Http\Request;
use App\Http\Requests\FormRequest;

class UpdateBankRequest extends FormRequest
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
            'name' => 'required|string|unique:banks,name,' . $this->route('bank')->id
        ];
    }
}
