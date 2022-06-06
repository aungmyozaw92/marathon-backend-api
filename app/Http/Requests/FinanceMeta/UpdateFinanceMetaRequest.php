<?php

namespace App\Http\Requests\FinanceMeta;

use Illuminate\Http\Request;
use App\Http\Requests\FormRequest;

class UpdateFinanceMetaRequest extends FormRequest
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
            'label' => 'required|string',
            'model' => 'required|string'
        ];
    }
}
