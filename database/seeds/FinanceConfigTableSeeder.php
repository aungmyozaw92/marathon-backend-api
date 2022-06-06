<?php

use App\Models\Branch;
use App\Models\FinanceConfig;
use Illuminate\Database\Seeder;

class FinanceConfigTableSeeder extends Seeder
{

    protected $data = [
        ['screen' => 'Expense', 'finance_account_id' =>1184],
        ['screen' => 'Advance', 'finance_account_id' =>1184],
        ['screen' => 'Asset', 'finance_account_id' =>1184],
    ];
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Schema::disableForeignKeyConstraints();
        foreach (Branch::all() as $branch) {
            foreach ($this->data as $config) {
                FinanceConfig::create([
                    'screen'    => $config['screen'],
                    'finance_account_id'    => $config['finance_account_id'],
                    'branch_id' => $branch->id
                ]);
            }
        }
        Schema::enableForeignKeyConstraints();
    }
}
