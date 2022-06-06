<?php

use App\Models\Coupon;
use App\Models\Customer;
use Faker\Generator as Faker;
use App\Models\CouponAssociate;

$factory->define(CouponAssociate::class, function (Faker $faker) {
    return [
        'code' => str_random(6),
        'coupon_id' => function () {
            return Coupon::all()->random()->id;
        },

        'valid' => $faker->randomElement($array = array(1)),
    ];
});

$factory->state(CouponAssociate::class, 'coupon_id', function ($faker) {
    return [
        'coupon_id' => $faker->randomDigitNotNull,
    ];
});
