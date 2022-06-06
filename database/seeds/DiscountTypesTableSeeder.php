<?php

use App\Models\DiscountType;
use Illuminate\Database\Seeder;

class DiscountTypesTableSeeder extends Seeder
{
    protected $status = [
        'Percentage' => 'percentage base discount',
        'Flat' => 'flat rate discount',
        'Volume' => 'unlock this discount after some merchant hit target volume',
        // 'location' => 'unlock this discount only from X to Y',
    ];

    /**
     * Run the database seeds.
     */
    public function run()
    {
        // DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        // DiscountType::truncate();
        // DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        Schema::disableForeignKeyConstraints();
        foreach ($this->status as $name => $description) {
            factory(DiscountType::class)->create([
                'name' => $name,
                'description' => $description,
            ]);
        }
        Schema::enableForeignKeyConstraints();
    }
}
