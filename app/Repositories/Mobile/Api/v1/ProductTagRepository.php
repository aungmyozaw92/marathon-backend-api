<?php

namespace App\Repositories\Mobile\Api\v1;

use App\Models\ProductTag;
use App\Repositories\BaseRepository;

class ProductTagRepository extends BaseRepository
{
    /**
     * @return string
     */
    public function model()
    {
        return ProductTag::class;
    }

    /**
     * @param array $data
     *
     * @return ProductTag
     */
    public function create(array $data) : ProductTag
    {
        return ProductTag::create([
            'product_id' => $data['product_id'],
            'tag_id' => $data['tag_id'],
            'created_by_id' => auth()->user()->id,
            'created_by_type' => 'Merchant',
        ]);
    }

    /**
     * @param ProductTag  $product_tag
     * @param array $data
     *
     * @return mixed
     */
    public function update(ProductTag $product_tag, array $data) : ProductTag
    {
        $product_tag->product_id = isset($data['product_id']) ? $data['product_id'] : $product_tag->product_id;
        $product_tag->tag_id = isset($data['tag_id']) ? $data['tag_id'] : $product_tag->tag_id;
        if($product_tag->isDirty()) {
            $product_tag->updated_by_id = auth()->user()->id;
            $product_tag->updated_by_type = 'Merchant';
            $product_tag->save();
        }

        return $product_tag->refresh();
    }

    /**
     * @param Tag $product_tag
     */
    public function destroy(ProductTag $product_tag)
    {
        $deleted = $this->deleteById($product_tag->id);

        if ($deleted) {
            $product_tag->deleted_by_id = auth()->user()->id;
            $product_tag->deleted_by_type = 'Merchant';
            $product_tag->save();
        }
    }
}

