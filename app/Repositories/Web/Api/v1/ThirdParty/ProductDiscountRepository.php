<?php

namespace App\Repositories\Web\Api\v1\ThirdParty;

use App\Models\ProductDiscount;
use App\Repositories\BaseRepository;

class ProductDiscountRepository extends BaseRepository
{
    /**
     * @return string
     */
    public function model()
    {
        return ProductDiscount::class;
    }

    /**
     * @param array $data
     *
     * @return ProductDiscount
     */
    public function create(array $data) : ProductDiscount
    {
        return ProductDiscount::create([
            'parcel_id' => $data['parcel_id'],
            'merchant_id' => auth()->user()->id,
            'discount_type' => $data['discount_type'],
            'amount' => $data['amount'],
            'min_qty' => $data['min_qty'],
            'is_inclusive' => isset($data['is_inclusive'])?$data['is_inclusive']:0,
            'is_exclusive' => isset($data['is_exclusive'])?$data['is_exclusive']:0,
            'is_foc' => isset($data['is_foc'])?$data['is_foc']:0,
            'start_date' => $data['start_date'],
            'end_date' => $data['end_date'],
            'created_by_id' => auth()->user()->id,
            'created_by_type' => 'Merchant',
        ]);
    }

    /**
     * @param ProductDiscount  $product_discount
     * @param array $data
     *
     * @return mixed
     */
    public function update(ProductDiscount $product_discount, array $data) : ProductDiscount
    {
        
        $product_discount->parcel_id = isset($data['parcel_id']) ? $data['parcel_id'] : $product_discount->parcel_id;
        $product_discount->merchant_id = isset($data['merchant_id']) ? $data['merchant_id'] : $product_discount->merchant_id;
        $product_discount->discount_type = isset($data['discount_type']) ? $data['discount_type'] : $product_discount->discount_type;
        $product_discount->amount = isset($data['amount']) ? $data['amount'] : $product_discount->amount;
        $product_discount->min_qty = isset($data['min_qty']) ? $data['min_qty'] : $product_discount->min_qty;
        $product_discount->is_inclusive = isset($data['is_inclusive']) ? $data['is_inclusive'] : $product_discount->is_inclusive;
        $product_discount->is_exclusive = isset($data['is_exclusive']) ? $data['is_exclusive'] : $product_discount->is_exclusive;
        $product_discount->is_foc = isset($data['is_foc']) ? $data['is_foc'] : $product_discount->is_foc;
        $product_discount->start_date = isset($data['start_date']) ? $data['start_date'] : $product_discount->start_date;
        $product_discount->end_date = isset($data['end_date']) ? $data['end_date'] : $product_discount->end_date;
        
        if($product_discount->isDirty()) {
            $product_discount->updated_by_id = auth()->user()->id;
            $product_discount->updated_by_type = 'Merchant';
            $product_discount->save();
        }
        return $product_discount->refresh();
    }

    /**
     * @param ProductDiscount $product_discount
     */
    public function destroy(ProductDiscount $product_discount)
    {
        $deleted = $this->deleteById($product_discount->id);

        if ($deleted) {
            $product_discount->deleted_by_id = auth()->user()->id;
            $product_discount->deleted_by_type = 'Merchant';
            $product_discount->save();
        }
    }
}

