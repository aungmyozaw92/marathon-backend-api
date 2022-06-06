<?php

use App\Models\LogStatus;
use Faker\Generator as Faker;

$factory->define(LogStatus::class, function (Faker $faker) {
    return [
        'value'          => $faker->name,
        'description'    => $faker->sentence,
        'description_mm' => $faker->sentence
    ];
});
