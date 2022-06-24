<?php

use App\Models\Meta;
use App\Models\Role;
use App\Models\Merchant;
use Illuminate\Database\Seeder;

class MerchantsTableSeeder extends Seeder
{
    protected $merchants = [
        ['name' => "Shop Online", 'username' => 'balaalal', 'password' => 'balalal'],
        ['name' => "BarLoLo Online", 'username' => 'balaalal2', 'password' => 'balalal'],
        ['name' => "MM Online", 'username' => 'balaalal3', 'password' => 'balalal'],
        ['name' => "New Online", 'username' => 'balaalal4', 'password' => 'balalal'],
        ['name' => "Merchant New", 'username' => 'baalaa', 'password' => 'balalal'],
        ['name' => "Data Online", 'username' => 'asdfasdf', 'password' => 'balalal'],
        ['name' => "BlaBla Online", 'username' => 'asdfsadf', 'password' => 'balalal'],
        ['name' => "Okkar Online", 'username' => 'asdfsadf', 'password' => 'balalal'],
        ['name' => "AKS Online", 'username' => 'asdfasdf', 'password' => 'balalal'],
        ['name' => "SettWai Online", 'username' => 'asdfasdf', 'password' => 'balalal'],
        ['name' => "Nan Nan Online", 'username' => 'asdfsadfas', 'password' => 'balalal'],

    ];

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        // Merchant::truncate();
        // DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        Schema::disableForeignKeyConstraints();
        $branch_city_id = Meta::where('key', 'branch')->first()->value;

        foreach ($this->merchants as $merchant) {
            factory(Merchant::class)->create([
                'name' => $merchant["name"],
                'username' => $merchant["username"],
                'password' => $merchant["password"],
                'city_id' => $branch_city_id,
                // 'fix_pickup_price' => $merchant["fix_pickup_price"],
                // 'fix_dropoff_price' => $merchant["fix_dropoff_price"],
                // 'fix_delivery_price' => $merchant["fix_delivery_price"]
            ]);
        }
        Schema::enableForeignKeyConstraints();



        // $merchant = factory(Merchant::class)->create([
        //     'name'             => 'merchant',
        //     'username'         => 'merchant',
        // ]);

        // factory(Merchant::class, 20)->create();
    }
}
