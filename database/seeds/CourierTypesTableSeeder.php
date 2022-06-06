<?php

use App\Models\CourierType;
use Illuminate\Database\Seeder;

class CourierTypesTableSeeder extends Seeder
{
    protected $courierTypes = ['bycicle', 'bike', 'car', 'truck'];
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        // CourierType::truncate();
        // DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        Schema::disableForeignKeyConstraints();
        foreach ($this->courierTypes as $courierType) {
            factory(CourierType::class)->create([
                'name' => $courierType
            ]);
        }
        Schema::enableForeignKeyConstraints();
    }
}
