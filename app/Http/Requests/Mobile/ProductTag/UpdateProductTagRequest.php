<?php

namespace App\Http\Requests\Mobile\ProductTag;

use App\Models\ProductTag;
use App\Http\Requests\FormRequest;

class UpdateProductTagRequest extends FormRequest
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
            'tag_id'  => 'required|integer|exists:tags,id,deleted_at,NULL,merchant_id,'.auth()->user()->id,
            'product_id'  => 'required|integer|exists:products,id,deleted_at,NULL,merchant_id,'.auth()->user()->id,
        ];
    }

}
