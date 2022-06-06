<?php

use App\Models\BusStation;
use Illuminate\Database\Seeder;
use App\Traits\TruncateTableSeeder;

class BusStationsTableSeeder extends Seeder
{
    use TruncateTableSeeder;

    protected $bus_stations = [
        ['name' => "Aung Mingalar", 'number_of_gates' => 60, 'city_id' => 1, 'zone_id' => 3,'delivery_rate' => 1000],
        ['name' => "Dagon Aya", 'number_of_gates' => 30, 'city_id' => 1, 'zone_id' => 4,'delivery_rate' => 1500],
        ['name' => "ရွှေမန်းသူ", 'number_of_gates' => 1, 'city_id' => 1, 'zone_id' => 5,'delivery_rate' => 2000],
        ['name' => "Aung San Kwin", 'number_of_gates' => 10, 'city_id' => 1, 'zone_id' => 6,'delivery_rate' => 2500],
        ['name' => "Elite Main Station", 'number_of_gates' => 1, 'city_id' => 1, 'zone_id' => 19,'delivery_rate' => 2000],
        ['name' => "ကျွဲဆယ်ကန်", 'number_of_gates' => 69, 'city_id' => 29, 'zone_id' => 51,'delivery_rate' => 1500],
        ['name' => "သီရိမန္တလာ", 'number_of_gates' => 12, 'city_id' => 29, 'zone_id' => 53,'delivery_rate' => 1500],
        ['name' => "ပြည်ကြီးမြတ်ရှင်", 'number_of_gates' => 40, 'city_id' => 29, 'zone_id' => 56,'delivery_rate' => 1500],
        ['name' => "Kyaukse Bus station", 'number_of_gates' => 30, 'city_id' => 31, 'zone_id' => 60,'delivery_rate' => 1000],
        ['name' => "Naypyitaw Bus station", 'number_of_gates' => 20, 'city_id' => 41, 'zone_id' => 61,'delivery_rate' => 1000],
        ['name' => "Thanlyin Bus station", 'number_of_gates' => 10, 'city_id' => 2, 'zone_id' => 62,'delivery_rate' => 1000],
        ['name' => "Monywa Bus station ", 'number_of_gates' => 12, 'city_id' => 23, 'zone_id' => 63,'delivery_rate' => 1000],
        ['name' => "Taungoo Bus station", 'number_of_gates' => 15, 'city_id' => 48, 'zone_id' => 64,'delivery_rate' => 1000],
        ['name' => "Pyay Bus station", 'number_of_gates' => 26, 'city_id' => 9, 'zone_id' => 65,'delivery_rate' => 1000],
        ['name' => "Pyin Oo Lwin Bus station", 'number_of_gates' => 29, 'city_id' => 71, 'zone_id' => 66,'delivery_rate' => 1000],
    ];

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        // BusStation::truncate();
        // DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        $this->truncate('bus_stations');
        foreach ($this->bus_stations as $station) {
            factory(BusStation::class)->create([
                'name' => $station["name"],
                'number_of_gates' => $station["number_of_gates"],
                'city_id' => $station["city_id"],
                'zone_id' => $station["zone_id"],
                'delivery_rate' => $station["delivery_rate"]
            ]);
        }
    }
}
