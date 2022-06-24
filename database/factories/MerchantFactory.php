<?php

use App\Models\Merchant;
use Faker\Generator as Faker;

$factory->define(Merchant::class, function (Faker $faker) {
    return [
        'name'                => $faker->name,
        'username'            => $faker->unique()->name,
        'password'            => 'balalalalal',
        // 'fix_pickup_price'    => $faker->numberBetween($min = 0, $max = 1000),
        // 'fix_dropoff_price'   => $faker->numberBetween($min = 0, $max = 3000),
        // 'fix_delivery_price'  => $faker->numberBetween($min = 0, $max = 3000),
    ];
});
