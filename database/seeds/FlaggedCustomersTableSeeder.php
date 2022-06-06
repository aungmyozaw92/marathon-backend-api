<?php

use App\Models\FlaggedCustomer;
use Illuminate\Database\Seeder;

class FlaggedCustomersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        // FlaggedCustomer::truncate();
        // DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        Schema::disableForeignKeyConstraints();
        factory(FlaggedCustomer::class, 60)->create();
        Schema::enableForeignKeyConstraints();
    }
}
