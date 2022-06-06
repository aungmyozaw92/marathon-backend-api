<?php

namespace App\Repositories\Web\Api\v1\ThirdParty;

use App\Models\ProductVariation;
use App\Repositories\BaseRepository;

class ProductVariationRepository extends BaseRepository
{
    /**
     * @return string
     */
    public function model()
    {
        return ProductVariation::class;
    }

    /**
     * @param array $data
     *
     * @return ProductVariation
     */
    public function create(array $data) : ProductVariation
    {
        return ProductVariation::create([
            'product_id' => $data['product_id'],
            'variation_meta_id' => $data['variation_meta_id'],
            'created_by_id' => auth()->user()->id,
            'created_by_type' => 'Merchant',
        ]);
    }

    /**
     * @param ProductVariation  $product_variation
     * @param array $data
     *
     * @return mixed
     */
    public function update(ProductVariation $product_variation, array $data) : ProductVariation
    {
        
        $product_variation->product_id = isset($data['product_id']) ? $data['product_id'] : $product_variation->product_id;
        $product_variation->variation_meta_id = isset($data['variation_meta_id']) ? $data['variation_meta_id'] : $product_variation->variation_meta_id;
        if($product_variation->isDirty()) {
            $product_variation->updated_by_id = auth()->user()->id;
            $product_variation->updated_by_type = 'Merchant';
            $product_variation->save();
        }

        return $product_variation->refresh();
    }

    /**
     * @param ProductVariation $product_variation
     */
    public function destroy(ProductVariation $product_variation)
    {
        $deleted = $this->deleteById($product_variation->id);

        if ($deleted) {
            $product_variation->deleted_by_id = auth()->user()->id;
            $product_variation->deleted_by_type = 'Merchant';
            $product_variation->save();
        }
    }
}

