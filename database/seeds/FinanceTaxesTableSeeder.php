<?php

use App\Models\FinanceTax;
use Illuminate\Database\Seeder;

class FinanceTaxesTableSeeder extends Seeder
{
    protected $data = [
        'commercial tax' => '5',
        'income tax' => '10',
        'company tax' => '15'
       
    ];
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Schema::disableForeignKeyConstraints();
        foreach ($this->data as $name => $amount) {
            FinanceTax::create([
                'name'    => $name,
                'amount' => $amount,
                'description' => $name
            ]);
        }
        Schema::enableForeignKeyConstraints();
    }
}
