<?php

use App\Models\BusDropOff;
use Faker\Generator as Faker;

$factory->define(BusDropOff::class, function (Faker $faker) {
    return [
        'gate_id' => $faker->numberBetween($min = 1, $max = 100),
        'global_scale_id' => $faker->numberBetween($min = 1, $max = 100),
        'base_rate' => $faker->numberBetween($min = 500, $max = 1000),
        'agent_base_rate' => $faker->numberBetween($min = 500, $max = 1000),
        'salt' => $faker->numberBetween($min = 500, $max = 1000),
    ];
});
