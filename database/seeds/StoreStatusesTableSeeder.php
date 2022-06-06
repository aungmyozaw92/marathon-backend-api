<?php

use App\Models\StoreStatus;
use Illuminate\Database\Seeder;

class StoreStatusesTableSeeder extends Seeder
{
    protected $status = [
        ['Waiting', 'စောင့်ဆိုင်း', 'haven\'t received parcel', 0],
        ['Received', 'လက်ခံရရှိ', 'parcel receive to marathon branch'],
        ['Warehousing', 'သိုလှောင်', 'parcel like to stay here for storing purpose', 200],
        ['Pending', 'ဆိုင်းငံ့', 'parcel will go out in next order', 0],
        ['Assigned ', 'တာဝန်ပေးပြီး', 'parcel already put into deli or waybill', 0],
        ['Delaying', 'ကြန့်ကြာ', 'parcel waiting due to some error', 0],
        ['Settle', 'ဖြေရှင်းပြီး', 'parcel already delivered to customer', 0],
        ['Postpone', 'ရွှေ့ဆိုင်း', 'parcel will wait for one more night within city', 0],
        ['Return', 'ပြန်ပို့ရန်', 'parcel send back to merchant', 0],
    ];

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        // StoreStatus::truncate();
        // DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        Schema::disableForeignKeyConstraints();
        foreach ($this->status as $status) {
            factory(StoreStatus::class)->create([
                'status'    => $status[0],
                'status_mm' => $status[1]

            ]);
        }
        Schema::enableForeignKeyConstraints();
    }
}
