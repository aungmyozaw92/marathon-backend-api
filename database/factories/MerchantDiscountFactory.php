<?php

use App\Models\City;
use App\Models\Merchant;
use App\Models\Zone;
use App\Models\BusStation;
use App\Models\DiscountType;
use Faker\Generator as Faker;
use App\Models\MerchantDiscount;

$factory->define(MerchantDiscount::class, function (Faker $faker) {
    $type = $faker->randomElement($array = array('1', '2'));
    if ($type == '1') {
        $amount = $faker->numberBetween($min = 10, $max = 10);
    } elseif ($type == '2') {
        $amount = $faker->numberBetween($min = 100, $max = 1000);
        $volume = null;
    }

    return [
        'amount' => $amount,
        'discount_type_id' => function () {
            return DiscountType::all()->random()->id;
        },
        // 'volume' => $volume,
        'merchant_id' => function () {
            return Merchant::all()->random()->id;
        },
        'normal_or_dropoff' => $faker->randomElement($array = array(0, 1)),
        'extra_or_discount' => $faker->randomElement($array = array(0, 1)),
        'sender_city_id' => function () {
            return City::all()->random()->id;
        },
        'receiver_city_id' => function () {
            return City::all()->random()->id;
        },
        'sender_zone_id' => function () {
            return Zone::all()->random()->id;
        },
        'receiver_zone_id' => function () {
            return Zone::all()->random()->id;
        },
        'from_bus_station_id' => function () {
            return BusStation::all()->random()->id;
        },
        'to_bus_station_id' => function () {
            return BusStation::all()->random()->id;
        },
        
    ];
});

$factory->state(MerchantDiscount::class, 'discount_type_id', function ($faker) {
    return [
        'discount_type_id' => $faker->randomDigitNotNull,
    ];
});

$factory->state(MerchantDiscount::class, 'merchant_id', function ($faker) {
    return [
        'merchant_id' => $faker->randomDigitNotNull,
    ];
});

$factory->state(MerchantDiscount::class, 'sender_city_id', function ($faker) {
    return [
        'sender_city_id' => $faker->randomDigitNotNull,
    ];
});
$factory->state(MerchantDiscount::class, 'receiver_city_id', function ($faker) {
    return [
        'receiver_city_id' => $faker->randomDigitNotNull,
    ];
});
$factory->state(MerchantDiscount::class, 'sender_zone_id', function ($faker) {
    return [
        'sender_zone_id' => $faker->randomDigitNotNull,
    ];
});
$factory->state(MerchantDiscount::class, 'receiver_zone_id', function ($faker) {
    return [
        'receiver_zone_id' => $faker->randomDigitNotNull,
    ];
});
$factory->state(MerchantDiscount::class, 'from_bus_station_id', function ($faker) {
    return [
        'from_bus_station_id' => $faker->randomDigitNotNull,
    ];
});
$factory->state(MerchantDiscount::class, 'to_bus_station_id', function ($faker) {
    return [
        'to_bus_station_id' => $faker->randomDigitNotNull,
    ];
});
