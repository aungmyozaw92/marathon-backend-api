<?php

use App\Models\Customer;
use Illuminate\Database\Seeder;

class CustomersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        // Customer::truncate();
        // DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        Schema::disableForeignKeyConstraints();
        factory(Customer::class, 20)->create();
        Schema::enableForeignKeyConstraints();
    }
}
