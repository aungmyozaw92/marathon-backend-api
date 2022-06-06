<?php

use App\Models\Meta;
use Faker\Generator as Faker;

$factory->define(Meta::class, function (Faker $faker) {
    return [
        'key' => $faker->name,
        'value' => $faker->name
    ];
});
