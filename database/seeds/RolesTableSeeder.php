<?php

use App\Models\Role;
use Illuminate\Database\Seeder;

class RolesTableSeeder extends Seeder
{
    protected $role = [
        'Admin' => 'Admin Role - can access all',
        'Finance' => 'Finance Role',
        'Operation' => 'Operation Role - can access ',
        'CustomerService' => 'Customer Service Role',        
        'Delivery' => 'Delivery Role',
        'HQ' => 'HQ',
        'Agent' => 'Agent'
    ];

    public function run()
    {
        // DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        // Role::truncate();
        // DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        Schema::disableForeignKeyConstraints();
        foreach ($this->role as $role => $description) {
            factory(Role::class)->create([
                'name'    => $role,
                'description' => $description
            ]);
        }
        Schema::enableForeignKeyConstraints();
    }
}
