<?php

use Illuminate\Database\Seeder;
use App\Models\MerchantDiscount;

class MerchantDiscountsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        // DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        // MerchantDiscount::truncate();
        // DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        Schema::disableForeignKeyConstraints();
        factory(MerchantDiscount::class, 20)->create();
        Schema::enableForeignKeyConstraints();
    }
}
