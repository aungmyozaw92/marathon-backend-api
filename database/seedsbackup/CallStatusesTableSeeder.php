<?php

use App\Models\CallStatus;
use Illuminate\Database\Seeder;
use App\Traits\TruncateTableSeeder;

class CallStatusesTableSeeder extends Seeder
{
    use TruncateTableSeeder;

    protected $status = [
        'Haven\'t call' => 'ဖုန်းမခေါ်ဆိုရသေးပါ',
        'Can\'t call (Wrong number)' => 'ဖုန်းခေါ်ဆို၍မရပါ(ဖုန်းနံပါတ်မှားယွင်း)',
        'Can\'t call (Power off)' => 'ဖုန်းခေါ်ဆို၍မရပါ(ပါဝါပိတ်ထား)',
        'Can call (don\'t pickup)' => 'ဖုန်းခေါ်ဆို၍ရပါသည်(ဖုန်းမကိုင်ပါ)',
        'Can call (out of service)' => 'ဖုန်းခေါ်ဆို၍ရပါသည်(ဆက်သွယ်မှုဧရိယာပြင်ပ)',
        'Called (confirmed)' => 'ဖုန်းခေါ်ဆိုပြီးပါပြီ(အတည်ပြုပြီး)',
        'Called (not confirmed)' => 'ဖုန်းခေါ်ဆိုပြီးပါပြီ(အတည်မပြုရသေး)',
        'Can call (busy)' => 'ဖုန်းခေါ်ဆို၍ရပါသည်(ဖုန်းမအားသေးပါ)',
        'Called (recall)' => 'ဖုန်းခေါ်ဆိုပြီးပါပြီ(ဖုန်း​ပြန်ခေါ်ဆိုရန်)',
        'Called (return)' => 'ဖုန်းခေါ်ဆိုပြီးပါပြီ(မယူတော့ပါ)'
    ];
    
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->truncate('call_statuses');

        // DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        // CallStatus::truncate();
        // DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        foreach ($this->status as $status => $status_mm) {
            factory(CallStatus::class)->create([
               'status'    => $status,
               'status_mm' => $status_mm
            ]);
        }
    }
}
