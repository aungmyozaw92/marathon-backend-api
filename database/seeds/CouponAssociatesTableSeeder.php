<?php

use App\Models\CouponAssociate;
use Illuminate\Database\Seeder;

class CouponAssociatesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        // CouponAssociate::truncate();
        // DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        Schema::disableForeignKeyConstraints();
        factory(CouponAssociate::class, 60)->create();
        Schema::enableForeignKeyConstraints();
    }
}
