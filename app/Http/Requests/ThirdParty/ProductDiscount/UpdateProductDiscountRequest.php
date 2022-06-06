<?php

namespace App\Http\Requests\ThirdParty\ProductDiscount;

use App\Models\ProductDiscount;
use App\Http\Requests\FormRequest;

class UpdateProductDiscountRequest extends FormRequest
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
            'parcel_id'  => 'required|integer|exists:parcels,id',
            'discount_type'  => 'required|string',
            'amount'  => 'required|regex:/^\d{1,14}(\.\d{1,2})?$/',
            'min_qty'  => 'required|numeric|gt:0',
            'is_inclusive'  => 'nullable|boolean|in:0,1',
            'is_exclusive'  => 'nullable|boolean|in:0,1',
            'is_foc'  => 'nullable|boolean|in:0,1',
            'start_date'  => 'required|date_format:Y-m-d|after_or_equal:' . date('m/d/Y'),
            'end_date'  => 'required|date_format:Y-m-d|after:start_date:',
 
            // 'tag_id'  => 'required|integer|exists:tags,id,deleted_at,NULL,merchant_id,'.auth()->user()->id,
            // 'product_id'  => 'required|integer|exists:products,id,deleted_at,NULL,merchant_id,'.auth()->user()->id,
        ];
    }

}
