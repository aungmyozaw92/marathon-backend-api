<?php

use App\Models\City;
use Illuminate\Database\Seeder;
use App\Traits\TruncateTableSeeder;

class CitiesTableSeeder extends Seeder
{
    use TruncateTableSeeder;

    protected $cities = [
        ['name'=>'Yangon', 'name_mm'=>'ရန်ကုန်', 'is_collect_only'=> 0,'is_on_demand'=>0, 'is_available_d2d'=>1],
        ['name'=>'Thanlyin', 'name_mm'=>'သန်လျင်', 'is_collect_only'=> 0,'is_on_demand'=>0, 'is_available_d2d'=>1],
        ['name'=>'Thonse', 'name_mm'=>'သုံးဆယ်', 'is_collect_only'=> 0,'is_on_demand'=>0, 'is_available_d2d'=>1],
        ['name'=>'Letpadan', 'name_mm'=>'လက်ပံတန်း', 'is_collect_only'=> 0,'is_on_demand'=>0, 'is_available_d2d'=>1],
        ['name'=>'Othegon', 'name_mm'=>'အိုးသည်ကုန်း', 'is_collect_only'=> 0,'is_on_demand'=>0, 'is_available_d2d'=>1],
        ['name'=>'Min Hla', 'name_mm'=>'မင်းလှ', 'is_collect_only'=> 0,'is_on_demand'=>0, 'is_available_d2d'=>1],
        ['name'=>'Paungde', 'name_mm'=>'ပေါင်းတည်', 'is_collect_only'=> 0,'is_on_demand'=>0, 'is_available_d2d'=>1],
        ['name'=>'Nattalin', 'name_mm'=>'နတ်တလင်း', 'is_collect_only'=> 0,'is_on_demand'=>0, 'is_available_d2d'=>1],
        ['name'=>'Pyay', 'name_mm'=>'ပြည်', 'is_collect_only'=> 0,'is_on_demand'=>0, 'is_available_d2d'=>1],
        ['name'=>'Aunglan', 'name_mm'=>'အောင်လံ', 'is_collect_only'=> 0,'is_on_demand'=>0, 'is_available_d2d'=>1],
        ['name'=>'Sin Phyu Kyun', 'name_mm'=>'ဆင်ဖြူကျွန်း', 'is_collect_only'=> 0,'is_on_demand'=>0, 'is_available_d2d'=>1],
        ['name'=>'Magway', 'name_mm'=>'မကွေး', 'is_collect_only'=> 0,'is_on_demand'=>0, 'is_available_d2d'=>1],
        ['name'=>'Taungdwingyi', 'name_mm'=>'တောင်တွင်းကြီး', 'is_collect_only'=> 0,'is_on_demand'=>0, 'is_available_d2d'=>1],
        ['name'=>'Kyaukpadaung', 'name_mm'=>'ကျောက်ပန်းတောင်း', 'is_collect_only'=> 0,'is_on_demand'=>0, 'is_available_d2d'=>1],
        ['name'=>'Nyaung-U', 'name_mm'=>'ညောင်ဦး', 'is_collect_only'=> 0,'is_on_demand'=>0, 'is_available_d2d'=>1],
        ['name'=>'Pakokku', 'name_mm'=>'ပခုက္ကူ', 'is_collect_only'=> 0,'is_on_demand'=>0, 'is_available_d2d'=>1],
        ['name'=>'Myit Chay', 'name_mm'=>'မြစ်ခြေ', 'is_collect_only'=> 0,'is_on_demand'=>0, 'is_available_d2d'=>1],
        ['name'=>'Kamma', 'name_mm'=>'ကမ္မ', 'is_collect_only'=> 0,'is_on_demand'=>0, 'is_available_d2d'=>1],
        ['name'=>'Yesagyo', 'name_mm'=>'ရေစကြို', 'is_collect_only'=> 0,'is_on_demand'=>0, 'is_available_d2d'=>1],
        ['name'=>'Pauk', 'name_mm'=>'ပေါက်', 'is_collect_only'=> 0,'is_on_demand'=>0, 'is_available_d2d'=>1],
        ['name'=>'Myinmu', 'name_mm'=>'မြင်းမူ', 'is_collect_only'=> 0,'is_on_demand'=>0, 'is_available_d2d'=>1],
        ['name'=>'Chaung-U', 'name_mm'=>'ချောင်းဦး', 'is_collect_only'=> 0,'is_on_demand'=>0, 'is_available_d2d'=>1],
        ['name'=>'Monywa', 'name_mm'=>'မုံရွာ', 'is_collect_only'=> 0,'is_on_demand'=>0, 'is_available_d2d'=>1],
        ['name'=>'Ye-U', 'name_mm'=>'ရေဦး', 'is_collect_only'=> 0,'is_on_demand'=>0, 'is_available_d2d'=>1],
        ['name'=>'Khin-U', 'name_mm'=>'ခင်ဦး', 'is_collect_only'=> 0,'is_on_demand'=>0, 'is_available_d2d'=>1],
        ['name'=>'Gangaw', 'name_mm'=>'ဂန့်ဂေါ', 'is_collect_only'=> 0,'is_on_demand'=>0, 'is_available_d2d'=>1],
        ['name'=>'Shwe Bo', 'name_mm'=>'ရွှေဘို', 'is_collect_only'=> 0,'is_on_demand'=>0, 'is_available_d2d'=>1],
        ['name'=>'Sagaing', 'name_mm'=>'စစ်ကိုင်း', 'is_collect_only'=> 0,'is_on_demand'=>0, 'is_available_d2d'=>1],
        ['name'=>'Mandalay', 'name_mm'=>'မန္တလေး', 'is_collect_only'=> 0,'is_on_demand'=>0, 'is_available_d2d'=>1],
        ['name'=>'Pyin Oo Lwin1', 'name_mm'=>'ပြင်ဦးလွင်1', 'is_collect_only'=> 0,'is_on_demand'=>0, 'is_available_d2d'=>1],
        ['name'=>'Kyaukse', 'name_mm'=>'ကျောက်ဆည်', 'is_collect_only'=> 0,'is_on_demand'=>0, 'is_available_d2d'=>1],
        ['name'=>'Myingyan', 'name_mm'=>'မြင်းခြံ', 'is_collect_only'=> 0,'is_on_demand'=>0, 'is_available_d2d'=>1],
        ['name'=>'Taung Thar', 'name_mm'=>'တောင်သာ', 'is_collect_only'=> 0,'is_on_demand'=>0, 'is_available_d2d'=>1],
        ['name'=>'Natogyi', 'name_mm'=>'နွားထိုးကြီး', 'is_collect_only'=> 0,'is_on_demand'=>0, 'is_available_d2d'=>1],
        ['name'=>'Meiktila', 'name_mm'=>'မိတ္ထီလာ', 'is_collect_only'=> 0,'is_on_demand'=>0, 'is_available_d2d'=>1],
        ['name'=>'Mogok', 'name_mm'=>'မိုးကုတ်', 'is_collect_only'=> 0,'is_on_demand'=>0, 'is_available_d2d'=>1],
        ['name'=>'Pyawbwe', 'name_mm'=>'ပျော်ဖွယ်', 'is_collect_only'=> 0,'is_on_demand'=>0, 'is_available_d2d'=>1],
        ['name'=>'Yamethin ', 'name_mm'=>'ရမည်းသင်း', 'is_collect_only'=> 0,'is_on_demand'=>0, 'is_available_d2d'=>1],
        ['name'=>'Tatkon', 'name_mm'=>'တပ်ကုန်း', 'is_collect_only'=> 0,'is_on_demand'=>0, 'is_available_d2d'=>1],
        ['name'=>'Pyinmana', 'name_mm'=>'ပျဉ်းမနား ', 'is_collect_only'=> 0,'is_on_demand'=>0, 'is_available_d2d'=>1],
        ['name'=>'Naypyitaw', 'name_mm'=>'နေပြည်တော်', 'is_collect_only'=> 0,'is_on_demand'=>0, 'is_available_d2d'=>1],
        ['name'=>'Lewe', 'name_mm'=>'လယ်ဝေး', 'is_collect_only'=> 0,'is_on_demand'=>0, 'is_available_d2d'=>1],
        ['name'=>'Thar Wut Hti', 'name_mm'=>'သာဝတ္ထီ', 'is_collect_only'=> 0,'is_on_demand'=>0, 'is_available_d2d'=>1],
        ['name'=>'Thar Ga Ya', 'name_mm'=>'သာဂရ', 'is_collect_only'=> 0,'is_on_demand'=>0, 'is_available_d2d'=>1],
        ['name'=>'Yae Ni', 'name_mm'=>'ရေနီ', 'is_collect_only'=> 0,'is_on_demand'=>0, 'is_available_d2d'=>1],
        ['name'=>'Swar', 'name_mm'=>'ဆွာ', 'is_collect_only'=> 0,'is_on_demand'=>0, 'is_available_d2d'=>1],
        ['name'=>'Yedashe', 'name_mm'=>'ရေတာရှည်', 'is_collect_only'=> 0,'is_on_demand'=>0, 'is_available_d2d'=>1],
        ['name'=>'Taungoo', 'name_mm'=>'တောင်ငူ', 'is_collect_only'=> 0,'is_on_demand'=>0, 'is_available_d2d'=>1],
        ['name'=>'Oktwin', 'name_mm'=>'အုတ်တွင်း', 'is_collect_only'=> 0,'is_on_demand'=>0, 'is_available_d2d'=>1],
        ['name'=>'Zay Ya Wa Di', 'name_mm'=>'ဇေယျဝတီ', 'is_collect_only'=> 0,'is_on_demand'=>0, 'is_available_d2d'=>1],
        ['name'=>'Ka Nyut Kwin', 'name_mm'=>'ကညွတ်ကွင်း', 'is_collect_only'=> 0,'is_on_demand'=>0, 'is_available_d2d'=>1],
        ['name'=>'Penwegon', 'name_mm'=>'ပဲနွယ်ကုန်း', 'is_collect_only'=> 0,'is_on_demand'=>0, 'is_available_d2d'=>1],
        ['name'=>'Nyaung Lay Pin', 'name_mm'=>'ညောင်လေးပင်', 'is_collect_only'=> 0,'is_on_demand'=>0, 'is_available_d2d'=>1],
        ['name'=>'Bago', 'name_mm'=>'ပဲခူး', 'is_collect_only'=> 0,'is_on_demand'=>0, 'is_available_d2d'=>1],
        ['name'=>'Waw', 'name_mm'=>'ဝေါ', 'is_collect_only'=> 0,'is_on_demand'=>0, 'is_available_d2d'=>1],
        ['name'=>'Bilin', 'name_mm'=>'ဘီးလင်း', 'is_collect_only'=> 0,'is_on_demand'=>0, 'is_available_d2d'=>1],
        ['name'=>'Hpa-an', 'name_mm'=>'ဘားအံ', 'is_collect_only'=> 0,'is_on_demand'=>0, 'is_available_d2d'=>1],
        ['name'=>'Mawlamyine', 'name_mm'=>'မော်လမြိုင်', 'is_collect_only'=> 0,'is_on_demand'=>0, 'is_available_d2d'=>1],
        ['name'=>'Sittwe', 'name_mm'=>'စစ်တွေ', 'is_collect_only'=> 0,'is_on_demand'=>0, 'is_available_d2d'=>1],
        ['name'=>'Taunggyi', 'name_mm'=>'တောင်ကြီး', 'is_collect_only'=> 0,'is_on_demand'=>0, 'is_available_d2d'=>1],
        ['name'=>'Nyaung Shwe', 'name_mm'=>'ညောင်ရွှေ', 'is_collect_only'=> 0,'is_on_demand'=>0, 'is_available_d2d'=>1],
        ['name'=>'Aungpan', 'name_mm'=>'အောင်ပန်း', 'is_collect_only'=> 0,'is_on_demand'=>0, 'is_available_d2d'=>1],
        ['name'=>'Pathein', 'name_mm'=>'ပုသိမ်', 'is_collect_only'=> 0,'is_on_demand'=>0, 'is_available_d2d'=>1],
        ['name'=>'Danubyu', 'name_mm'=>'ဓနုဖြူ', 'is_collect_only'=> 0,'is_on_demand'=>0, 'is_available_d2d'=>1],
        ['name'=>'Hinthada', 'name_mm'=>'ဟင်္သာတ', 'is_collect_only'=> 0,'is_on_demand'=>0, 'is_available_d2d'=>1],
        ['name'=>'Kyonpyaw', 'name_mm'=>'ကျုံပျော်', 'is_collect_only'=> 0,'is_on_demand'=>0, 'is_available_d2d'=>1],
        ['name'=>'Kale', 'name_mm'=>'ကလေး', 'is_collect_only'=> 0,'is_on_demand'=>0, 'is_available_d2d'=>1],
        ['name'=>'Tachileik', 'name_mm'=>'တာချီလိတ်', 'is_collect_only'=> 0,'is_on_demand'=>0, 'is_available_d2d'=>1],
        ['name'=>'Kengtung', 'name_mm'=>'ကျိုင်းတုံ', 'is_collect_only'=> 0,'is_on_demand'=>0, 'is_available_d2d'=>1],
        ['name'=>'Pyawbwe1', 'name_mm'=>'ပျော်ဘွယ်', 'is_collect_only'=> 0,'is_on_demand'=>0, 'is_available_d2d'=>1],
        ['name'=>'Pyin Oo Lwin', 'name_mm'=>'ပြင်ဦးလွင်‌', 'is_collect_only'=> 0,'is_on_demand'=>0, 'is_available_d2d'=>1],
    ];
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        // City::truncate();
        // DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        $this->truncate('cities');

        foreach ($this->cities as $city) {
            factory(City::class)->create([
                'name' => $city["name"],
                'name_mm' => $city[ "name_mm"],
                'is_collect_only' => $city["is_collect_only"],
                'is_on_demand' => $city["is_on_demand"],
                'is_available_d2d' => $city["is_available_d2d"]
            ]);
        }
        
        // Schema::disableForeignKeyConstraints();
        // factory(City::class, 60)->create();
        // Schema::enableForeignKeyConstraints();
    }
}
