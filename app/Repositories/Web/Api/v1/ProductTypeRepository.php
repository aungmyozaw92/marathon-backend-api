<?php

namespace App\Repositories\Web\Api\v1;

use App\Models\ProductType;
use App\Repositories\BaseRepository;

class ProductTypeRepository extends BaseRepository
{
    /**
     * @return string
     */
    public function model()
    {
        return ProductType::class;
    }

    /**
     * @param array $data
     *
     * @return ProductType
     */
    public function create(array $data) : ProductType
    {
        return ProductType::create([
            'name'   => getConvertedString($data['name']),
            'merchant_id' => $data['merchant_id'],
            'created_by_id'  => auth()->user()->id,
            'created_by_type'  => 'Staff',
        ]);
    }

    /**
     * @param ProductType  $product
     * @param array $data
     *
     * @return mixed
     */
    public function update(ProductType $product_type, array $data) : ProductType
    {
        $product_type->name = isset($data['name']) ? getConvertedString($data['name']) : $product_type->name;
        $product_type->merchant_id = isset($data['merchant_id']) ? getConvertedString($data['merchant_id']) : $product_type->merchant_id;

        if ($product_type->isDirty()) {
            $product_type->updated_by_id = auth()->user()->id;
            $product_type->updated_by_type = 'Staff';
            $product_type->save();
        }

        return $product_type->refresh();
    }

    /**
     * @param ProductType $product_type
     */
    public function destroy(ProductType $product_type)
    {
        $deleted = $this->deleteById($product_type->id);

        if ($deleted) {
            $product_type->deleted_by_id = auth()->user()->id;
            $product_type->deleted_by_type = 'Staff';
            $product_type->save();
        }
    }
}
