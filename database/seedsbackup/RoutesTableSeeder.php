<?php

use App\Models\City;
use App\Models\Route;
use Illuminate\Database\Seeder;
use App\Traits\TruncateTableSeeder;

class RoutesTableSeeder extends Seeder
{
    use TruncateTableSeeder;

    protected $routes = [
        [ 'origin_id'=>1, 'destination_id'=>1, 'travel_day'=>1],
        [ 'origin_id'=>1, 'destination_id'=>29, 'travel_day'=>1],
        [ 'origin_id'=>1, 'destination_id'=>41, 'travel_day'=>1],
        [ 'origin_id'=>1, 'destination_id'=>2, 'travel_day'=>1],
        [ 'origin_id'=>1, 'destination_id'=>23, 'travel_day'=>1],
        [ 'origin_id'=>1, 'destination_id'=>48, 'travel_day'=>1],
        [ 'origin_id'=>1, 'destination_id'=>9, 'travel_day'=>1],
        [ 'origin_id'=>1, 'destination_id'=>71, 'travel_day'=>1],
    ];
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
        $this->truncate('routes');

        foreach ($this->routes as $route) {
            $route_name = City::find($route["origin_id"])->name . '=>' . City::find($route["destination_id"])->name;
            factory(Route::class)->create([
                'origin_id' => $route["origin_id"],
                'destination_id' => $route["destination_id"],
                'travel_day' => $route["travel_day"],
                'route_name' => $route_name,
            ]);
        }

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
