<?php

use App\Models\PaymentStatus;
use Faker\Generator as Faker;

$factory->define(PaymentStatus::class, function (Faker $faker) {
    return [
        'name'    => $faker->name,
        'name_mm' => $faker->name
    ];
});
