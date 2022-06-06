<?php

use App\Models\GlobalScale;
use Illuminate\Database\Seeder;

class GlobalScalesTableSeeder extends Seeder
{
    protected $globalScales = [
        ['cbm' => 16, 'support_weight' => 2, 'max_weight' => 10, 'global_scale_rate' => 1200, 'description' => 'Tissue box', 'description_mm' => 'တစ်ရှုးဘောက်စ်', 'salt' => 1000],
        ['cbm' => 23.5, 'support_weight' => 4, 'max_weight' => 15, 'global_scale_rate' => 1300, 'description' => 'A Bag', 'description_mm' => 'လွယ်အိပ်', 'salt' => 800],
        ['cbm' => 26, 'support_weight' => 6, 'max_weight' => 20, 'global_scale_rate' => 1400, 'description' => 'A small plastic bag', 'description_mm' => 'ပလက်စတစ်ချင်းတောင်းသေး', 'salt' => 600],
        ['cbm' => 28, 'support_weight' => 8, 'max_weight' => 30, 'global_scale_rate' => 1500, 'description' => 'A medium plastic bag', 'description_mm' => 'ပလက်စတစ်ချင်း အလယ္အလတ္', 'salt' => 450],
        ['cbm' => 32, 'support_weight' => 10, 'max_weight' => 35, 'global_scale_rate' => 1600, 'description' => 'A large plastic bag', 'description_mm' => 'ပလက်စတစ်ချင်းတောင်းကြီး', 'salt' => 450]
    ];
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        // GlobalScale::truncate();
        // DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        Schema::disableForeignKeyConstraints();
        foreach ($this->globalScales as $globalScale) {
            factory(GlobalScale::class)->create([
                'cbm' => $globalScale["cbm"],
                'support_weight' => $globalScale["support_weight"],
                'max_weight' => $globalScale["max_weight"],
                //'global_scale_rate' => $globalScale["global_scale_rate"],
                'description' => $globalScale["description"],
                'description_mm' => $globalScale["description_mm"],
                //'salt' => $globalScale["salt"]
            ]);
        }
        Schema::enableForeignKeyConstraints();
    }
}
