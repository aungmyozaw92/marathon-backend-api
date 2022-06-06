<?php

use App\Models\FinanceGroup;
use Illuminate\Database\Seeder;

class FinanceGroupsTableSeeder extends Seeder
{
    protected $data = [
        'Non Current Assets' => 'Non Current Assets',
        'Current Assets' => 'Current Assets',
        'Other Current Assets' => 'Other Current Assets',
        'Non Current Liabilities' => 'Non Current Liabilities',
        'Current Liabilities' => 'Current Liabilities',
        'Other Current Liabilities' => 'Other Current Liabilities',
        'Equity and Capital Employed' => 'Equity and Capital Employed',
        'Income/Revenue' => 'Income/Revenue',
        'Cost of Service' => 'Cost of Service',
        'Overhead' => 'Overhead',
        'Others Income' => 'Others Income',
        'Others' => 'Others',
    ];

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Schema::disableForeignKeyConstraints();
        foreach ($this->data as $name => $desc) {
            FinanceGroup::create([
                'name'    => $name,
                'description' => $desc
            ]);
        }
        Schema::enableForeignKeyConstraints();
    }
}
