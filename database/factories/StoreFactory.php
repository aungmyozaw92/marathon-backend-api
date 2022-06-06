<?php

use App\Models\Store;
use App\Models\Merchant;
use Illuminate\Support\Str;
use Faker\Generator as Faker;

$factory->define(Store::class, function (Faker $faker) {
    return [
        'uuid'         => Str::orderedUuid(),
        'item_name'    => $faker->name,
        'item_price'   => $faker->numberBetween($min = 1000, $max = 9000),
        'merchant_id'  => function () {
            return Merchant::all()->random()->id;
        },
    ];
});

$factory->state(Store::class, 'merchant_id', function ($faker) {
    return [
        'merchant_id' => $faker->randomDigitNotNull,
    ];
});
