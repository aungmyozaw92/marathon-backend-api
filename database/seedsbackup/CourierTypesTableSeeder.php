<?php

use App\Models\CourierType;
use Illuminate\Database\Seeder;
use App\Traits\TruncateTableSeeder;

class CourierTypesTableSeeder extends Seeder
{
    use TruncateTableSeeder;

    protected $courierTypes = [ 'bycicle', 'bike', 'car', 'truck' ];
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
        $this->truncate('courier_types');
        
        foreach ($this->courierTypes as $courierType) {
            factory(CourierType::class)->create([
                'name' => $courierType
            ]);
        }
    }
}
