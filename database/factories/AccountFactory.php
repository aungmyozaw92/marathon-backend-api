<?php

use App\Models\City;
use App\Models\Gate;
use App\Models\Staff;
use App\Models\Account;
use App\Models\Customer;
use App\Models\Merchant;
use App\Models\Zone;
use Faker\Generator as Faker;

$factory->define(Account::class, function (Faker $faker) {
    // $type = $faker->randomElement($array = array( 'Merchant', 'Customer', 'Staff', 'Zone','Gate'));
    // if ($type == 'Merchant') {
    //     $type_id = Merchant::all()->random()->id;
    // } elseif ($type == 'Customer') {
    //     $type_id = Customer::all()->random()->id;
    // } elseif ($type == 'Staff') {
    //     $type_id = Staff::where('department_id', 5)->get()->random()->id;
    // } elseif ($type == 'Zone') {
    //     $type_id = Zone::all()->random()->id;
    // } elseif ($type == 'Gate') {
    //     $type_id = Gate::all()->random()->id;
    // }
    // /* else {
    //     $type_id = Zone::all()->random()->id;

    // } */
    return [
        'account_no' => 'A' . str_pad($faker->numberBetween($min = 1, $max = 999999), 6, '0', STR_PAD_LEFT),
        'accountable_type' => 'HQ',
        'accountable_id' => 1,
        'credit' => 0,
        'debit' => 0,
        'balance' => 0
    ];
});
// $factory->state(Account::class, 'city_id', function ($faker) {
//     return [
//         'city_id' => $faker->randomDigitNotNull,
//     ];
// });
