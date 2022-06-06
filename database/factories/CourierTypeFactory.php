<?php

use App\Models\CourierType;
use Faker\Generator as Faker;

$factory->define(CourierType::class, function (Faker $faker) {
    return [
        'name'   => $faker->name,
        'rate'   => $faker->randomFloat($nbMaxDecimals = 2, $min = 0, $max = 100),
        'cbm'    => $faker->randomFloat($nbMaxDecimals = 2, $min = 0, $max = 100),
        'weight' => $faker->randomFloat($nbMaxDecimals = 2, $min = 0, $max = 100)
    ];
});
