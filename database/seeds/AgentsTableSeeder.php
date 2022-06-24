<?php

use App\Models\Agent;
use Illuminate\Database\Seeder;

class AgentsTableSeeder extends Seeder
{
    protected $cities = [
        ['name' => "U Win Mg", 'username' => 'balalalal', 'city_id' => 1, 'password' => 'balalalal'],
        ['name' => "U Kyaw Kyaw", 'username' => 'balalalal', 'city_id' => 2, 'password' => 'balalalal'],
        ['name' => "U Hla", 'username' => 'balalalal', 'city_id' => 3, 'password' => 'balalalal'],
        ['name' => "Daw Mya", 'username' => 'balalalal', 'city_id' => 4, 'password' => 'balalalal'],
        ['name' => "Ko Oo", 'username' => 'balalalal', 'city_id' => 5, 'password' => 'balalalal'],
        ['name' => "Ko Min Min", 'username' => 'balalalal', 'city_id' => 6, 'password' => 'balalalal'],
        ['name' => "U Hla Myint", 'username' => 'balalalal', 'city_id' => 7, 'password' => 'balalalal'],
        ['name' => "U Soe", 'username' => 'balalalal', 'city_id' => 8, 'password' => 'balalalal'],
        ['name' => "Daw Kyu Kyu", 'username' => 'balalalal', 'city_id' => 9, 'password' => 'balalalal'],
        ['name' => "U Mg Mg", 'username' => 'balalalal', 'city_id' => 10, 'password' => 'balalalal'],
    ];

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        // Agent::truncate();
        // DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        Schema::disableForeignKeyConstraints();
        foreach ($this->cities as $city) {
            factory(Agent::class)->create([
                'name' => $city["name"],
                'username' => $city["username"],
                'password' => $city["password"],
                'city_id' => $city["city_id"],
            ]);
        }
        Schema::enableForeignKeyConstraints();
    }
}
