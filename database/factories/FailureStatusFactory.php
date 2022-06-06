<?php

use Faker\Generator as Faker;
use App\Models\FailureStatus;

$factory->define(FailureStatus::class, function (Faker $faker) {
    return [
        'category'          => $faker->name,
        'specification'    => $faker->sentence
    ];
});
