<?php

use Faker\Generator as Faker;
use App\Models\DeliveryStatus;

$factory->define(DeliveryStatus::class, function (Faker $faker) {
    return [
        'status'    => $faker->name,
        'status_mm' => $faker->name
    ];
});
