<?php

use App\Models\StaffRole;
use Faker\Generator as Faker;

$factory->define(StaffRole::class, function (Faker $faker) {
    return [
       'staff_id'            => $faker->numberBetween($min = 1, $max = 5),
       'role_id'             => $faker->numberBetween($min = 1, $max = 5),
    ];
});
