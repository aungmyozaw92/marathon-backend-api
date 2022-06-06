<?php


use App\Models\User;
use Illuminate\Database\Seeder;
use App\Traits\TruncateTableSeeder;

class UsersTableSeeder extends Seeder
{
    use TruncateTableSeeder;
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->truncate('users');
        
        factory(User::class)->create([
            'name' => 'super_admin',
            'email' => 'superadmin@gmail.com',
        ]);

        factory(User::class, 60)->create();
    }
}
