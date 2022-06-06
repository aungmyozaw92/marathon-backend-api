<?php

use App\Models\Merchant;
use App\Models\Zone;
use Faker\Generator as Faker;
use App\Models\ContactAssociate;
use App\Models\MerchantAssociate;

$factory->define(ContactAssociate::class, function (Faker $faker) {
    $type = $faker->randomElement($array = array('phone', 'email'));

    if ($type == 'phone') {
        $value = $faker->e164PhoneNumber;
    } else {
        $value = $faker->unique()->safeEmail;
    }

    return [
        'merchant_id' => function () {
            return Merchant::all()->random()->id;
        },
        'merchant_associate_id' => function () {
            return MerchantAssociate::all()->random()->id;
        },
        'type' => $type,
        'value'=> $value,
    ];
});

$factory->state(ContactAssociate::class, 'merchant_id', function ($faker) {
    return [
        'merchant_id' => $faker->randomDigitNotNull,
    ];
});

$factory->state(ContactAssociate::class, 'merchant_associate_id', function ($faker) {
    return [
        'merchant_associate_id' => $faker->randomDigitNotNull,
    ];
});
