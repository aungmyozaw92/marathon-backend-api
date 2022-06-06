<?php

use App\Models\City;
use App\Models\Zone;
use App\Models\BusStation;
use Faker\Generator as Faker;

$factory->define(BusStation::class, function (Faker $faker) {
    return [
        'name' => $faker->name,
        // 'lat' => $faker->latitude,
        // 'long' => $faker->longitude,
        'number_of_gates' => $faker->randomDigitNotNull,
        'delivery_rate' => $faker->name,
        'city_id' => function () {
            return City::all()->random()->id;
        },
        'zone_id' => function () {
            return Zone::all()->random()->id;
        },
    ];
});

$factory->state(BusStation::class, 'city_id', function ($faker) {
    return [
        'city_id' => $faker->randomDigitNotNull,
    ];
});

$factory->state(BusStation::class, 'zone_id', function ($faker) {
    return [
        'zone_id' => $faker->randomDigitNotNull,
    ];
});
