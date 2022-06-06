<?php

use Illuminate\Database\Seeder;
use App\Models\FinancePaymentOption;

class FinancePaymentOptionSeeder extends Seeder
{
    protected $data = [
        'Cash',
        'Cheque',
        'Credit Card',
        'Account/Bank Transfer',
        'Others',
    ];
    
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Schema::disableForeignKeyConstraints();
        foreach ($this->data as $label) {
            FinancePaymentOption::create([
                'label'    => $label
            ]);
        }
        Schema::enableForeignKeyConstraints();
    }
}
