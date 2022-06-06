<?php

use App\Models\Route;
use App\Models\DoorToDoor;
use Illuminate\Database\Seeder;
use App\Models\GlobalScale;

class DoorToDoorsTableSeeder extends Seeder
{
    protected $dtds = [
        ['route_id' => 1, 'global_scale_id' => 1, 'base_rate' => 1200, 'agent_base_rate' => 600, 'salt' => 1000, 'agent_salt' => 300],
        ['route_id' => 1, 'global_scale_id' => 2, 'base_rate' => 1300, 'agent_base_rate' => 700, 'salt' => 800, 'agent_salt' => 400],
        ['route_id' => 1, 'global_scale_id' => 3, 'base_rate' => 1400, 'agent_base_rate' => 800, 'salt' => 600, 'agent_salt' => 500],
        ['route_id' => 1, 'global_scale_id' => 4, 'base_rate' => 1500, 'agent_base_rate' => 900, 'salt' => 450, 'agent_salt' => 600],
        ['route_id' => 1, 'global_scale_id' => 5, 'base_rate' => 1600, 'agent_base_rate' => 1000, 'salt' => 400, 'agent_salt' => 700],
        ['route_id' => 2, 'global_scale_id' => 1, 'base_rate' => 1200, 'agent_base_rate' => 600, 'salt' => 1000, 'agent_salt' => 800],
        ['route_id' => 2, 'global_scale_id' => 2, 'base_rate' => 1300, 'agent_base_rate' => 700, 'salt' => 800, 'agent_salt' => 900],
        ['route_id' => 2, 'global_scale_id' => 3, 'base_rate' => 1400, 'agent_base_rate' => 800, 'salt' => 600, 'agent_salt' => 1000],
        ['route_id' => 2, 'global_scale_id' => 4, 'base_rate' => 1500, 'agent_base_rate' => 900, 'salt' => 450, 'agent_salt' => 1100],
        ['route_id' => 2, 'global_scale_id' => 5, 'base_rate' => 1600, 'agent_base_rate' => 1000, 'salt' => 400, 'agent_salt' => 1200],
        ['route_id' => 3, 'global_scale_id' => 1, 'base_rate' => 1200, 'agent_base_rate' => 600, 'salt' => 1000, 'agent_salt' => 1300],
        ['route_id' => 3, 'global_scale_id' => 2, 'base_rate' => 1300, 'agent_base_rate' => 700, 'salt' => 800, 'agent_salt' => 1400],
        ['route_id' => 3, 'global_scale_id' => 3, 'base_rate' => 1400, 'agent_base_rate' => 800, 'salt' => 600, 'agent_salt' => 1500],
        ['route_id' => 3, 'global_scale_id' => 4, 'base_rate' => 1500, 'agent_base_rate' => 900, 'salt' => 450, 'agent_salt' => 1600],
        ['route_id' => 3, 'global_scale_id' => 5, 'base_rate' => 1600, 'agent_base_rate' => 1000, 'salt' => 400, 'agent_salt' => 1700],
        ['route_id' => 4, 'global_scale_id' => 1, 'base_rate' => 1200, 'agent_base_rate' => 600, 'salt' => 1000, 'agent_salt' => 1800],
        ['route_id' => 4, 'global_scale_id' => 2, 'base_rate' => 1300, 'agent_base_rate' => 700, 'salt' => 800, 'agent_salt' => 1900],
        ['route_id' => 4, 'global_scale_id' => 3, 'base_rate' => 1400, 'agent_base_rate' => 800, 'salt' => 600, 'agent_salt' => 2000],
        ['route_id' => 4, 'global_scale_id' => 4, 'base_rate' => 1500, 'agent_base_rate' => 900, 'salt' => 450, 'agent_salt' => 2100],
        ['route_id' => 4, 'global_scale_id' => 5, 'base_rate' => 1600, 'agent_base_rate' => 1000, 'salt' => 400, 'agent_salt' => 2200],
        ['route_id' => 5, 'global_scale_id' => 1, 'base_rate' => 1200, 'agent_base_rate' => 600, 'salt' => 1000, 'agent_salt' => 2300],
        ['route_id' => 5, 'global_scale_id' => 2, 'base_rate' => 1300, 'agent_base_rate' => 700, 'salt' => 800, 'agent_salt' => 2400],
        ['route_id' => 5, 'global_scale_id' => 3, 'base_rate' => 1400, 'agent_base_rate' => 800, 'salt' => 600, 'agent_salt' => 2500],
        ['route_id' => 5, 'global_scale_id' => 4, 'base_rate' => 1500, 'agent_base_rate' => 900, 'salt' => 450, 'agent_salt' => 2600],
        ['route_id' => 5, 'global_scale_id' => 5, 'base_rate' => 1600, 'agent_base_rate' => 1000, 'salt' => 400, 'agent_salt' => 2700],
        ['route_id' => 6, 'global_scale_id' => 1, 'base_rate' => 1200, 'agent_base_rate' => 600, 'salt' => 1000, 'agent_salt' => 2800],
        ['route_id' => 6, 'global_scale_id' => 2, 'base_rate' => 1300, 'agent_base_rate' => 700, 'salt' => 800, 'agent_salt' => 2900],
        ['route_id' => 6, 'global_scale_id' => 3, 'base_rate' => 1400, 'agent_base_rate' => 800, 'salt' => 600, 'agent_salt' => 3000],
        ['route_id' => 6, 'global_scale_id' => 4, 'base_rate' => 1500, 'agent_base_rate' => 900, 'salt' => 450, 'agent_salt' => 3100],
        ['route_id' => 6, 'global_scale_id' => 5, 'base_rate' => 1600, 'agent_base_rate' => 1000, 'salt' => 400, 'agent_salt' => 3200],
        ['route_id' => 7, 'global_scale_id' => 1, 'base_rate' => 1200, 'agent_base_rate' => 600, 'salt' => 1000, 'agent_salt' => 3300],
        ['route_id' => 7, 'global_scale_id' => 2, 'base_rate' => 1300, 'agent_base_rate' => 700, 'salt' => 800, 'agent_salt' => 3400],
        ['route_id' => 7, 'global_scale_id' => 3, 'base_rate' => 1400, 'agent_base_rate' => 800, 'salt' => 600, 'agent_salt' => 3500],
        ['route_id' => 7, 'global_scale_id' => 4, 'base_rate' => 1500, 'agent_base_rate' => 900, 'salt' => 450, 'agent_salt' => 3600],
        ['route_id' => 7, 'global_scale_id' => 5, 'base_rate' => 1600, 'agent_base_rate' => 1000, 'salt' => 400, 'agent_salt' => 3700],
        ['route_id' => 8, 'global_scale_id' => 1, 'base_rate' => 1200, 'agent_base_rate' => 600, 'salt' => 1000, 'agent_salt' => 3800],
        ['route_id' => 8, 'global_scale_id' => 2, 'base_rate' => 1300, 'agent_base_rate' => 700, 'salt' => 800, 'agent_salt' => 3900],
        ['route_id' => 8, 'global_scale_id' => 3, 'base_rate' => 1400, 'agent_base_rate' => 800, 'salt' => 600, 'agent_salt' => 4000],
        ['route_id' => 8, 'global_scale_id' => 4, 'base_rate' => 1500, 'agent_base_rate' => 900, 'salt' => 450, 'agent_salt' => 4100],
        ['route_id' => 8, 'global_scale_id' => 5, 'base_rate' => 1600, 'agent_base_rate' => 1000, 'salt' => 400, 'agent_salt' => 4200],
    ];
    /**
     * Run the database seeds.
     *
     * @return void
     */

    public function run()
    {
        // DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        // DoorToDoor::truncate();
        // DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        Schema::disableForeignKeyConstraints();
        foreach ($this->dtds as $dtd) {
            factory(DoorToDoor::class)->create([
                'route_id' => $dtd['route_id'],
                'global_scale_id' => $dtd['global_scale_id'],
                'base_rate' => $dtd['base_rate'],
                'agent_base_rate' => $dtd['agent_base_rate'],
                'salt' => $dtd['salt'],
                'agent_salt' => $dtd['agent_salt']
            ]);
        }
        Schema::enableForeignKeyConstraints();

        // $routes = Route::all();
        // $global_scales = GlobalScale::all();

        // foreach ($routes as $route) {
        //     foreach ($global_scales as $global_scale) {
        //         factory(DoorToDoor::class)->create([
        //             'route_id' => $route->id,
        //             'global_scale_id' => $global_scale->id
        //     ]);
        //     }
        // }

        // //dd($this->cities);
    }
}
