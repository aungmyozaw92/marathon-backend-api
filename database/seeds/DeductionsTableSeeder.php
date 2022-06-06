<?php

use Illuminate\Database\Seeder;
use App\Models\Deduction;

class DeductionsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    protected $deductions = [
        ['points' => 10, 'description' => 'Neat and Tidy'],
        ['points' => 10, 'description' => 'Betal Spitting/Smoking'],
        ['points' => 20, 'description' => 'Helmet Wearing'],
        ['points' => 20, 'description' => 'Mobile Phone Rental'],
        ['points' => 20, 'description' => 'Bicycle Rental'],
        ['points' => 10, 'description' => 'Payment/City Error'],
    ];
    public function run()
    {
        //
        foreach ($this->deductions as $deduction) {
            factory(Deduction::class)->create([
                'points' => $deduction['points'],
                'description' => $deduction['description'],
            ]);
        }
    }
}
