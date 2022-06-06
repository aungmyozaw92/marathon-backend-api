<?php

use App\Models\FlaggedCustomer;
use Illuminate\Database\Seeder;
use App\Traits\TruncateTableSeeder;

class FlaggedCustomersTableSeeder extends Seeder
{
    use TruncateTableSeeder;
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->truncate('flagged_customers');

        // DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        // FlaggedCustomer::truncate();
        // DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        
        Schema::disableForeignKeyConstraints();
        factory(FlaggedCustomer::class, 60)->create();
        Schema::enableForeignKeyConstraints();
    }
}
