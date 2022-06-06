<?php

use App\Models\DiscountType;
use Faker\Generator as Faker;

$factory->define(DiscountType::class, function (Faker $faker) {
    return [
        'name' => $faker->name,
        'description' => $faker->name,
    ];
});
