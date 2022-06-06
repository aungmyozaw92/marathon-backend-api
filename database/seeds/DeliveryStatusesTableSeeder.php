<?php

use Illuminate\Database\Seeder;
use App\Models\DeliveryStatus;

class DeliveryStatusesTableSeeder extends Seeder
{
    protected $status = [
        'No attempt' => 'ပို့ဖို့မကြိုးစားရသေးပါ',
        'First attempt' => 'ပထမဦးစွာကြိုးပမ်းမှု',
        'Second attempt' => 'ဒုတိယအကြိမ်ကြိုးပမ်းမှု',
        'Third attempt' => 'တတိယအကြိမ်ကြိုးပမ်းမှု',
        'Wrong address' => 'လိပ်စာမှားယွင်း',
        'Wrong person' => 'ပုဂ္ဂိုလ်နာမည်မှားယွင်း',
        'Wrong phone' => 'ဖုန်းမှားယွင်း',
        'Delivered' => 'ပို့ဆောင်မှုပြီးဆုံး',
        'Return' => 'ပြန်ပို့',
        'Can\'t Deliver' => 'ပို့မရ',
        'Shipped' => 'လိပ်စာမှားယွင်း(Shipped)'
    ];

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        // DeliveryStatus::truncate();
        // DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        Schema::disableForeignKeyConstraints();
        foreach ($this->status as $status => $status_mm) {
            factory(DeliveryStatus::class)->create([
                'status'    => $status,
                'status_mm' => $status_mm
            ]);
        }
        Schema::enableForeignKeyConstraints();
    }
}
