<?php

use App\Models\Badge;
use Faker\Generator as Faker;

$factory->define(Badge::class, function (Faker $faker) {
    return [
        'name' => $faker->name,
        'logo' => $faker->imageUrl($width = 640, $height = 480),
        'description' => $faker->sentence
    ];
});
