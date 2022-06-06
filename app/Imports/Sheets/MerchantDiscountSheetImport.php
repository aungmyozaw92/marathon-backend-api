<?php
namespace App\Imports\Sheets;

use App\Models\MerchantDiscount;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class MerchantDiscountSheetImport implements ToModel,WithHeadingRow
{
    public function model(array $row)
    {
      if (request()->get('MerchantDiscount') == 'MerchantDiscount') {
          MerchantDiscount::create([
              'amount' => isset($row['amount'])? $row['amount'] : 0,
              'merchant_id' => isset($row['merchant_id'])? $row['merchant_id'] : 0,
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
              'platform' => isset($row['platform'])? $row['platform'] : 'Mobile',
            ]);
      }
    }
}
						
