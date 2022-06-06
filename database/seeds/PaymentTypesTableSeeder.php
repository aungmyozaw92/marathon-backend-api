<?php

use App\Models\PaymentType;
use Illuminate\Database\Seeder;

class PaymentTypesTableSeeder extends Seeder
{
    protected $paymentTypes = [
        ['name' => 'Sum total', 'name_mm' => 'ပို့ခ ပေါင်းရန်', 'description' => null, 'default' => null],
        ['name' => 'Net total', 'name_mm' => 'ပို့ခ ပါပြီး', 'description' => null, 'default' => null],
        ['name' => 'Delivery only', 'name_mm' => 'ပို့ခ သာကောက်ခံရန်', 'description' => null, 'default' => null],
        ['name' => 'Nothing to collect', 'name_mm' => 'ငွေမကောက်ရပါ', 'description' => null, 'default' => null],
        ['name' => 'Unpaid Delivery & Unpaid Bus fee', 'name_mm' => 'ဒလီကြွေး ကုန်ကြွေး', 'description' => null, 'default' => 1500],
        ['name' => 'Unpaid Delivery & Paid Bus fee', 'name_mm' => 'ဒလီကြွေး ကုန်ရှင်း', 'description' => null, 'default' => null],
        ['name' => 'Paid Delivery & Unpaid Bus fee', 'name_mm' => 'ဒလီရှင်း ကုန်ကြွေး', 'description' => null, 'default' => null],
        ['name' => 'Paid Delivery & Paid Bus fee', 'name_mm' => 'ဒလီရှင်း ကုန်ရှင်း', 'description' => null, 'default' => null],
        ['name' => 'Prepaid NTC', 'name_mm' => 'ကြိုရှင်း ငွေမကောက်ရပါ', 'description' => null, 'default' => null],
        ['name' => 'Prepaid Collect', 'name_mm' => 'ကြိုရှင်း ငွေကောက်ရမည်', 'description' => null, 'default' => null]
    ];

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        // PaymentType::truncate();
        // DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        Schema::disableForeignKeyConstraints();
        foreach ($this->paymentTypes as $payment_type) {
            factory(PaymentType::class)->create([
                'name' => $payment_type["name"],
                'name_mm' => $payment_type["name_mm"],
                'description' => $payment_type["description"],
                'default' => $payment_type["default"]
            ]);
        }
        Schema::enableForeignKeyConstraints();
    }
}
