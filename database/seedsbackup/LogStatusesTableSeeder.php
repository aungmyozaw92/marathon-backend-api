<?php

use Illuminate\Database\Seeder;
use App\Models\LogStatus;
use App\Traits\TruncateTableSeeder;

class LogStatusesTableSeeder extends Seeder
{
    use TruncateTableSeeder;
    
    protected $statuses = [
        'delivery' => [
            'en' => 'changed delivering status',
            'mm' => 'မှ ပေးပို့သောဒေတာအား ပြောင်းလဲသွား'
        ],
        'call' => [
            'en' => 'changed calling status',
            'mm' => 'မှ ခေါ်ဆိုခြင်းဒေတာအား ပြောင်းလဲသွား'
        ],
        'store' => [
            'en' => 'changed storing status',
            'mm' => 'မှ သိုလှောင်ခြင်းဒေတာအား ပြောင်းလဲသွား'
        ],
        'sender_address' =>  [
            'en' => 'changed address field',
            'mm' =>'မှ လိပ်စာအား ပြောင်းလဲသွား'
        ],
        'sender_phone' => [
            'en' => 'changed phone field',
            'mm' => 'မှ ဖုန်းအား ပြောင်းလဲသွား'
        ],
        'sender_name' => [
            'en' => 'changed name field',
            'mm' => 'မှ အမည်အား ပြောင်းလဲသွား'
        ],
        'new_pickup' => [
            'en' => 'add new pickup',
            'mm' => 'add new pickup'
        ],
        'new_voucher' => [
            'en' => 'add new voucher',
            'mm' => 'add new voucher'
        ],
        'new_dropoff' => [
            'en' => 'add new',
            'mm' => 'add new'
        ],
        'opened_by' => [
            'en' => 'changed opened by field on Pickup',
            'mm' => 'changed opened by field on Pickup'
        ],
        'note' => [
            'en' => 'changed note by field on Pickup',
            'mm' => 'changed note by field on Pickup'
        ],
        'sender_type' => [
            'en' => 'changed sender_type by field on Pickup',
            'mm' => 'changed sender_type by field on Pickup'
        ],
        'sender_id' => [
            'en' => 'changed sender_id by field on Pickup',
            'mm' => 'changed sender_id by field on Pickup'
        ],
        'sender_associate_id' => [
            'en' => 'changed sender_associate_id by field on Pickup',
            'mm' => 'changed sender_associate_id by field on Pickup'
        ],
        'sender_note' => [
            'en' => 'changed note by field on pickup(sender)',
            'mm' => 'မှ ပစ်ကပ် မှတ်ချက် ပြောင်းလဲသွားတယ်'
        ],
        'receiver_name' => [
            'en' => 'changed name field from',
            'mm' => 'မှ ပေးပို့သူ အမည်အား ပြောင်းလဲသွား'
        ],
        'receiver_address' => [
            'en' => 'changed address field from',
            'mm' => 'မှ ပေးပို့သူ လိပ်စာအား ပြောင်းလဲသွား'
        ],
        'receiver_phone' => [
            'en' => 'changed phone field from',
            'mm' => 'မှ ပေးပို့သူ ဖုန်းအား ပြောင်းလဲသွား'
        ],
        'receiver_note' => [
            'en' => 'changed note by field on pickup(receiver)',
            'mm' => 'မှ ပစ်ကပ် မှတ်ချက် ပြောင်းလဲသွားတယ်'
        ],
        'pickup_fee' => [
            'en' => 'changed pickup fee on pickup',
            'mm' => 'မှ ပစ်ကပ် ကြေးပြောင်းလဲသွားတယ်'
        ],
        'from_city' => [
            'en' => 'changed from city field from',
            'mm' => 'changed from city field from'
        ],
        'to_city' => [
            'en' => 'changed to city field from',
            'mm' => 'changed to city field from'
        ],
        'from_zone' => [
            'en' => 'changed from zone field from',
            'mm' => 'changed from zone field from'
        ],
        'to_zone' => [
            'en' => 'changed to zone field from',
            'mm' => 'changed to zone field from'
        ],
        'from_gate' => [
            'en' => 'changed from gate field from',
            'mm' => 'changed from gate field from'
        ],
        'to_gate' => [
            'en' => 'changed to gate field from',
            'mm' => 'changed to gate field from'
        ],
        'from_bus_station ' => [
            'en' => 'changed from bus station field from',
            'mm' => 'changed from bus station field from'
        ],
        'to_bus_station ' => [
            'en' => 'changed to bus station field from',
            'mm' => 'changed to bus station field from'
        ],
        'parcel_creation' => [
            'en' => 'create parcel price',
            'mm' => ' မှ အထုပ် ဖန်တီး'
        ],
        'parcel_deletion' => [
            'en' => ' delete parcel price',
            'mm' => 'မှ အထုပ် ဖျက်စီး'
        ],
        'item_creation' => [
            'en' => 'create item price',
            'mm' => 'မှ ကုန်ပစ္စည်း ဖန်တီး'
        ],
        'item_deletion' => [
            'en' => 'delete item price',
            'mm' => 'မှ ကုန်ပစ္စည်း ဖျက်စီး'
        ],
        'item_name' => [
            'en' => 'change item name',
            'mm' => 'မှ ကုန်ပစ္စည်း ဖျက်စီး'
        ],
        'item_qty' => [
            'en' => 'change item qty',
            'mm' => 'မှ ကုန်ပစ္စည်း ဖျက်စီး'
        ],
        'item_price ' => [
            'en' => 'change item price',
            'mm' => 'မှ ကုန်ပစ္စည်း ဖျက်စီး'
        ],

        'from_zone' => [
            'en'=>'changed from zone field from',
             'mm'=>'မှ ပို့သူဘက် ဇုန်အား ပြောင်းလဲသွား'
            ],
        'to_zone' => [
            'en'=>'changed to zone field from',
             'mm'=>'မှ လက်ခံသူဘက် ဇုန်အား ပြောင်းလဲသွား'
            ],
        'weight' => ['en'=> 'change weight field from ', 'mm'=> 'မှ အလေးချိန်စျေးနှုန်းအား ပြောင်းလဲသွား'],
        'global_scale' => ['en'=>'changed global', 'mm'=>'မှ ပို့သူဘက် ဂိတ်အား ပြောင်းလဲသွား'],
        'coupon_price' => ['en'=>'changed coupon price', 'mm'=>'မှ လက်ခံသူဘက် ဂိတ်အား ပြောင်းလဲသွား'],
        'postpone_date' => ['en'=>'changed postpone date', 'mm'=> 'ရက်စွဲကိုရွှေ့ဆိုင်းပြောင်းလဲသွားတယ်'],
        'transaction_fee' => ['en'=>'changed transaction fee from', 'mm'=>'မှ ပို့သူဘက် ဂိတ်အား ပြောင်းလဲသွား'],
        'insurance_fee' => ['en'=>'changed insurance fee from', 'mm'=>'မှ လက်ခံသူဘက် ဂိတ်အား ပြောင်းလဲသွား'],
        'warehousing_fee' => ['en'=>'changed warehousing fee from', 'mm'=> 'ရက်စွဲကိုရွှေ့ဆိုင်းပြောင်းလဲသွားတယ်'],
    ];

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        // LogStatus::truncate();
        // DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $this->truncate('log_statuses');
        
        foreach ($this->statuses as $value => $description) {
            factory(LogStatus::class)->create([
               'value'           => $value,
               'description'     => $description["en"],
               'description_mm'  => $description["mm"]
            ]);
        }
    }
}
