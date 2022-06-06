<?php

use App\Models\Badge;
use Illuminate\Database\Seeder;
use App\Traits\TruncateTableSeeder;

class BadgesTableSeeder extends Seeder
{
    use TruncateTableSeeder;
    
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
        $this->truncate('badges');

        foreach ($this->badge as $name => $description) {
            factory(Badge::class)->create([
                'name'    => $name,
                'description' => $description
            ]);
        }
    }
}
