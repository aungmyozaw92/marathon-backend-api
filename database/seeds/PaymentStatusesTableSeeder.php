<?php

use App\Models\PaymentStatus;
use Illuminate\Database\Seeder;

class PaymentStatusesTableSeeder extends Seeder
{
    protected $statuses = [
        ['name' => 'Prepaid', 'name_mm' => 'ကြိုပေးချေရန်'],
        ['name' => 'Postpaid', 'name_mm' => 'နောက်မှ​ပေးရန်'],
        ['name' => 'Partial paid', 'name_mm' => 'တစိတ်တဒေသပေးရန်'],
        ['name' => 'Settle', 'name_mm' => 'ပေးပြီး'],
        ['name' => 'Refund', 'name_mm' => 'ပြန်ပေးခြင်း']
    ];



    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        // PaymentStatus::truncate();
        // DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        Schema::disableForeignKeyConstraints();
        foreach ($this->statuses as $status) {
            factory(PaymentStatus::class)->create([
                'name' => $status["name"],
                'name_mm' => $status["name_mm"],
                // 'description' => $status["description"]
            ]);
        }
        Schema::enableForeignKeyConstraints();
    }
}
