<?php

use App\Models\City;
use App\Models\Zone;
use Faker\Generator as Faker;

$factory->define(Zone::class, function (Faker $faker) {
    return [
        'name'      => $faker->name,
        'zone_rate' => $faker->numberBetween($min = 1000, $max = 2000),
        'zone_agent_rate' => $faker->numberBetween($min = 1000, $max = 2000),
        'city_id'   => function () {
            return City::all()->random()->id;
        },
    ];
});

$factory->state(Zone::class, 'city_id', function ($faker) {
    return [
        'city_id' => $faker->randomDigitNotNull,
    ];
});
