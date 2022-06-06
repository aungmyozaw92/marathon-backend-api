<?php

namespace App\Repositories\Web\Api\v1;

use App\Models\Parcel;
use App\Models\Voucher;
use App\Models\ParcelItem;
use App\Models\GlobalScale;
use App\Repositories\BaseRepository;

class ParcelRepository extends BaseRepository
{
    /**
     * @return string
     */
    public function model()
    {
        return Parcel::class;
    }

    /**
     * @param array $data
     *
     * @return Parcel
     */
    public function create($data, $voucher_id = null)
    {
        $parcel = Parcel::create([
            'voucher_id'           => $voucher_id,
            'global_scale_id'      => isset($data['global_scale_id']) ? $data['global_scale_id'] : 1,
            'discount_type_id'     => isset($data['discount_type_id']) ? $data['discount_type_id'] : null,
            'coupon_associate_id'  => isset($data['coupon_associate_id']) ? $data['coupon_associate_id'] : null,
            'weight'               => isset($data['weight']) ? $data['weight'] : 2,
            'coupon_price'        => isset($data['coupon_price']) ? $data['coupon_price'] : 0,
            'agent_fee'        => isset($data['agent_fee']) ? $data['agent_fee'] : 0,

            'discount_price' => isset($data['discount_price']) ? $data['discount_price'] : 0,
            'cal_parcel_price' => isset($data['cal_parcel_price']) ? $data['cal_parcel_price'] : 0,
            'cal_delivery_price' => isset($data['cal_delivery_price']) ? $data['cal_delivery_price'] : 0,
            'cal_gate_price' => isset($data['cal_gate_price']) ? $data['cal_gate_price'] : 0,
            'label_parcel_price' => isset($data['label_parcel_price']) ? $data['label_parcel_price'] : 0,
            'label_delivery_price' => isset($data['label_delivery_price']) ? $data['label_delivery_price'] : 0,
            'label_gate_price' => isset($data['label_gate_price']) ? $data['label_gate_price'] : 0,
            'sub_total' => isset($data['sub_total']) ? $data['sub_total'] : 0,
            'origin_lwh' => isset($data['origin_lwh']) ? $data['origin_lwh'] : null,
            'origin_weight' => isset($data['origin_weight']) ? $data['origin_weight'] : null,
            'created_by'           => auth()->user()->id,
            'seller_discount' => isset($data['seller_discount']) ? $data['seller_discount'] : 0,
        ]);

        return $parcel;
    }

    /**
     * @param Parcel $parcel
     * @param array  $data
     *
     * @return mixed
     */
    public function update(Parcel $parcel, array $data): Parcel
    {
        $parcel->voucher_id           = $parcel->voucher_id;
        $parcel->global_scale_id      = isset($data['global_scale_id']) ? $data['global_scale_id'] : $parcel->global_scale_id;
        $parcel->discount_type_id     = isset($data['discount_type_id']) ? $data['discount_type_id'] : $parcel->discount_type_id;
        $parcel->coupon_associate_id  = isset($data['coupon_associate_id']) ? $data['coupon_associate_id'] : $parcel->coupon_associate_id;
        $parcel->weight               = isset($data['weight']) ? $data['weight'] : $parcel->weight;
        $parcel->origin_lwh           = isset($data['origin_lwh']) ? $data['origin_lwh'] : $parcel->origin_lwh;
        $parcel->origin_weight        = isset($data['origin_weight']) ? $data['origin_weight'] : $parcel->origin_weight;
        $parcel->coupon_price        = isset($data['coupon_price']) ? $data['coupon_price'] : $parcel->coupon_price;
        $parcel->agent_fee           = isset($data['agent_fee']) ? $data['agent_fee'] : $parcel->agent_fee;

        $parcel->discount_price = isset($data['discount_price']) ? $data['discount_price'] : $parcel->discount_price;
        $parcel->cal_parcel_price = isset($data['cal_parcel_price']) ? $data['cal_parcel_price'] : $parcel->cal_parcel_price;
        $parcel->cal_delivery_price = isset($data['cal_delivery_price']) ? $data['cal_delivery_price'] : $parcel->cal_delivery_price;
        $parcel->cal_gate_price = isset($data['cal_gate_price']) ? $data['cal_gate_price'] : $parcel->cal_gate_price;
        $parcel->label_parcel_price = isset($data['label_parcel_price']) ? $data['label_parcel_price'] : $parcel->label_parcel_price;
        $parcel->label_delivery_price = isset($data['label_delivery_price']) ? $data['label_delivery_price'] : $parcel->label_delivery_price;
        $parcel->label_gate_price = isset($data['label_gate_price']) ? $data['label_gate_price'] : $parcel->label_gate_price;
        $parcel->sub_total = isset($data['sub_total']) ? $data['sub_total'] : $parcel->sub_total;
        $parcel->seller_discount = isset($data['seller_discount']) ? $data['seller_discount'] : $parcel->seller_discount;

        if ($parcel->isDirty()) {
            $parcel->updated_by = auth()->user() ? auth()->user()->id : null;
            $parcel->save();
        }

        return $parcel->refresh();
    }

    /**
     * @param Parcel $parcel
     */
    public function destroy(Parcel $parcel)
    {
        $item_deleted = $parcel->parcel_items()->forceDelete();
        // if ($item_deleted) {
        $deleted = Parcel::find($parcel->id)->forceDelete();

        if ($deleted) {
            $parcel->deleted_by = auth()->user() ? auth()->user()->id : null;
            $parcel->save();
        }
        // }
    }
}
