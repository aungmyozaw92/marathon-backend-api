<?php

use App\Models\Department;
use Faker\Generator as Faker;

$factory->define(Department::class, function (Faker $faker) {
    return [
        'authority' => $faker->name,
        'department' => $faker->name
    ];
});
