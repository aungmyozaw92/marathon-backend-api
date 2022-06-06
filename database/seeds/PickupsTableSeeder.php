<?php

use App\Models\Pickup;
use Illuminate\Database\Seeder;

class PickupsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        // Pickup::truncate();
        // DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        Schema::disableForeignKeyConstraints();
        factory(Pickup::class, 60)->create();
        Schema::enableForeignKeyConstraints();
    }
}
