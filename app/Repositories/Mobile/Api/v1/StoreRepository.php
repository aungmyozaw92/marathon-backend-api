<?php

namespace App\Repositories\Mobile\Api\v1;

use App\Models\Store;
use Illuminate\Support\Str;
use App\Repositories\BaseRepository;

class StoreRepository extends BaseRepository
{
    /**
     * @return string
     */
    public function model()
    {
        return Store::class;
    }

    /**
     * @param array $data
     *
     * @return Store
     */
    public function create(array $data) : Store
    {
        return Store::create([
            'uuid'        => Str::orderedUuid(),
            'item_name'   => getConvertedString($data['item_name']),
            'item_price'  => $data['item_price'],
            'merchant_id' => auth()->user()->id,
            'created_by'  => auth()->user()->id
        ]);
    }

    /**
     * @param Store $store
     * @param array $data
     *
     * @return mixed
     */
    public function update(Store $store, array $data) : Store
    {
        $store->item_name = getConvertedString($data['item_name']);
        $store->item_price = $data['item_price'];

        if ($store->isDirty()) {
            $store->updated_by = auth()->user()->id;
            $store->save();
        }

        return $store->refresh();
    }


    /**
     * @param Store $store
     */
    public function destroy(Store $store)
    {
        $deleted = $this->deleteById($store->id);

        if ($deleted) {
            $store->deleted_by = auth()->user()->id;
            $store->save();
        }
    }
}

