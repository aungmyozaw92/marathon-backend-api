<?php

use App\Models\Gate;
use App\Models\Route;
use App\Models\BusDropOff;
use App\Models\GlobalScale;
use Illuminate\Database\Seeder;
use App\Traits\TruncateTableSeeder;

class BusDropOffsTableSeeder extends Seeder
{
    use TruncateTableSeeder;
    /**
     * Run the database seeds.
     *
     * @return void
     */
    
    public function run()
    {
        // DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        // BusDropOff::truncate();
        // DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        $this->truncate('bus_drop_offs');

        $gates = Gate::all();
        $routes = Route::all();
        $global_scales = GlobalScale::all();
        $i = 1;
        foreach ($gates as $gate) {
            foreach ($global_scales as $global_scale) {
                foreach ($routes as $route) {
                    factory(BusDropOff::class)->create([
                    'gate_id' => $gate->id,
                    'global_scale_id' => $global_scale->id,
                    'route_id' => $route->id,
            ]);
                }
            }
            $i += 1;
        }
    }
}
