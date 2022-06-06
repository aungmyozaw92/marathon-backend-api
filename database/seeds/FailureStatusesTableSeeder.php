<?php

use Illuminate\Database\Seeder;
use App\Models\FailureStatus;

class FailureStatusesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    protected $statuses = [
        ['category' => 'Customer Issue', 'specification' => 'ပစ္စည်းလက်ခံသူအိမ်တွင်မရှိပါ'],
        ['category' => 'Customer Issue', 'specification' => 'ပစ္စည်းလက်ခံသူတွင်ငွေမရှိပါ'],
        ['category' => 'Customer Issue', 'specification' => 'ပစ္စည်းလက်ခံသူကိုဆက်သွယ်၍မရပါ(ဖုန်းပိတ်ထား၊ အိမ်တံခါးပိတ်ထား၊ လိပ်စာမှားယွင်း)'],
        ['category' => 'Customer Issue', 'specification' => 'ပစ္စည်းလက်ခံသူကရက်ရွေ့ခိုင်းနေပါသည်'],
        ['category' => 'Customer Issue', 'specification' => 'ပစ္စည်းလက်ခံသူကိုယ်စားမှာယူထားသည်'],
        ['category' => 'Customer Issue', 'specification' => 'ပစ္စည်းလက်ခံသူမှ မမှာထားဟုငြင်းဆို (မှားယွင်းမှာကြားမှု)'],
        ['category' => 'Customer Issue', 'specification' => 'ပစ္စည်းလက်ခံသူမှဖွင့်ဖောက်စစ်ဆေးခိုင်းနေပါသည်'],
        ['category' => 'Customer Issue', 'specification' => 'ပစ္စည်းလက်ခံသူ ဈေးစစ်(ဈေးလျော့ခိုင်း)'],
        ['category' => 'Delivery Issue', 'specification' => 'ပစ္စည်းပို့ဆောင်သူ ယာဉ် ချို့ယွင်း၊ ပျက်စီး၊ ပျောက်ဆုံး'],
        ['category' => 'Delivery Issue', 'specification' => 'ဆိုးရွားစွာ လမ်းပိတ်၊ လမ်းပျက်စီး၊ မီးလောင်၊ သွားလာရခက်ခဲ'],
        ['category' => 'Delivery Issue', 'specification' => 'ပစ္စည်းပို့ဆောင်သူ ထွက်ပြေး'],
        ['category' => 'Delivery Issue', 'specification' => 'ပစ္စည်းပို့ဆောင်သူ ငွေပိုတောင်း'],
        ['category' => 'Delivery Issue', 'specification' => 'ပစ္စည်းပို့ဆောင်သူ အချိန်မှီအရောက်မပို့နိုင်'],
        ['category' => 'Delivery Issue', 'specification' => 'ပစ္စည်းပို့ဆောင်သူ မတော်တဆမှုဖြစ်ပွား'],
        ['category' => 'Delivery Issue', 'specification' => 'ပစ္စည်းပို့ဆောင်သူ ဝန်များနေ'],
        ['category' => 'Operation Issue', 'specification' => 'ပါဆယ်အထုတ် ပျောက်ဆုံး'],
        ['category' => 'Operation Issue', 'specification' => 'ပါဆယ်အထုတ် အထုတ်မှား'],
        ['category' => 'Operation Issue', 'specification' => 'မှားယွင်း၍တာဝန်ပေး(မြို့နယ်မှား၊ လူမှား)'],
        ['category' => 'Other Issue', 'specification' => 'ဝန်ဆောင်မှုမရသည့် ဧရိယာ'],
        ['category' => 'Other Issue', 'specification' => 'ပစ္စည်းပို့ဆောင်သူ သဘာဝဘေးအန္တရယ်ကြုံတွေ့နေပါသည်'],
        ['category' => 'Other Issue', 'specification' => 'သက်ဆိုင်ရာအဖွဲ့အစည်းမှ သိမ်းဆည်း'],
        ['category' => 'Merchant Issue', 'specification' => 'ကောက်ယူရမည့် ပစ္စည်းတန်ဖိုးမှားယွင်း'],
        ['category' => 'System Issue', 'specification' => 'ဘောက်ချာနံပါတ်မှားယွင်း(မထင်မရှား)'],
        ['category' => 'System Issue', 'specification' => 'QR ဖတ်၍မရ'],
        ['category' => 'Product Issue', 'specification' => 'ပါဆယ်အထုတ် ပျက်စီး'],
    ];
    public function run()
    {
        Schema::disableForeignKeyConstraints();

        foreach ($this->statuses as $result) {
            factory(FailureStatus::class)->create([
                'category'           => $result['category'],
                'specification'     => $result['specification']
            ]);
        }
        Schema::enableForeignKeyConstraints();
    }
}
