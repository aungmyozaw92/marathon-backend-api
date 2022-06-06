<?php

use App\Models\City;
use App\Models\Meta;
use App\Models\Route;
use Illuminate\Database\Seeder;

class RoutesTableSeeder extends Seeder
{    
    /**
     * Run the database seeds.
     *
     * @return void
     */

    public function run()
    {
        // DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        // Route::truncate();
        // DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        Schema::disableForeignKeyConstraints();
        $cities = City::all();
        $branch_city_id = Meta::where('key', 'branch')->first()->value;
       // dd(City::find($branch_city_id)->name);
        $origin_name = City::find($branch_city_id)->name;
        foreach ($cities as $city) {
            $route_name = $origin_name . '=>' . City::find($city["id"])->name;
            factory(Route::class)->create([
                'origin_id' => $branch_city_id,
                'destination_id' => $city["id"],
                'travel_day' => 1,
                'route_name' => $route_name,
            ]);
        }
        Schema::enableForeignKeyConstraints();




        // //dd($this->cities);
        // foreach ($this->cities as $city) {
        //     factory(Route::class)->create([
        //         //'route_rate' => $city[2],
        //         'travel_day' => 1,
        //         'origin_id' => $city[0],
        //         'destination_id' => $city[1]
        //     ]);
        // }
    }
}
