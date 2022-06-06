<?php

use App\Models\Coupon;
use Illuminate\Database\Seeder;
use App\Traits\TruncateTableSeeder;

class CouponsTableSeeder extends Seeder
{
    use TruncateTableSeeder;
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->truncate('coupons');

        // DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        // Coupon::truncate();
        // DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        factory(Coupon::class, 20)->create();
    }
}
