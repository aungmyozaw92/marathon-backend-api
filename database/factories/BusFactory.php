<?php

use App\Models\Bus;
use Faker\Generator as Faker;

$factory->define(Bus::class, function (Faker $faker) {
    return [
        'name' => $faker->name,
    ];
});
