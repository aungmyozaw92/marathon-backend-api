<?php

use App\Models\City;
use App\Models\Badge;
use App\Models\Customer;
use App\Models\Zone;
use Faker\Generator as Faker;

$factory->define(Customer::class, function (Faker $faker) {
    $city = City::all()->random();
    $zone = $city->zones()->inRandomOrder()->first();

    return [
        'name' => $faker->name,
        'phone' => $faker->e164PhoneNumber,
        'address' => $faker->address,
        'point' => $faker->numberBetween($min = 1, $max = 999999),
        'city_id' => $city->id,
        'zone_id' => isset($zone) ? $zone->id : null,
        'badge_id' => function () {
            return Badge::all()->random()->id;
        },
    ];
});

$factory->state(Customer::class, 'city_id', function ($faker) {
    return [
        'city_id' => $faker->randomDigitNotNull,
    ];
});


$factory->state(Customer::class, 'zone_id', function ($faker) {
    return [
        'zone_id' => $faker->randomDigitNotNull,
    ];
});

$factory->state(Customer::class, 'badge_id', function ($faker) {
    return [
        'badge_id' => $faker->randomDigitNotNull,
    ];
});
