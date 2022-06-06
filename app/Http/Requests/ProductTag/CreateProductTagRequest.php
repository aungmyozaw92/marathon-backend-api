<?php

namespace App\Http\Requests\ProductTag;

use App\Http\Requests\FormRequest;

class CreateProductTagRequest extends FormRequest
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
            'tag_id'  => 'required|integer|exists:tags,id,deleted_at,NULL',
            'product_id'  => 'required|integer|exists:products,id,deleted_at,NULL'
        ];
    }
}
