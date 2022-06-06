<?php

use App\Models\Store;
use Illuminate\Database\Seeder;
use App\Traits\TruncateTableSeeder;

class StoresTableSeeder extends Seeder
{
    use TruncateTableSeeder;
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->truncate('stores');

        factory(Store::class, 120)->create();
    }
}
