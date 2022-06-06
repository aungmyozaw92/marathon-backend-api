<?php

use App\Models\Staff;
use App\Models\Zone;
use App\Models\Department;
use App\Models\CourierType;
use Faker\Generator as Faker;

$factory->define(Staff::class, function (Faker $faker) {
    return [
       'name'             => $faker->name,
       'role_id'          => $faker->numberBetween($min = 2, $max = 4),
       'department_id'    => function () {
           return Department::all()->random()->id;
      },
       'username'         => $faker->unique()->name,
       'password'         => '$2y$10$TKh8H1.PfQx37YgCzwiKb.KjNyWgaHb9cbcoQgdIVFlYg7B77UdFm', // secret;
    //    'zone_id'      => function () {
    //        return Zone::all()->random()->id;
    //    },
    //    'courier_type_id' => function () {
    //        return CourierType::all()->random()->id;
    //    }
    ];
});


// $factory->state(Staff::class, 'department_id', function ($faker) {
//     return [
//         'department_id' => $faker->randomDigitNotNull,
//     ];
// });

// $factory->state(Staff::class, 'zone_id', function ($faker) {
//     return [
//         'zone_id' => $faker->randomDigitNotNull,
//     ];
// });

// $factory->state(Staff::class, 'courier_type_id', function ($faker) {
//     return [
//         'courier_type_id' => $faker->randomDigitNotNull,
//     ];
// });
