<?php

use App\Models\CallStatus;
use Faker\Generator as Faker;

$factory->define(CallStatus::class, function (Faker $faker) {
    return [
        'status'    => $faker->name,
        'status_mm' => $faker->name
    ];
});
