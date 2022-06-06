<?php

namespace App\Http\Requests\Mobile\Inventory;

use App\Http\Requests\FormRequest;

class CreateInventoryRequest extends FormRequest
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
            'product_id'  => 'required|integer|unique:inventories,product_id|exists:products,id,deleted_at,NULL,merchant_id,'.auth()->user()->id,
            'minimum_stock' => 'nullable',
            'qty' => 'required|integer|gt:0',
            'purchase_price' => 'nullable|regex:/^\d{1,14}(\.\d{1,2})?$/',
            'sale_price' => 'required|regex:/^\d{1,14}(\.\d{1,2})?$/',
            'is_refundable' => 'nullable|in:1,0',
            'is_taxable' => 'nullable|in:1,0',
            'is_fulfilled_by' => 'nullable|in:1,0',
            'vendor_name'  => 'nullable|string',
        ];
    }
}
