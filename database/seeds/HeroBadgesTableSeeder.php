<?php

use Illuminate\Database\Seeder;
use App\Models\HeroBadge;

class HeroBadgesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    protected $badges = [
        ['name' => 'Marathoner', 'logo' => 'logo_1592815124_Ordinary_3.png', 'description' => 'description_1','multiplier_point' => 1, 'maintainence_point' => 500],
        ['name' => 'Silver', 'logo' => 'logo_1592815124_Ordinary_3.png', 'description' => 'description_2', 'multiplier_point' => 1.2, 'maintainence_point' => 800],
        ['name' => 'Gold', 'logo' => 'logo_1592815124_Ordinary_3.png', 'description' => 'description_3', 'multiplier_point' => 1.4, 'maintainence_point' => 1000],
        ['name' => 'Platinum', 'logo' => 'logo_1592815124_Ordinary_3.png', 'description' => 'description_4', 'multiplier_point' => 1.6, 'maintainence_point' => 1200],
    ];
    public function run()
    {
        Schema::disableForeignKeyConstraints();
        foreach ($this->badges as $badge) {
            factory(HeroBadge::class)->create([
                'name' => $badge['name'],
                'logo' => $badge['logo'],
                'description' => $badge['description'],
                'multiplier_point' => $badge['multiplier_point'],
                'maintainence_point' => $badge['maintainence_point'],
            ]);
        }
        Schema::enableForeignKeyConstraints();
    }
}
