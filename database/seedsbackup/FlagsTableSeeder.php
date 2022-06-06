<?php

use App\Models\Flag;
use Illuminate\Database\Seeder;
use App\Traits\TruncateTableSeeder;

class FlagsTableSeeder extends Seeder
{
    use TruncateTableSeeder;
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->truncate('flags');

        // DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        // Flag::truncate();
        // DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        
        Schema::disableForeignKeyConstraints();
        factory(Flag::class, 60)->create();
        Schema::enableForeignKeyConstraints();
    }
}
