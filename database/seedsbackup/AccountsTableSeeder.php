<?php

use App\Models\Account;
use Illuminate\Database\Seeder;
use App\Traits\TruncateTableSeeder;

class AccountsTableSeeder extends Seeder
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
        // Account::truncate();
        // DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        $this->truncate('accounts');

        factory(Account::class)->create([
            'accountable_type'   => 'Zone',
            'accountable_id'     => 1,
        ]);

        factory(Account::class, 60)->create();
    }
}
