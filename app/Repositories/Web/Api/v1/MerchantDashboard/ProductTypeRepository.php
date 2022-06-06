<?php

namespace App\Repositories\Web\Api\v1\MerchantDashboard;

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
            'merchant_id' => auth()->user()->id,
            'created_by_id'  => auth()->user()->id,
            'created_by_type'  => 'Merchant',
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
        $product_type->name = getConvertedString($data['name']);

        if ($product_type->isDirty()) {
            $product_type->updated_by_id = auth()->user()->id;
            $product_type->updated_by_type = 'Merchant';
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
            foreach ($product_type->products as $d) {
                $del = $d->delete($d->id);
                if ($del) {
                    $d->deleted_by_id = auth()->user()->id;
                    $d->deleted_by_type = 'Merchant';
                    $d->save();
                }
            }
            $product_type->deleted_by_id = auth()->user()->id;
            $product_type->deleted_by_type = 'Merchant';
            $product_type->save();
        }
    }
}
