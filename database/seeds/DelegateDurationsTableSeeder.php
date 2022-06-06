<?php

use Illuminate\Database\Seeder;
use App\Models\DelegateDuration;

class DelegateDurationsTableSeeder extends Seeder
{
    protected $delegates = [
        ['time' => 15, 'value' => "min"],
        ['time' => 30, 'value' => "min"],
        ['time' => 45, 'value' => "min"],
        ['time' => 1, 'value' => "hr"],
        ['time' => 2, 'value' => "hr"],
        ['time' => 3, 'value' => "hr"],
    ];

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        // DelegateDuration::truncate();
        // DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        Schema::disableForeignKeyConstraints();
        foreach ($this->delegates as $delegate) {
            factory(DelegateDuration::class)->create([
                'time' => $delegate["time"],
                'value' => $delegate["value"]
            ]);
        }
        Schema::enableForeignKeyConstraints();
    }
}
