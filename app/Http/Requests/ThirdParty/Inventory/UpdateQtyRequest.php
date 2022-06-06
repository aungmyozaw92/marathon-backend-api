<?php

namespace App\Http\Requests\ThirdParty\Inventory;

use App\Models\Inventory;
use App\Http\Requests\FormRequest;

class UpdateQtyRequest extends FormRequest
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
             'qty' => 'required|integer|gt:0',
        ];
    }

}
