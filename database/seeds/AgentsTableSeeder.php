<?php

use App\Models\Agent;
use Illuminate\Database\Seeder;

class AgentsTableSeeder extends Seeder
{
    protected $cities = [
        ['name' => "U Win Mg", 'username' => 'uwinmg123', 'city_id' => 1, 'password' => '$2y$10$TKh8H1.PfQx37YgCzwiKb.KjNyWgaHb9cbcoQgdIVFlYg7B77UdFm'],
        ['name' => "U Kyaw Kyaw", 'username' => 'ukyaw123', 'city_id' => 2, 'password' => '$2y$10$TKh8H1.PfQx37YgCzwiKb.KjNyWgaHb9cbcoQgdIVFlYg7B77UdFm'],
        ['name' => "U Hla", 'username' => 'uhla123', 'city_id' => 3, 'password' => '$2y$10$TKh8H1.PfQx37YgCzwiKb.KjNyWgaHb9cbcoQgdIVFlYg7B77UdFm'],
        ['name' => "Daw Mya", 'username' => 'dawmya123', 'city_id' => 4, 'password' => '$2y$10$TKh8H1.PfQx37YgCzwiKb.KjNyWgaHb9cbcoQgdIVFlYg7B77UdFm'],
        ['name' => "Ko Oo", 'username' => 'kooo123', 'city_id' => 5, 'password' => '$2y$10$TKh8H1.PfQx37YgCzwiKb.KjNyWgaHb9cbcoQgdIVFlYg7B77UdFm'],
        ['name' => "Ko Min Min", 'username' => 'komin123', 'city_id' => 6, 'password' => '$2y$10$TKh8H1.PfQx37YgCzwiKb.KjNyWgaHb9cbcoQgdIVFlYg7B77UdFm'],
        ['name' => "U Hla Myint", 'username' => 'uhlam123', 'city_id' => 7, 'password' => '$2y$10$TKh8H1.PfQx37YgCzwiKb.KjNyWgaHb9cbcoQgdIVFlYg7B77UdFm'],
        ['name' => "U Soe", 'username' => 'usoe123', 'city_id' => 8, 'password' => '$2y$10$TKh8H1.PfQx37YgCzwiKb.KjNyWgaHb9cbcoQgdIVFlYg7B77UdFm'],
        ['name' => "Daw Kyu Kyu", 'username' => 'dawkyu123', 'city_id' => 9, 'password' => '$2y$10$TKh8H1.PfQx37YgCzwiKb.KjNyWgaHb9cbcoQgdIVFlYg7B77UdFm'],
        ['name' => "U Mg Mg", 'username' => 'umg123', 'city_id' => 10, 'password' => '$2y$10$TKh8H1.PfQx37YgCzwiKb.KjNyWgaHb9cbcoQgdIVFlYg7B77UdFm'],
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
