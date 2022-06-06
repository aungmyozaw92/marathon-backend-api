<?php

use Faker\Generator as Faker;

$factory->define(App\Models\Deduction::class, function (Faker $faker) {
    return [
        //
        'points' => $faker->randomFloat(2, 10, 10),
        'description' => $faker->sentence
    ];
});
