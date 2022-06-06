<?php

use App\Models\MerchantRateCard;
use Illuminate\Database\Seeder;

class MerchantRateCardSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Schema::disableForeignKeyConstraints();
        MerchantRateCard::create([
            'amount' => 1200,
            'merchant_id' => 724,
            'merchant_associate_id' => 709,
            'discount_type_id' => 1,
            'normal_or_dropoff' => 0,
            'extra_or_discount' => 0,
            'sender_city_id' => 0,
            'receiver_city_id' => 0,
            'sender_zone_id' => 0,
            'receiver_zone_id' => 0,
            'from_bus_station_id' => 0,
            'to_bus_station_id' => 0,
            'start_date' => null,
            'end_date' => null,
            'from_weight' => null,
            'to_weight' => null,
            'note' => null,
            'platform' => 'All',
            'created_by' => 1
            
        ]);

       // factory(Staff::class, 1)->create();
        Schema::enableForeignKeyConstraints();
    }
}
