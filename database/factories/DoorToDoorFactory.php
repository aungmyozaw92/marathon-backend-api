<?php

use App\Models\DoorToDoor;
use Faker\Generator as Faker;

$factory->define(DoorToDoor::class, function (Faker $faker) {
    return [
        'route_id' => $faker->numberBetween($min = 1, $max = 100),
        'global_scale_id' => $faker->numberBetween($min = 1, $max = 100),
        'base_rate' => $faker->numberBetween($min = 500, $max = 1000),
        'agent_base_rate' => $faker->numberBetween($min = 500, $max = 1000),
        'salt' => $faker->numberBetween($min = 500, $max = 1000),
        'agent_salt' => $faker->numberBetween($min = 500, $max = 1000),
    ];
});
