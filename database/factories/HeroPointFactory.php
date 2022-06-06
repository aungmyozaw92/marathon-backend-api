<?php

use Faker\Generator as Faker;

$factory->define(App\Models\HeroPoint::class, function (Faker $faker) {
    return [
        'start_point' => $faker->randomFloat(2, 1, 1500),
        'end_point' => $faker->randomFloat(2, 499, 1500),
        'bonus' => $faker->numberBetween($min = 100000, $max = 200000)
    ];
});
