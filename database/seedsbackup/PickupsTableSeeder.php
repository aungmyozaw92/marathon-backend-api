<?php

use App\Models\Pickup;
use Illuminate\Database\Seeder;
use App\Traits\TruncateTableSeeder;

class PickupsTableSeeder extends Seeder
{
    use TruncateTableSeeder;
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->truncate('pickups');

        // DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        // Pickup::truncate();
        // DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        
        factory(Pickup::class, 60)->create();
    }
}
