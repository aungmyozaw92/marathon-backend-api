<?php

namespace App\Imports;

use App\Models\MerchantRateCard;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;


class RateCardImport implements ToModel, WithHeadingRow
{
    /**
     * @param array $row
     *
     * @return MerchantRateCard|null
     */
    public function model(array $row)
    {
        // dd($row);
        MerchantRateCard::create([
              'amount' => isset($row['amount'])? $row['amount'] : 0,
              'merchant_id' => isset($row['merchant_id'])? $row['merchant_id'] : 0,
              'merchant_associate_id' => isset($row['merchant_associate_id'])? $row['merchant_associate_id'] : 0,
              'discount_type_id' => isset($row['discount_type_id'])? $row['discount_type_id'] : 0,
              'normal_or_dropoff' => isset($row['normal_or_dropoff'])? $row['normal_or_dropoff'] : 0,
              'extra_or_discount' => isset($row['extra_or_discount'])? $row['extra_or_discount'] : 0,
              'sender_city_id' => isset($row['sender_city_id'])? $row['sender_city_id'] : 0,
              'receiver_city_id' => isset($row['receiver_city_id'])? $row['receiver_city_id'] : 0,
              'sender_zone_id' => isset($row['sender_zone_id'])? $row['sender_zone_id'] : 0,
              'receiver_zone_id' => isset($row['receiver_zone_id'])? $row['receiver_zone_id'] : 0,
              'from_bus_station_id' => isset($row['from_bus_station_id'])? $row['from_bus_station_id'] : 0,
              'to_bus_station_id' => isset($row['to_bus_station_id'])? $row['to_bus_station_id'] : 0,
              'start_date' => isset($row['start_date'])? $row['start_date'] : null,
              'end_date' => isset($row['end_date'])? $row['end_date'] : null,
              'note' => isset($row['note'])? $row['note'] : null,
              'platform' => isset($row['platform'])? $row['platform'] : 'All',
              'from_weight' => isset($row['from_weight'])? $row['from_weight'] : 0.1,
              'to_weight' => isset($row['to_weight'])? $row['to_weight'] : 2,
              'incremental_weight' => isset($row['incremental_weight'])? $row['incremental_weight'] : 500,
        ]);
    }
}