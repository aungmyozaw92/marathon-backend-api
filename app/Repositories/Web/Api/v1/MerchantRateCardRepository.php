<?php

namespace App\Repositories\Web\Api\v1;

use App\Models\MerchantRateCard;
use App\Repositories\BaseRepository;

class MerchantRateCardRepository extends BaseRepository
{
    /**
     * @return string
     */
    public function model()
    {
        return MerchantRateCard::class;
    }

    /**
     * @param array $data
     *
     * @return MerchantRateCard
     */
    public function create(array $data) : MerchantRateCard
    {
        $merchant_rate_card = MerchantRateCard::create([
              'amount' => isset($data['amount'])? $data['amount'] : 0,
              'merchant_id' => isset($data['merchant_id'])? $data['merchant_id'] : 0,
              'merchant_associate_id' => isset($data['merchant_associate_id'])? $data['merchant_associate_id'] : 0,
              'discount_type_id' => isset($data['discount_type_id'])? $data['discount_type_id'] : 0,
              'normal_or_dropoff' => isset($data['normal_or_dropoff'])? $data['normal_or_dropoff'] : 0,
              'extra_or_discount' => isset($data['extra_or_discount'])? $data['extra_or_discount'] : 0,
              'sender_city_id' => isset($data['sender_city_id'])? $data['sender_city_id'] : 0,
              'receiver_city_id' => isset($data['receiver_city_id'])? $data['receiver_city_id'] : 0,
              'sender_zone_id' => isset($data['sender_zone_id'])? $data['sender_zone_id'] : 0,
              'receiver_zone_id' => isset($data['receiver_zone_id'])? $data['receiver_zone_id'] : 0,
              'from_bus_station_id' => isset($data['from_bus_station_id'])? $data['from_bus_station_id'] : 0,
              'to_bus_station_id' => isset($data['to_bus_station_id'])? $data['to_bus_station_id'] : 0,
              'start_date' => isset($data['start_date'])? $data['start_date'] : null,
              'end_date' => isset($data['end_date'])? $data['end_date'] : null,
              'note' => isset($data['note'])? $data['note'] : null,
              'platform' => isset($data['platform'])? $data['platform'] : 'All',
              'from_weight' => isset($data['from_weight'])? $data['from_weight'] : 0.1,
              'to_weight' => isset($data['to_weight'])? $data['to_weight'] : 2,
              'incremental_weight' => isset($data['incremental_weight'])? $data['incremental_weight'] : 500,
            ]);

        return $merchant_rate_card;
    }

    /**
     * @param MerchantRateCard  $merchant_rate_card
     * @param array $data
     *
     * @return mixed
     */
    public function update(MerchantRateCard $merchant_rate_card, array $data) : MerchantRateCard
    {
        $merchant_rate_card->amount = isset($data['amount']) ? $data['amount'] : $merchant_rate_card->amount ;
        $merchant_rate_card->merchant_id = isset($data['merchant_id']) ? $data['merchant_id'] : 0 ;
        $merchant_rate_card->merchant_associate_id = isset($data['merchant_associate_id']) ? $data['merchant_associate_id'] : 0;
        $merchant_rate_card->discount_type_id = isset($data['discount_type_id']) ? $data['discount_type_id'] : $merchant_rate_card->discount_type_id ;
        $merchant_rate_card->normal_or_dropoff = isset($data['normal_or_dropoff']) ? $data['normal_or_dropoff'] : 0;
        $merchant_rate_card->extra_or_discount = isset($data['extra_or_discount']) ? $data['extra_or_discount'] : 0 ;
        $merchant_rate_card->sender_city_id = isset($data['sender_city_id']) ? $data['sender_city_id'] : 0 ;
        $merchant_rate_card->receiver_city_id = isset($data['receiver_city_id']) ? $data['receiver_city_id'] : 0 ;
        $merchant_rate_card->sender_zone_id = isset($data['sender_zone_id']) ? $data['sender_zone_id'] :0;
        $merchant_rate_card->receiver_zone_id = isset($data['receiver_zone_id']) ? $data['receiver_zone_id'] :0;
        $merchant_rate_card->from_bus_station_id = isset($data['from_bus_station_id']) ? $data['from_bus_station_id'] :0;
        $merchant_rate_card->to_bus_station_id = isset($data['to_bus_station_id']) ? $data['to_bus_station_id'] :0;
        $merchant_rate_card->start_date = isset($data['start_date']) ? $data['start_date'] :null;
        $merchant_rate_card->end_date = isset($data['end_date']) ? $data['end_date'] :null;
        $merchant_rate_card->note = isset($data['note']) ? $data['note'] : null;
        $merchant_rate_card->platform = isset($data['platform']) ? $data['platform'] :0;
        $merchant_rate_card->from_weight = isset($data['from_weight']) ? $data['from_weight'] : $merchant_rate_card->from_weight ;
        $merchant_rate_card->to_weight = isset($data['to_weight']) ? $data['to_weight'] : $merchant_rate_card->to_weight ;
        $merchant_rate_card->incremental_weight = isset($data['incremental_weight']) ? $data['incremental_weight'] : $merchant_rate_card->incremental_weight ;
        

        if ($merchant_rate_card->isDirty()) {
            $merchant_rate_card->updated_by = auth()->user()->id;
            $merchant_rate_card->save();
        }
        return $merchant_rate_card->refresh();
    }

    /**
     * @param MerchantRateCard $merchant_rate_card
     */
    public function destroy(MerchantRateCard $merchant_rate_card)
    {
        $deleted = $this->deleteById($merchant_rate_card->id);

        if ($deleted) {
            $merchant_rate_card->deleted_by = auth()->user()->id;
            $merchant_rate_card->save();
        }
    }
}
