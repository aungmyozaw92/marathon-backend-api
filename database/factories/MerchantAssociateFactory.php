<?php

use App\Models\City;
use App\Models\Meta;
use App\Models\Zone;
use App\Models\Merchant;
use Faker\Generator as Faker;
use App\Models\MerchantAssociate;

$factory->define(MerchantAssociate::class, function (Faker $faker) {
   // $branch_city_id = Meta::where('key', 'branch')->first()->value;

    $city = City::find(66);
    $zone = $city->zones()->inRandomOrder()->first();

    return [
        'merchant_id' => function () {
            return Merchant::all()->random()->id;
        },
        'label' => $faker->word,
        'address' => $faker->address,
        'city_id' => $city->id,
        'zone_id' => isset($zone) ? $zone->id : null,
        
    ];
});

$factory->state(MerchantAssociate::class, 'merchant_id', function ($faker) {
    return [
        'merchant_id' => $faker->randomDigitNotNull,
    ];
});

// $factory->state(MerchantAssociate::class, 'city_id', function ($faker) {
//     return [
//         'city_id' => $faker->randomDigitNotNull,
//     ];
// });

$factory->state(MerchantAssociate::class, 'zone_id', function ($faker) {
    return [
        'zone_id' => $faker->randomDigitNotNull,
    ];
});
