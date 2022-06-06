<?php

use App\Models\Gate;
use App\Models\Bus;
use App\Models\BusStation;
use Faker\Generator as Faker;

$factory->define(Gate::class, function (Faker $faker) {
    return [
        'name' => $faker->name,
       // 'gate_rate' => $faker->name,
        'bus_station_id' => function () {
            return BusStation::all()->random()->id;
        },
        'bus_id' => function () {
            return Bus::all()->random()->id;
        }
    ];
});

$factory->state(BusStation::class, 'bus_station_id', function ($faker) {
    return [
        'bus_station_id' => $faker->randomDigitNotNull,
    ];
});

$factory->state(Bus::class, 'bus_id', function ($faker) {
    return [
        'bus_id' => $faker->randomDigitNotNull,
    ];
});
