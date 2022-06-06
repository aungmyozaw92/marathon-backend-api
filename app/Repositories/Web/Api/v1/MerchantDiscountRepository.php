<?php

namespace App\Repositories\Web\Api\v1;

use App\Models\MerchantDiscount;
use App\Repositories\BaseRepository;

class MerchantDiscountRepository extends BaseRepository
{
    /**
     * @return string
     */
    public function model()
    {
        return MerchantDiscount::class;
    }
    
    /**
     * @param array $data
     *
     * @return MerchantDiscount
     */
    public function create(array $data) : MerchantDiscount
    {
        if (isset($data['note'])) {
            $note = getConvertedString($data['note']);
        }

        return MerchantDiscount::create([
            'amount' => isset($data['amount']) ? $data['amount'] : 0,
            'merchant_id' => $data['merchant_id'],
            'discount_type_id' => $data['discount_type_id'],
            'normal_or_dropoff' => isset($data['normal_or_dropoff']) ? $data['normal_or_dropoff'] : 0,
            'extra_or_discount' => isset($data['extra_or_discount']) ? $data['extra_or_discount'] : 0,
            'sender_city_id' => isset($data['sender_city_id']) ? $data['sender_city_id'] : 0,
            'receiver_city_id' => isset($data['receiver_city_id']) ? $data['receiver_city_id'] : 0,
            'sender_zone_id' => isset($data['sender_zone_id']) ? $data['sender_zone_id'] : 0,
            'receiver_zone_id' => isset($data['receiver_zone_id']) ? $data['receiver_zone_id'] : 0,
            'from_bus_station_id' => isset($data['from_bus_station_id']) ? $data['from_bus_station_id'] : 0,
            'to_bus_station_id' => isset($data['to_bus_station_id']) ? $data['to_bus_station_id'] : 0,
            'start_date' => isset($data['start_date']) ? $data['start_date'] : null,
            'end_date' => isset($data['end_date']) ? $data['end_date'] : null,
            'note' => isset($data['note']) ? $note : null,
            'platform' => isset($data['platform']) ? $data['platform'] : 'All',
            'created_by' => auth()->user()->id
        ]);
    }

    /**
     * @param MerchantDiscount  $merchantDiscount
     * @param array $data
     *
     * @return mixed
     */
    public function update(MerchantDiscount $merchantDiscount, array $data) : MerchantDiscount
    {
        if (isset($data['note'])) {
            $note = getConvertedString($data['note']);
        }

        $merchantDiscount->amount = isset($data['amount']) ? $data['amount'] : $merchantDiscount->amount;
        $merchantDiscount->merchant_id = isset($data['merchant_id']) ? $data['merchant_id'] : $merchantDiscount->merchant_id;
        $merchantDiscount->discount_type_id = isset($data['discount_type_id']) ? $data['discount_type_id'] : $merchantDiscount->discount_type_id;
        $merchantDiscount->normal_or_dropoff = isset($data['normal_or_dropoff']) ? $data['normal_or_dropoff'] : $merchantDiscount->normal_or_dropoff;
        $merchantDiscount->extra_or_discount = isset($data['extra_or_discount']) ? $data['extra_or_discount'] : $merchantDiscount->extra_or_discount;
        $merchantDiscount->sender_city_id = isset($data['sender_city_id']) ? $data['sender_city_id'] : $merchantDiscount->sender_city_id;
        $merchantDiscount->receiver_city_id = isset($data['receiver_city_id']) ? $data['receiver_city_id'] : $merchantDiscount->receiver_city_id;
        $merchantDiscount->sender_zone_id = isset($data['sender_zone_id']) ? $data['sender_zone_id'] : $merchantDiscount->sender_zone_id;
        $merchantDiscount->receiver_zone_id = isset($data['receiver_zone_id']) ? $data['receiver_zone_id'] : $merchantDiscount->receiver_zone_id;
        $merchantDiscount->from_bus_station_id = isset($data['from_bus_station_id']) ? $data['from_bus_station_id'] : $merchantDiscount->from_bus_station_id;
        $merchantDiscount->to_bus_station_id = isset($data['to_bus_station_id']) ? $data['to_bus_station_id'] : $merchantDiscount->to_bus_station_id;
        $merchantDiscount->start_date = isset($data['start_date']) ? $data['start_date'] : $merchantDiscount->start_date;
        $merchantDiscount->end_date = isset($data['end_date']) ? $data['end_date'] : $merchantDiscount->end_date;
        $merchantDiscount->note = isset($data['note']) ? $note : $merchantDiscount->note;
        $merchantDiscount->platform = isset($data['platform']) ? $data['platform'] : $merchantDiscount->platform;

        if ($merchantDiscount->isDirty()) {
            $merchantDiscount->updated_by = auth()->user()->id;
            $merchantDiscount->save();
        }

        return $merchantDiscount->refresh();
    }


    /**
     * @param MerchantDiscount $merchantDiscount
     */
    public function destroy(MerchantDiscount $merchantDiscount)
    {
        $deleted = $this->deleteById($merchantDiscount->id);

        if ($deleted) {
            $merchantDiscount->deleted_by = auth()->user()->id;
            $merchantDiscount->save();
        }
    }
}
