<?php

use App\Models\AgentBadge;
use Illuminate\Database\Seeder;

class AgentBadgesTableSeeder extends Seeder
{
    protected $badges = [
        ['name' => 'Ordinary', 'deposit' =>0.0000, 'logo' => 'logo_1592815124_Ordinary_3.png', 'monthly_reward' => 0.0000, 'delivery_points' => 0, 'weekly_payment' => 0.0000, 'monthly_good_credit' => 0.0000],
        ['name' => 'Silver', 'deposit' =>200000.0000, 'logo' => 'logo_1592813579_Silver_1.png', 'monthly_reward' => 10000.0000, 'delivery_points' => 1, 'weekly_payment' => 0.0010, 'monthly_good_credit' => 0.0010],
        ['name' => 'Gold', 'deposit' =>500000.0000, 'logo' => 'logo_1592813694_Gold_1.png', 'monthly_reward' => 20000.0000, 'delivery_points' => 2, 'weekly_payment' => 0.0015, 'monthly_good_credit' => 0.0020],
        ['name' => 'Platinum', 'deposit' =>1000000.0000, 'logo' => 'logo_1592813778_Platinum_1.png', 'monthly_reward' => 30000.0000, 'delivery_points' => 3, 'weekly_payment' => 0.0020, 'monthly_good_credit' => 0.0030]
    ];

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Schema::disableForeignKeyConstraints();
        foreach ($this->badges as $badge) {
            factory(AgentBadge::class)->create([
                'name'                => $badge["name"],
                'deposit'             => $badge["deposit"],
                'logo'                => $badge["logo"],
                'monthly_reward'      => $badge["monthly_reward"],
                'delivery_points'     => $badge["delivery_points"],
                'weekly_payment'      => $badge["weekly_payment"],
                'monthly_good_credit' => $badge["monthly_good_credit"],
            ]);
        }
        Schema::enableForeignKeyConstraints();
    }
}
