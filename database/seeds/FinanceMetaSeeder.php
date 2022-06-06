<?php

use App\Models\FinanceMeta;
use Illuminate\Database\Seeder;

class FinanceMetaSeeder extends Seeder
{
    protected $data = [
        'Expense Screen' =>	'FinanceExpense',
        'Expense Item Screen' =>	'FinanceExpenseItem',
        'Advance Screen' =>	'FinanceAdvance',
        'Fix Asset Screen' =>	'FinanceAsset',
        'Pickup Screen' =>	'Pickup',
        'Delisheet Screen' =>	'DeliSheet',
        'Waybill Screen' =>	'Waybill',
    ];
    
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Schema::disableForeignKeyConstraints();
        foreach ($this->data as $label => $model) {
            FinanceMeta::create([
                'label'    => $label,
                'model' => $model
            ]);
        }
        Schema::enableForeignKeyConstraints();
    }
}
