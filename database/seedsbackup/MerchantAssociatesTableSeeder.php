<?php

use App\Models\Merchant;
use Illuminate\Database\Seeder;
use App\Models\MerchantAssociate;
use App\Traits\TruncateTableSeeder;

class MerchantAssociatesTableSeeder extends Seeder
{
    use TruncateTableSeeder;
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
        $this->truncate('merchant_associates');

        $merchats = Merchant::all();

        foreach ($merchats as $merchat) {
            factory(MerchantAssociate::class, 2)->create([
                'merchant_id' => $merchat->id
            ]);
        }
    }
}
