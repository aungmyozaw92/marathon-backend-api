<?php

use App\Models\StoreStatus;
use Faker\Generator as Faker;

$factory->define(StoreStatus::class, function (Faker $faker) {
    return [
        'status'    => $faker->name,
        'status_mm' => $faker->name
    ];
});
