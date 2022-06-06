<?php

use Illuminate\Database\Seeder;
use App\Models\ContactAssociate;
use App\Models\MerchantAssociate;
use App\Traits\TruncateTableSeeder;

class ContactAssociatesTableSeeder extends Seeder
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
        // ContactAssociate::truncate();
        // DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        $this->truncate('contact_associates');

        $merchantAssociates = MerchantAssociate::all();

        foreach ($merchantAssociates as $merchantAssociate) {
            factory(ContactAssociate::class, 6)->create([
                'merchant_id' => $merchantAssociate->merchant->id,
                'merchant_associate_id' => $merchantAssociate->id,
            ]);
        }
    }
}
