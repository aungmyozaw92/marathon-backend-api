<?php

use App\Models\Role;
use Illuminate\Database\Seeder;
use App\Traits\TruncateTableSeeder;

class RolesTableSeeder extends Seeder
{
    use TruncateTableSeeder;
    
    protected $role = [
        'Admin' => 'Admin Role - can access all',
        'OS' => 'Operation Role - can access ',
        'CS' => 'Customer Service Role',
        'Finance' => 'Finance Role',
        'Delivery' => 'Delivery Role'
    ];
    
    public function run()
    {
        // DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        // Role::truncate();
        // DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        $this->truncate('roles');

        foreach ($this->role as $role => $description) {
            factory(Role::class)->create([
               'name'    => $role,
               'description' => $description
            ]);
        }
    }
}
