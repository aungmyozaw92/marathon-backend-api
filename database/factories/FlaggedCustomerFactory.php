<?php

use App\Models\Flag;
use App\Models\Customer;
use Faker\Generator as Faker;
use App\Models\FlaggedCustomer;

$factory->define(FlaggedCustomer::class, function (Faker $faker) {
    return [
        'frequency' => $faker->randomDigitNotNull,
        'flag_id' => function () {
            return Flag::all()->random()->id;
        },
        'customer_id' => function () {
            return Customer::all()->random()->id;
        }
    ];
});

$factory->state(FlaggedCustomer::class, 'flag_id', function ($faker) {
    return [
        'flag_id' => $faker->randomDigitNotNull,
    ];
});

$factory->state(FlaggedCustomer::class, 'customer_id', function ($faker) {
    return [
        'customer_id' => $faker->randomDigitNotNull,
    ];
});
