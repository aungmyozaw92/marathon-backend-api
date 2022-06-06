<?php

namespace App\Http\Requests\MerchantDashboard\Product;

use App\Http\Requests\FormRequest;

class CreateProductRequest extends FormRequest
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
            'item_name' => 'required|string',
            'sku' => 'nullable|string|unique:products,sku',
            'item_price' => 'required|regex:/^\d{1,14}(\.\d{1,2})?$/',
            'is_feature' => 'nullable|boolean',
            'is_seasonal' => 'nullable|boolean',
            'product_type_id' => 'nullable|integer|exists:product_types,id,deleted_at,NULL,merchant_id,'.auth()->user()->id,

            'lwh' => 'required|regex:/^\d{1,14}(\.\d{1,2})?$/|gt:0',
            'weight' => 'required|regex:/^\d{1,14}(\.\d{1,2})?$/|gt:0',
            'file' => 'nullable|mimes:jpeg,png,jpg,gif,svg|max:10048',
        ];
    }
}
