<?php

use App\Models\FinanceNature;
use Illuminate\Database\Seeder;

class FinanceNaturesTableSeeder extends Seeder
{
    protected $data = [
        'Assets' => 'Assets',
        'Liabilities' => 'Liabilities',
        'Income' => 'Income',
        'Expenses' => 'Expenses',
        
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
            FinanceNature::create([
                'name'    => $name,
                'description' => $desc
            ]);
        }
        Schema::enableForeignKeyConstraints();
    }
}
