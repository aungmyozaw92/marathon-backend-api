<?php

use App\Models\Coupon;
use Faker\Generator as Faker;
use App\Models\DiscountType;

$factory->define(Coupon::class, function (Faker $faker) {
    $discount_type = DiscountType::whereIn('id', [1,2])->get()->random()->id;
    if ($discount_type == 1) {
        $amount = $faker->numberBetween($min = 10, $max = 10);
    } else {
        $amount = $faker->numberBetween($min = 500, $max = 3000);
    }
    return [
        'amount' => $amount,
        'discount_type_id' => $discount_type,
        //'valid_date' => $faker->dateTimeThisYear('+1 month')
        'valid_date' => '2019-09-04 15:27:13'
    ];
});

$factory->state(Coupon::class, 'discount_type_id', function ($faker) {
    return [
        'discount_type_id' => $faker->randomDigitNotNull,
    ];
});
