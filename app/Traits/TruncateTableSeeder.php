<?php
namespace App\Traits;

use Illuminate\Support\Facades\DB;

trait TruncateTableSeeder
{
    public function truncate($table)
    {
        if (app()->environment() == 'production') {
            //   DB::statement('TRUNCATE TABLE ' . $table . ' CASCADE;');
            DB::statement("TRUNCATE TABLE {$table} RESTART IDENTITY CASCADE");
        } else {
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
            DB::table($table)->truncate();
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        }
    }
}
