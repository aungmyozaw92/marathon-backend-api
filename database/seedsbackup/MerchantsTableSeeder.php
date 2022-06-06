<?php

use App\Models\Role;
use App\Models\Merchant;
use Illuminate\Database\Seeder;
use App\Traits\TruncateTableSeeder;

class MerchantsTableSeeder extends Seeder
{
    use TruncateTableSeeder;
    
    protected $merchants = [
        ['name' => "Shop Online", 'username' => 'merchant', 'password' => '$2y$10$TKh8H1.PfQx37YgCzwiKb.KjNyWgaHb9cbcoQgdIVFlYg7B77UdFm'],
        ['name' => "BarLoLo Online", 'username' => 'merchant2', 'password' => '$2y$10$TKh8H1.PfQx37YgCzwiKb.KjNyWgaHb9cbcoQgdIVFlYg7B77UdFm'],
        ['name' => "MM Online", 'username' => 'merchant3', 'password' => '$2y$10$TKh8H1.PfQx37YgCzwiKb.KjNyWgaHb9cbcoQgdIVFlYg7B77UdFm'],
        ['name' => "New Online", 'username' => 'merchant4', 'password' => '$2y$10$TKh8H1.PfQx37YgCzwiKb.KjNyWgaHb9cbcoQgdIVFlYg7B77UdFm'],
        ['name' => "Merchant New", 'username' => 'merchant5', 'password' => '$2y$10$TKh8H1.PfQx37YgCzwiKb.KjNyWgaHb9cbcoQgdIVFlYg7B77UdFm'],
        ['name' => "Data Online", 'username' => 'merchant6', 'password' => '$2y$10$TKh8H1.PfQx37YgCzwiKb.KjNyWgaHb9cbcoQgdIVFlYg7B77UdFm'],
        ['name' => "BlaBla Online", 'username' => 'merchant7', 'password' => '$2y$10$TKh8H1.PfQx37YgCzwiKb.KjNyWgaHb9cbcoQgdIVFlYg7B77UdFm'],
        ['name' => "Okkar Online", 'username' => 'okkar123', 'password' => '$2y$10$TKh8H1.PfQx37YgCzwiKb.KjNyWgaHb9cbcoQgdIVFlYg7B77UdFm'],
        ['name' => "AKS Online", 'username' => 'aks123', 'password' => '$2y$10$TKh8H1.PfQx37YgCzwiKb.KjNyWgaHb9cbcoQgdIVFlYg7B77UdFm'],
        ['name' => "SettWai Online", 'username' => 'settwai123', 'password' => '$2y$10$TKh8H1.PfQx37YgCzwiKb.KjNyWgaHb9cbcoQgdIVFlYg7B77UdFm'],
        ['name' => "Nan Nan Online", 'username' => 'nan123', 'password' => '$2y$10$TKh8H1.PfQx37YgCzwiKb.KjNyWgaHb9cbcoQgdIVFlYg7B77UdFm'],
        
    ];
    
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->truncate('merchants');

        // DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        // Merchant::truncate();
        // DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        foreach ($this->merchants as $merchant) {
            factory(Merchant::class)->create([
                'name' => $merchant["name"],
                'username' => $merchant["username"],
                'password' => $merchant["password"],
                // 'fix_pickup_price' => $merchant["fix_pickup_price"],
                // 'fix_dropoff_price' => $merchant["fix_dropoff_price"],
                // 'fix_delivery_price' => $merchant["fix_delivery_price"]
            ]);
        }

        // $merchant = factory(Merchant::class)->create([
        //     'name'             => 'merchant',
        //     'username'         => 'merchant',
        // ]);
        
        // factory(Merchant::class, 20)->create();
    }
}
