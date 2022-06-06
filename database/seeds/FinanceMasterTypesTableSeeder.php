<?php

use Illuminate\Database\Seeder;
use App\Models\FinanceMasterType;

class FinanceMasterTypesTableSeeder extends Seeder
{
    protected $data = [
        'General Ledger' => 'General Ledger',
        'Cash & Bank' => 'Cash & Bank',
        'Tax' => 'Tax'
        
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
            FinanceMasterType::create([
                'name'    => $name,
                'description' => $desc
            ]);
        }
        Schema::enableForeignKeyConstraints();
    }
}
