<?php


use App\Models\User;
use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        // User::truncate();
        // DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        Schema::disableForeignKeyConstraints();
        factory(User::class)->create([
            'name' => 'super_admin',
            'email' => 'sayargyi@marathonmyanmar.com',
        ]);

        factory(User::class, 5)->create();
        Schema::enableForeignKeyConstraints();
    }
}
