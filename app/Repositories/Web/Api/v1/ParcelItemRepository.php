<?php

namespace App\Repositories\Web\Api\v1;

use App\Models\ParcelItem;
use App\Repositories\BaseRepository;

class ParcelItemRepository extends BaseRepository
{
    /**
     * @return string
     */
    public function model()
    {
        return ParcelItem::class;
    }

    /**
     * @param array $data
     *
     * @return Parcel
     */
    public function create($data, $parcel_id = null)
    {
        $parcel_item = ParcelItem::create([
            'parcel_id'   => $parcel_id,
            'item_name'   => isset($data['item_name']) ? $data['item_name'] : "Parcel",
            'item_qty'    => isset($data['item_qty']) ? $data['item_qty'] : 1,
            'item_price'  => isset($data['item_price']) ? $data['item_price'] : 0,
            'item_status' =>  isset($data['item_status']) ? $data['item_status'] : null,
            'product_id' =>  isset($data['product_id']) ? $data['product_id'] : null,
            'weight' =>  isset($data['weight']) ? $data['weight'] : 0,
            'lwh' =>  isset($data['lwh']) ? $data['lwh'] : 0,
            'created_by'  => auth()->user()->id,
        ]);

        return $parcel_item;
    }

    /**
     * @param Parcel $parcel
     * @param array  $data
     *
     * @return mixed
     */
    public function update(ParcelItem $parcel_item, array $data): ParcelItem
    {
        $parcel_item->item_name   = isset($data['item_name']) ? $data['item_name'] : $parcel_item->item_name;
        $parcel_item->item_qty    = isset($data['item_qty']) ? $data['item_qty'] : $parcel_item->item_qty;
        $parcel_item->item_price  = isset($data['item_price']) ? $data['item_price'] : $parcel_item->item_price;
        $parcel_item->item_status = isset($data['item_status']) ? $data['item_status'] : $parcel_item->item_status;
        $parcel_item->weight = isset($data['weight']) ? $data['weight'] : $parcel_item->weight;
        $parcel_item->lwh = isset($data['lwh']) ? $data['lwh'] : $parcel_item->lwh;
        $parcel_item->product_id = array_key_exists('product_id', $data) ? $data['product_id'] : $parcel_item->product_id;

        if ($parcel_item->isDirty()) {
            $parcel_item->updated_by = auth()->user()->id;
            $parcel_item->save();
        }

        // if (isset($data['global_scale_id'])) {
        //     $parcel_item->parcel->update(['global_scale_id' => $data['global_scale_id']]);
        // }

        return $parcel_item->refresh();
    }

    /**
     * @param Parcel $parcel_item
     */
    public function destroy(ParcelItem $parcel_item)
    {
        $deleted = ParcelItem::find($parcel_item->id)->forceDelete();

        if ($deleted) {
            $parcel_item->deleted_by = auth()->user()->id;
            $parcel_item->save();
        }
    }
}
