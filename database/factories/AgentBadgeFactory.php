<?php

use App\Models\AgentBadge;
use Faker\Generator as Faker;

$factory->define(AgentBadge::class, function (Faker $faker) {
    return [
        'name' => $faker->name,
        'deposit' => $faker->numberBetween($min = 1, $max = 100),
        'logo' => $faker->imageUrl($width = 640, $height = 480),
        'monthly_reward' => $faker->numberBetween($min = 1, $max = 100),
        'delivery_points' => $faker->numberBetween($min = 1, $max = 100),
        'weekly_payment' => $faker->numberBetween($min = 1, $max = 100),
        'monthly_good_credit' => $faker->numberBetween($min = 1, $max = 100)
    ];
});
