<?php

use Illuminate\Database\Seeder;
use App\Models\HeroPoint;

class HeroPointsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    protected $points = [
        ['start_point' => 0, 'end_point' => 499, 'bonus' => 0],
        ['start_point' => 500, 'end_point' => 749, 'bonus' => 100000],
        ['start_point' => 750, 'end_point' => 999, 'bonus' => 120000],
        ['start_point' => 1000, 'end_point' => 1249, 'bonus' => 140000],
        ['start_point' => 1250, 'end_point' => 1499, 'bonus' => 160000],
        ['start_point' => 1500, 'end_point' => 1749, 'bonus' => 200000],
    ];
    public function run()
    {
        //
        foreach ($this->points as $point) {
            factory(HeroPoint::class)->create([
                'start_point' => $point['start_point'],
                'end_point' => $point['end_point'],
                'bonus' => $point['bonus'],
            ]);
        }
    }
}
