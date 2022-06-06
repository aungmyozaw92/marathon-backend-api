<?php

use App\Models\CouponAssociate;
use Illuminate\Database\Seeder;
use App\Traits\TruncateTableSeeder;

class CouponAssociatesTableSeeder extends Seeder
{
    use TruncateTableSeeder;
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->truncate('coupon_associates');

        // DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        // CouponAssociate::truncate();
        // DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        factory(CouponAssociate::class, 60)->create();
    }
}
