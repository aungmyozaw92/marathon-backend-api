<?php

use Illuminate\Database\Seeder;
use App\Models\ContactAssociate;
use App\Models\MerchantAssociate;

class ContactAssociatesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        // ContactAssociate::truncate();
        // DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        Schema::disableForeignKeyConstraints();
        $merchantAssociates = MerchantAssociate::all();

        foreach ($merchantAssociates as $merchantAssociate) {
            factory(ContactAssociate::class, 6)->create([
                'merchant_id' => $merchantAssociate->merchant->id,
                'merchant_associate_id' => $merchantAssociate->id,
            ]);
        }
        Schema::enableForeignKeyConstraints();
    }
}
