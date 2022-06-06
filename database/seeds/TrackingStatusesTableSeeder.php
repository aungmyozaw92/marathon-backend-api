<?php

use App\Models\TrackingStatus;
use Illuminate\Database\Seeder;

class TrackingStatusesTableSeeder extends Seeder
{
    // protected $status = [
    //     ['info_received', 'Info Received', 'သတင်းအချက်အလက်ရရှိထားပါသည်', 'Carrier has received request from shipper and is about to pick up the shipment.'],
    //     // ['', 'Shipment  Received', 'ပစ္စည်း လက်ခံရရှိပါသည် ', 'Our branch or Agent receive the shipment from carrier.'],
    //     ['in_transit', 'In Transit', 'ပစ္စည်း အကူးအပြောင်းတွင်ရှိနေပါသည်', 'Carrier has accepted or picked up shipment from shipper. The shipment is on the way.'],
    //     ['out_for_delivery', 'Out for Delivery', 'ပစ္စည်း ပေးပို့ရန် ထွက်ခွာသွားပါသည်', 'Carrier is about to deliver the shipment , or it is ready to pickup.'],
    //     ['failed_attempt', 'Failed Attempt ', 'ပို့ဆောင်မှု မအောင်မြင်ခဲ့ပါ ', 'Carrier attempted to deliver but failed, and usually leaves a notice and will try to delivery again.'],
    //     ['delivered', 'Delivered', 'ပို့ဆောင်မှု အောင်မြင်ခဲ့ပါသည်', 'The shipment was delivered successfully.'],
    //     ['returned', 'Returned', 'ပြန်ပို့ လိိုုက်ပါပြီ', 'Returned shipment to sender.'],
    //     ['exception', 'Exception', 'ချွင်းချက်', 'Custom hold, undelivered, returned shipment to sender or any shipping exceptions.'],
    //     ['expired', 'Expired', 'သက်တမ်းကုန်ဆုံးသွားပါပြီ', 'Shipment has no tracking information for 30 days since added.'],
    // ];
    protected $status = [
        ['info_received', 'Info Received', 'သတင်းအချက်အလက်ရရှိထားပါသည်။', 'Carrier has received request from shipper and is about to pick up the shipment.'],
        ['assign_for_pickup', 'Assign for Pickup', 'ပစ္စည်း ကောက်ယူရန် ညွှန်ကြားထားပါသည်။', 'Carrier was assigned to pickup.'],
        ['picked-up_the_parcel', 'Picked up the Parcel', 'ပစ္စည်း ကောက်ယူခဲ့ပါပြီ။', 'Carrier picked-up the parcel.'],
        ['marathon_received_parcel', 'Marathon Received Parcel', 'ပစ္စည်းကို မာရသွန်မှ လက်ခံရရှိပါပြီ။', 'Carrier picked-up the parcel.'],

        ['prepare_for_delivery', 'Prepare for Delivery', 'ပစ္စည်း ပို့ဆောင်ရန် စာရင်းသွင်းလိုက်ပါပြီ။', 'Prepare for Delivery.'],
        ['out_for_delivery', 'Out for Delivery', 'ပစ္စည်း ပေးပို့ရန် ထွက်ခွာသွားပါသည်', 'Carrier is about to deliver the shipment , or it is ready to pickup.'],

        ['delivered', 'Delivered', 'ပို့ဆောင်ပေးခဲ့ပါပြီ။ ', 'The shipment was delivered successfully.'],
        ['delivery_success', 'Delivery Success', 'ပို့ဆောင်မှု အောင်မြင်ခဲ့ကြောင်း အတည်ပြုလိုက်ပါသည်။', 'Delivery Success.'],

        ['failed_attempt', 'Failed Attempt ', 'ပို့ဆောင်မရခဲ့ပါ။', 'Carrier attempted to deliver but failed, and usually leaves a notice and will try to delivery again.'],
        ['delivery_fail', 'Delivery Fail', 'ပို့ဆောင်မှု မအောင်မြင်ခဲ့ကြောင်း အတည်ပြုလိုက်ပါသည်။', 'Delivery Fail.'],

        ['change_to_success', 'Change to Success', 'ပို့ဆောင်မှု အောင်မြင်ခဲ့သည်ဟု ပြောင်းလဲအတည်ပြုပါသည်။', 'Change to Success.'],
        ['change_to_fail', 'Change to Fail', 'ပို့ဆောင်မှု မအောင်မြင်ခဲ့ပါဟု ပြောင်းလဲအတည်ပြုပါသည်။', 'Change to Fail.'],

        ['prepare_for_freight', 'Prepare for Freight', 'ချောထုပ် တင်ပို့ရန် ပြင်ဆင်ပြီးပါပြီ။', 'Prepare freight shipment.'],
        ['on_the_freight', 'On the Freight', 'ချောထုပ် တင်ပို့ပေးလိုက်ပါပြီ။', 'Prepare freight shipment.'],
        ['receive_waybill', 'Received Waybill', 'ချောထုပ် လက်ခံရရှိလိုက်ပါပြီ။', 'Prepare freight shipment.'],

        ['welling_to_return', 'Welling to Return', 'မှာယူသူမှ လက်မခံကြောင်း အတည်ပြုလိုက်ပါသည်။', 'Customer welling to return.'],
        ['request_to_re-deliver', 'Request to Re-deliver', 'မှာယူသူမှ လက်ခံကြောင်း ပြန်လည်အကြောင်းပြန်သည်။', 'Customer welling to return.'],
        ['prepare_to_return', 'Prepare to Return', 'ရောင်းချသူထံ ပြန်ပို့ရန် ပြင်ဆင်ပြီးပါပြီ။', 'Prepare to return.'],
        ['return', 'Return', 'ရောင်းချသူထံ ပြန်ပို့လိုက်ပါပြီ။', 'Parcel was returned.'],
        ['exception', 'Exception', 'ချွင်းချက်', 'Custom hold, undelivered, returned shipment to sender or any shipping exceptions.'],
        ['expired', 'Expired', 'သက်တမ်းကုန်ဆုံးသွားပါပြီ။', 'Shipment has no tracking information for 30 days since added.']
    ];

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        // TrackingStatus::truncate();
        // DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        Schema::disableForeignKeyConstraints();
        foreach ($this->status as $status) {
            factory(TrackingStatus::class)->create([
                'status'    => $status[0],
                'status_en' => $status[1],
                'status_mm' => $status[2],
                'description' => $status[3]
            ]);
        }
        Schema::enableForeignKeyConstraints();
    }
}
