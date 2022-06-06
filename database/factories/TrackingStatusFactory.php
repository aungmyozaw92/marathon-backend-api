<?php

use App\Models\TrackingStatus;
use Faker\Generator as Faker;

$factory->define(TrackingStatus::class, function (Faker $faker) {
    return [
        'status'    => $faker->name,
        'status_mm' => $faker->name
    ];
});
