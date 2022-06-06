<?php

use App\Models\Badge;
use Illuminate\Database\Seeder;

class BadgesTableSeeder extends Seeder
{
    protected $badge = [
        'marathoner' => 'Guides and Community Managers who work at Marathon Myanmar',
        'silver' => 'new members who are developing their product knowledge',
        'gold' => 'Trusted members who are knowledgeable, active contributors',
        'platinum' => ' Seasoned members who give advice, mentor, create content, and more'
    ];

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        // Badge::truncate();
        // DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        Schema::disableForeignKeyConstraints();
        foreach ($this->badge as $name => $description) {
            factory(Badge::class)->create([
                'name'    => $name,
                'description' => $description
            ]);
        }
        Schema::enableForeignKeyConstraints();
    }
}
