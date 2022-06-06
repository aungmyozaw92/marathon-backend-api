<?php

use Faker\Generator as Faker;

$factory->define(App\Models\HeroBadge::class, function (Faker $faker) {
    return [
        'name' => $faker->name,
        'logo' => $faker->imageUrl($width = 640, $height = 480),
        'description' => $faker->sentence,
        'multiplier_point' => $faker->randomFloat(2, 1, 2),
        'maintainence_point' => $faker->randomFloat(2, 500, 1200),
    ];
});
