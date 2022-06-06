<?php

use App\Models\Product;
use Illuminate\Support\Str;
use Faker\Generator as Faker;

$factory->define(Product::class, function (Faker $faker) {
    return [
        'uuid'         => Str::orderedUuid(),
        'item_name'    => $faker->word,
        'item_price'   => $faker->numberBetween($min = 1000, $max = 50000),
        'merchant_id'  => 724,
        'created_by_id'  => 724,
        'created_by_type'  => 'Merchant',
        // 'merchant_id'  => function () {
        //     return Merchant::all()->random()->id;
        // },
    ];
});

// $factory->state(Product::class, 'merchant_id', function ($faker) {
//     return [
//         'merchant_id' => $faker->randomDigitNotNull,
//     ];
// });