<?php

use App\Models\City;
use App\Models\User;
use Faker\Generator as Faker;

$factory->define(City::class, function (Faker $faker) {
    return [
        'name'          => $faker->name,
       
    ];
});


