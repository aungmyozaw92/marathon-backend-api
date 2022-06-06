<?php

use Illuminate\Database\Seeder;
use App\Models\FinanceAccountType;

class FinanceAccountTypesTableSeeder extends Seeder
{
    protected $data = [
        'Balance Sheet' => 'Balance Sheet',
        'Profit & Loss' => 'Profit & Loss',
        
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
            FinanceAccountType::create([
                'name'    => $name,
                'description' => $desc
            ]);
        }
        Schema::enableForeignKeyConstraints();
    }
}
