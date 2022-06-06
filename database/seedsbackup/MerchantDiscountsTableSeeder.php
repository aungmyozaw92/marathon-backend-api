<?php

use Illuminate\Database\Seeder;
use App\Models\MerchantDiscount;
use App\Traits\TruncateTableSeeder;

class MerchantDiscountsTableSeeder extends Seeder
{
    use TruncateTableSeeder;
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $this->truncate('merchant_discounts');

        // DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        // MerchantDiscount::truncate();
        // DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        factory(MerchantDiscount::class, 20)->create();
    }
}
