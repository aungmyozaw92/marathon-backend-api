<?php

use App\Models\City;
use App\Models\Route;
use Faker\Generator as Faker;

$factory->define(Route::class, function (Faker $faker) {
    return [
        //'route_rate' => $faker->numberBetween($min = 500, $max = 2000),
        'travel_day' => $faker->numberBetween($min = 1, $max = 10),
        'route_name' => $faker->name,
        'origin_id'       => function () {
            return City::all()->random()->id;
        },
        'destination_id'       => function () {
            return City::all()->random()->id;
        },
    ];
});

$factory->state(Route::class, 'city_id', function ($faker) {
    return [
        'origin_id' => $faker->randomDigitNotNull,
    ];
});
$factory->state(Route::class, 'city_id', function ($faker) {
    return [
        'destination_id' => $faker->randomDigitNotNull,
    ];
});
