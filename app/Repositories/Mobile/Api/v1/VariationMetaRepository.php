<?php

namespace App\Repositories\Mobile\Api\v1;

use App\Models\VariationMeta;
use App\Repositories\BaseRepository;

class VariationMetaRepository extends BaseRepository
{
    /**
     * @return string
     */
    public function model()
    {
        return VariationMeta::class;
    }

    /**
     * @param array $data
     *
     * @return VariationMeta
     */
    public function create(array $data) : VariationMeta
    {
        return VariationMeta::create([
            'key' => $data['key'],
            'value' => $data['value'],
            'merchant_id' => auth()->user()->id,
            'created_by_id' => auth()->user()->id,
            'created_by_type' => 'Merchant',
        ]);
    }

    /**
     * @param VariationMeta  $variation_meta
     * @param array $data
     *
     * @return mixed
     */
    public function update(VariationMeta $variation_meta, array $data) : VariationMeta
    {
        
        $variation_meta->key = isset($data['key']) ? $data['key'] : $variation_meta->key;
        $variation_meta->value = isset($data['value']) ? $data['value'] : $variation_meta->value;
        if($variation_meta->isDirty()) {
            $variation_meta->updated_by_id = auth()->user()->id;
            $variation_meta->updated_by_type = 'Merchant';
            $variation_meta->save();
        }

        return $variation_meta->refresh();
    }

    /**
     * @param VariationMeta $variation_meta
     */
    public function destroy(VariationMeta $variation_meta)
    {
        $deleted = $this->deleteById($variation_meta->id);

        if ($deleted) {
            $variation_meta->deleted_by_id = auth()->user()->id;
            $variation_meta->deleted_by_type = 'Merchant';
            $variation_meta->save();
        }
    }
}

