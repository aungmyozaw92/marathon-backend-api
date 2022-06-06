<?php

use App\Models\Account;
use Illuminate\Database\Seeder;

class AccountsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        // Account::truncate();
        // DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        Schema::disableForeignKeyConstraints();

        factory(Account::class)->create([
            'account_no' => 'A000001',
            'accountable_type' => 'HQ',
            'accountable_id' => 1
        ]);
       
        Schema::enableForeignKeyConstraints();
    }
}
