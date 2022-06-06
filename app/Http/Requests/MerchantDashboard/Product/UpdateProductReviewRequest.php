<?php

namespace App\Http\Requests\MerchantDashboard\Product;

use App\Http\Requests\FormRequest;

class UpdateProductReviewRequest extends FormRequest
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
            //'product_id' => 'required|integer|exists:products,id,deleted_at,NULL,merchant_id,'.auth()->user()->id,
             'rating' => 'required|integer|in:1,2,3,4,5',
            // 'note' => 'nullable|string',
            //'customer_id' => 'required|integer|exists:customers,id',
            // 'file' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg',
        ];
    }
}
