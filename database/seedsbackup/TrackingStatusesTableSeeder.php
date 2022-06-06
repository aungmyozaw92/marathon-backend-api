<?php

use App\Models\TrackingStatus;
use Illuminate\Database\Seeder;
use App\Traits\TruncateTableSeeder;

class TrackingStatusesTableSeeder extends Seeder
{
    use TruncateTableSeeder;
    
    protected $status = [
        [ 'Info Received' , 'သတင်းအချက်အလက်ရရှိထားပါသည်', 'Carrier has received request from shipper and is about to pick up the shipment.' ],
        [ 'Shipment  Received  ', 'ပစ္စည်း လက်ခံရရှိပါသည် ', 'Our branch or Agent receive the shipment from carrier.' ],
        [ 'In Transit', 'ပစ္စည်း အကူးအပြောင်းတွင်ရှိနေပါသည်', 'Carrier has accepted or picked up shipment from shipper. The shipment is on the way.'],
        [ 'Out for Delivery', 'ပစ္စည်း ပေးပို့ရန် ထွက်ခွာသွားပါသည်', 'Carrier is about to deliver the shipment , or it is ready to pickup.'],
        [ 'Failed Attempt ', 'ပို့ဆောင်မှု မအောင်မြင်ခဲ့ပါ ', 'Carrier attempted to deliver but failed, and usually leaves a notice and will try to delivery again.'],
        [ 'Delivered', 'ပို့ဆောင်မှု အောင်မြင်ခဲ့ပါသည်', 'The shipment was delivered successfully.'],
        [ 'Returned' , 'ပြန်ပို့ လိိုုက်ပါပြီ', 'Returned shipment to sender.'],
        [ 'Exception', 'ချွင်းချက်', 'Custom hold, undelivered, returned shipment to sender or any shipping exceptions.'],
        [ 'Expired', 'သက်တမ်းကုန်ဆုံးသွားပါပြီ', 'Shipment has no tracking information for 30 days since added.'],
    ];
    
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->truncate('tracking_statuses');

        foreach ($this->status as $status) {
            factory(TrackingStatus::class)->create([
               'status'    => $status[0],
               'status_mm' => $status[1],
               'description' => $status[2]
            ]);
        }
    }
}
