<?php

namespace App\Http\Requests\ThirdParty\ProductVariation;

use App\Http\Requests\FormRequest;

class CreateProductVariationRequest extends FormRequest
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
            'product_id'  => 'required|integer|exists:products,id,deleted_at,NULL,merchant_id,'.auth()->user()->id,
            'variation_meta_id'  => 'required|integer|exists:variation_metas,id,deleted_at,NULL,merchant_id,'.auth()->user()->id,
            // 'merchant_id'  => 'required|integer|exists:merchants,id'
        ];
    }
}
