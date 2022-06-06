<?php

use App\Models\Staff;
use App\Models\Pickup;
use App\Models\Customer;
use App\Models\Merchant;
use Faker\Generator as Faker;

$factory->define(Pickup::class, function (Faker $faker) {
    $sender_type = $faker->randomElement($array = array('Merchant', 'Customer'));
    if ($sender_type == "Merchant") {
        $sender = Merchant::all()->random();
        $sender_associate_id = $sender->merchant_associates()->inRandomOrder()->first()->id;
    } else {
        $sender = Customer::all()->random();
    }
    // $sender_type = $faker->randomElement($array = array ('Customer'));

    return [
        // 'sender' => $faker->name,
        // 'sender_phone' => $faker->e164PhoneNumber,
        // 'sender_address' => $faker->address,
        'sender_type' => $sender_type,
        'sender_id' => $sender->id,
        'sender_associate_id' => isset($sender_associate_id) ? $sender_associate_id : null,
        'receiver_id' => function () {
            return Customer::all()->random()->id;
        },
        'pickup_invoice' => 'P' . str_pad($faker->numberBetween($min = 1, $max = 999999), 6, '0', STR_PAD_LEFT),
        'qty' => $faker->randomDigitNotNull,
        'total_delivery_amount' => $faker->randomFloat($nbMaxDecimals = 2, $min = 0, $max = 10000),
        'total_amount_to_collect' => $faker->randomFloat($nbMaxDecimals = 2, $min = 0, $max = 100000),
        'note'=> $faker->text,
        'type' => $faker->randomDigitNotNull,
        'opened_by' => function () {
            return Staff::where('department_id', 5)->get()->random()->id;
        },
        'created_by' => 1
    ];
});

$factory->state(Pickup::class, 'opened_by', function ($faker) {
    return [
        'opened_by' => $faker->randomDigitNotNull,
    ];
});

$factory->state(Pickup::class, 'sender_id', function ($faker) {
    return [
        'sender_id' => $faker->randomDigitNotNull,
    ];
});

$factory->state(Pickup::class, 'receiver_id', function ($faker) {
    return [
        'receiver_id' => $faker->randomDigitNotNull,
    ];
});
