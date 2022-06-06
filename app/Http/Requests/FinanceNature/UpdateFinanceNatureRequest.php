<?php

namespace App\Http\Requests\FinanceNature;

use Illuminate\Http\Request;
use App\Http\Requests\FormRequest;

class UpdateFinanceNatureRequest extends FormRequest
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
            'name'      => 'required|string|unique:finance_natures,name,' . $this->route('finance_nature')->id,
            'description' => 'nullable|string'
        ];
    }
}
