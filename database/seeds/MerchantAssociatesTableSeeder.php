<?php

use App\Models\Merchant;
use Illuminate\Database\Seeder;
use App\Models\MerchantAssociate;

class MerchantAssociatesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        // MerchantAssociate::truncate();
        // DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        Schema::disableForeignKeyConstraints();
        $merchats = Merchant::all();

        foreach ($merchats as $merchat) {
            factory(MerchantAssociate::class, 2)->create([
                'merchant_id' => $merchat->id
            ]);
        }
        Schema::enableForeignKeyConstraints();
    }
}
