<?php

use Illuminate\Database\Seeder;
use App\Models\Bus;

class BusesTableSeeder extends Seeder
{
    protected $companies = [
        ['name' => 'ရွှေတွင်းကြီး'],
        ['name' => 'အောင်လံ-ဒဂုံ'],
        ['name' => 'မျိုးဆက်သစ်'],
        ['name' => 'အာရှ'],
        ['name' => 'အေးချစ်မြိုင်'],
        ['name' => 'နယူးအောင်ကျော်မိုး'],
        ['name' => 'ဝင်းသစ္စာ'],
        ['name' => 'အောင်ဇေယျ'],
        ['name' => 'ရွှေသလ္လာ'],
        ['name' => 'မြို့သစ်-ဒဂုံ'],
        
        ['name' => 'အောင်နိုင်မန်း'],
        ['name' => 'မာန်သစ္စာ'],
        ['name' => 'ရိုးမသစ္စာ'],
        ['name' => 'ကြံတိုင်းအောင်'],
        ['name' => 'တိုးရတနာ'],
        ['name' => 'ရွှေပြည်မင်း'],
        ['name' => 'ရေဦးအောင်ရတနာ'],
        ['name' => 'ဇေယျာမြိုင်ကြီး'],
        ['name' => 'ရွှေမန်းရတနာ'],
        ['name' => 'အေးချမ်းဖြိုး'],
        
        ['name' => 'နယူးစိန်ဌေးလှိုင်'],
        ['name' => 'ပြည့်ဖြိုးအောင်'],
        ['name' => 'အောင်သစ္စာ'],
        ['name' => 'မြရတနာ'],
        ['name' => 'မိုးကောင်းကင်'],
        ['name' => 'တောင်ပြာတန်း'],
        ['name' => 'ရွှေကမ္ဘာ'],
        ['name' => 'မန်းသစ္စာ'],
        ['name' => 'သိန်းသန်းကုဋေ'],
        ['name' => 'ESE'],
        
        ['name' => 'ဒို့ညီနောင်'],
        ['name' => 'နယူးမန္တလာထွန်း'],
        ['name' => 'စိန်ကမ္ဘာ'],
        ['name' => 'မဟာ'],
        ['name' => 'ရွှေစကြ်ာ 1'],
        ['name' => 'မိုးထက်အာကာ'],
        ['name' => 'ရွှေတောင်ရိုး'],
        ['name' => 'တက်လမ်း'],
        ['name' => 'ရွှေကျောက်ခဲ'],
        ['name' => 'မြတ်မန္တလာထွန်း'],
        
        ['name' => 'ရွှေလားရှိုး'],
        ['name' => 'အောင်ကျော်မိုး'],
        ['name' => 'အကယ်ဒမီ'],
        ['name' => 'လုမ္ဗနီ'],
        ['name' => 'ရွှေမန္တလာ'],
        ['name' => 'Famous'],
        ['name' => 'မြသရဖူ'],
        ['name' => 'Elite'],
        ['name' => 'ရွှေမန္တလေး'],
        ['name' => 'အန်တီဝင်း'],
        
        ['name' => 'ဘုန်းမြတ်ပိုင်'],
        ['name' => 'ပုပ္ပါး/ပုဂံ'],
        ['name' => 'မန်းရာဇာ'],
        ['name' => 'ရွှေစကြ်ာ 2'],
        ['name' => 'ရိုးရိုးလေး'],
        ['name' => 'မန္တလာမင်း'],
        ['name' => 'Highclass'],
        ['name' => 'ရွှေဖားစည်'],
        ['name' => 'ဂျူပီတာ'],
        ['name' => 'သာမည'],
        
        ['name' => 'ရွှေခြင်္သေ့'],
        ['name' => 'အဆွေတော်'],
        ['name' => 'ရွှေအာကာ'],
        ['name' => 'မနောတံခွန်'],
        ['name' => 'ရိုးမ'],
        ['name' => 'နှင်းချယ်ရီ'],
        ['name' => 'ဝင်းရတနာ'],
        ['name' => 'ဝင်းရတနာ1'],
        ['name' => 'နေလမင်း'],
        ['name' => 'နယူးအောင်တံခွန်'],
        
        ['name' => 'တင့်တိုင်းအောင်'],
        ['name' => 'ရိုးမမန္တလာ'],
        ['name' => 'ရွှေလွန်းပျံ'],
        ['name' => 'ရွှေမန်းသူ'],
        ['name' => 'အောင်ရတနာ'],
        ['name' => 'ပုလော'],
        ['name' => 'ကော့သောင်း'],
        ['name' => 'မြတ်ပြည့်စုံ'],
        ['name' => 'စိန်တလုံး'],
        ['name' => 'ရွှေကချင်'],
        
        ['name' => 'လေးဝတီ'],
        ['name' => 'စွမ်း Group'],
        ['name' => 'သိန်းသန်းကျော်'],
        ['name' => 'ပြည့်ဝ'],
        ['name' => 'အောင်ကျော်ဇော'],
        ['name' => 'ဇေယျရွှေပြည်'],
        ['name' => 'ရွှင်လန်း'],
        ['name' => 'ပွင့်သစ်ရတနာ'],
        ['name' => 'ရောင်နီထွန်း'],
        ['name' => 'သိဒ္ဓိအောင်'],
        
        ['name' => 'သိန်းသန်းကျော်2'],
        ['name' => 'ရတနာ'],
        ['name' => 'သစ္စာဦး'],
        ['name' => 'ကြယ်စင်ဟိန်း'],
        ['name' => 'ရွှေစင်ရတနာ'],
        ['name' => 'ရွှေနတ်တောင်'],
        ['name' => 'အောင်စိုးမိုး'],
        ['name' => 'ကရဝိတ်'],
        ['name' => 'နယူးရွှေလီ'],
        ['name' => 'ရွှေပုလဲသီ'],
        
        ['name' => 'ဓူဝံ'],
        ['name' => 'ရွှေမြင်းပျံ'],
        ['name' => 'ရွှေလီရတနာ'],
        ['name' => 'ထက်ဦး'],
        ['name' => 'အောဇမ္ဗူ'],
        ['name' => 'ကနောင်မင်းသား'],
        ['name' => 'ရဲအောင်လံ'],
        ['name' => 'မင်းထက်သာ'],
        ['name' => 'အောင်ပြည့်စုံ'],
        ['name' => 'ရွှေပြည်သစ်'],
        
        ['name' => 'ရွှေမန်းသူ1'],
        
    ];
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        // Bus::truncate();
        // DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        Schema::disableForeignKeyConstraints();
        foreach ($this->companies as $bus) {
            factory(Bus::class)->create([
                'name' => $bus["name"]
            ]);
        }
        Schema::enableForeignKeyConstraints();
    }
}
