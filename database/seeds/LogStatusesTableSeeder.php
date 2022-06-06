<?php

use Illuminate\Database\Seeder;
use App\Models\LogStatus;

class LogStatusesTableSeeder extends Seeder
{
    protected $statuses = [
        // 'new_pickup' => ['en' => 'add new pickup', 'mm' => 'ပစ်ကပ်အသစ်တစ်ခု ဖန်တီးခဲ့သည်။'],
        // 'change_sender_type' => ['en' => 'change sender type', 'mm' => 'ပေးပို့သူအမျိုးအစားကို'],
        // 'assign_pickup' => ['en' => 'assign to pickup', 'mm' => 'ပစ်ကပ်ယူသူ ရွေးချယ်ခဲ့သည်။'],
        // 'change_pickup_fee' => ['en' => 'change pickup fee', 'mm' => 'ပစ်ကပ်ခကို'],
        // 'delete_pickup' => ['en' => 'delete pickup', 'mm' => 'ပစ်ကပ်ကို ဖျောက်ဖျက်ခဲ့သည်။'],
        // // end pickup
        // // change sender info from customers or merchants
        // 'change_sender_id' => ['en' => 'change sender', 'mm' => 'ပေးပို့သူကို'],
        // 'change_sender_name' => ['en' => 'change sender name', 'mm' => 'ပေးပို့သူအမည်ကို'],
        // 'change_sender_phone' => ['en' => 'change sender phone', 'mm' => 'ပေးပို့သူဖုန်းနံပါတ်ကို'],
        // 'change_sender_address' => ['en' => 'change sender address', 'mm' => 'ပေးပို့သူလိပ်စာကို'],
        // 'change_from_city' => ['en' => 'change sender city', 'mm' => 'ပေးပို့သူမြို့အမည်ကို'],
        // 'change_from_zone' => ['en' => 'change sender zone', 'mm' => 'ပေးပို့သူဇုန်အမည်ကို'],
        // // end sender
        // 'change_receiver_id' => ['en' => 'change receiver', 'mm' => 'လက်ခံသူကို'],
        // 'change_receiver_name' => ['en' => 'change receiver name', 'mm' => 'လက်ခံသူအမည်ကို'],
        // 'change_receiver_phone' => ['en' => 'change receiver phone', 'mm' => 'လက်ခံသူဖုန်းနံပါတ်ကို'],
        // 'change_receiver_other_phone' => ['en' => 'change receiver other phone', 'mm' => 'လက်ခံသူဒုတိယဖုန်းနံပါတ်ကို'],
        // 'change_receiver_address' => ['en' => 'change receiver address', 'mm' => 'လက်ခံသူလိပ်စာကို'],
        // 'change_to_city' => ['en' => 'change receiver city', 'mm' => 'လက်ခံသူမြို့အမည်ကို'],
        // 'change_to_zone' => ['en' => 'change receiver zone', 'mm' => 'လက်ခံသူဇုံအမည်ကို'],
        // // end receiver
        // 'change_note' => ['en' => 'change note', 'mm' => 'မှတ်ချက်ကို'],
        // // end note
        // 'new_voucher' => ['en' => 'add new voucher', 'mm' => 'ဘောက်ချာအသစ်တစ်ခု ဖန်တီးခဲ့သည်။'],
        // 'change_item_price' => ['en' => 'change item price', 'mm' => 'ပစ္စည်းတန်ဖိုးကို'],
        // 'change_global_scale' => ['en' => 'change item global scale', 'mm' => 'ဂလိုဘယ်(လ်)စကေး(လ်)ကို'],
        // 'change_item_weight' => ['en' => 'change item weight', 'mm' => 'ပစ္စည်းအလေးချိန်ကို'],
        // 'change_payment_type' => ['en' => 'change payment type', 'mm' => 'ငွေပေးချေမှုပုံစံကို'],
        // 'change_call_status' => ['en' => 'change call status', 'mm' => 'ဖုန်းဆက်သွယ်မှုအခြေအနေကို'],
        // 'change_store_status' => ['en' => 'change store status', 'mm' => 'ပစ္စည်းသိမ်းဆည်းမှုအခြေအနေကို'],
        // 'change_delivery_status' => ['en' => 'change delivery status', 'mm' => 'ပို့ဆောင်မှုအခြေအနေကို'],
        // 'new_item' => ['en' => 'add new item', 'mm' => 'ပစ္စည်းအသစ်တစ်ခု ထပ်ထည့်ခဲ့သည်။'],
        // 'delete_item' => ['en' => 'delete item', 'mm' => 'ပစ္စည်းကို ဖျောက်ဖျက်ခဲ့သည်။'],
        // 'change_item_name' => ['en' => 'change item name', 'mm' => 'ပစ္စည်းအမည်ကို'],
        // 'change_item_qty' => ['en' => 'change item qty', 'mm' => 'ပစ္စည်းအရေအတွက်ကို'],
        // 'change_item_price' => ['en' => 'change item price', 'mm' => 'ပစ္စည်းဈေးနှုန်းကို'],
        // 'new_parcel' => ['en' => 'add new parcel', 'mm' => 'ပါဆယ်တစ်ထုပ် ဖန်တီးခဲ့သည်။'],
        // 'new_parcel_item' => ['en' => 'add item to parcel', 'mm' => 'ပစ္စည်းကို ပါဆယ်ထုပ်ထဲထည့်ခဲ့သည်။'],
        // 'delete_parcel' => ['en' => 'delete parcel', 'mm' => 'ပါဆယ်တစ်ထုပ်ကို ဖျောက်ဖျက်ခဲ့သည်။'],
        // 'new_delisheet_voucher' => ['en' => 'add voucher to delisheet', 'mm' => 'ပို့ဆောင်ရန်စာရင်းသွင်းခဲ့သည်။'],
        // 'remove_delisheet_voucher' => ['en' => 'remove voucher from delisheet', 'mm' => 'ပို့ဆောင်ရန်စာရင်းမှ ဖယ်ထုတ်ခဲ့သည်။'],
        // 'new_msf_voucher' => ['en' => 'add voucher to merchant sheet finance', 'mm' => 'ကုန်သည်ငွေစာရင်းရှင်းတမ်းသို့ ထည့်သွင်းခဲ့သည်။'],
        // 'remove_msf_voucher' => ['en' => 'remove voucher from merchant sheet finance', 'mm' => 'ကုန်သည်ငွေစာရင်းရှင်းတမ်းမှ ဖယ်ထုတ်ခဲ့သည်။'],
        // 'new_waybill_voucher' => ['en' => 'add voucher to waybill', 'mm' => 'ဝေးဘေလ်ပို့ရန် စာရင်းသွင်းခဲ့သည်။'],
        // 'remove_waybill_voucher' => ['en' => 'remove voucher from waybill', 'mm' => 'ဝေးဘေလ်စာရင်းမှ ဖယ်ထုတ်ခဲ့သည်။'],
        // 'new_return_voucher' => ['en' => 'add voucher to return', 'mm' => 'ပြန်ပို့ရန် စာရင်းသွင်းခဲ့သည်။'],
        // 'remove_return_voucher' => ['en' => 'remove voucher from return', 'mm' => 'ပြန်ပို့ရန်စာရင်းမှ ဖယ်ထုတ်ခဲ့သည်။'],
        // 'delete_voucher' => ['en' => 'delete voucher', 'mm' => 'ဘောက်ချာကို ဖျောက်ဖျက်ခဲ့သည်။'],
        // // end voucher
        // 'new_delisheet' => ['en' => 'new delisheet', 'mm' => 'ပို့ဆောင်ရန်စာရင်း တစ်ခုဖန်တီးခဲ့သည်။'],
        // 'change_delivery_man' => ['en' => 'change delivery man', 'mm' => 'ပို့ဆောင်သူအမည်ကို'],
        // 'delete_delisheet' => ['en' => 'delete delisheet', 'mm' => 'ပို့ဆောင်ရန်စာရင်းကို ဖျောက်ဖျက်ခဲ့သည်။'],
        // // end delisheet
        // 'new_waybill' => ['en' => 'add new waybill', 'mm' => 'ဝေးဘေလ်အသစ်တစ်ခု ဖန်တီးခဲ့သည်။'],
        // 'change_from_bus_station' => ['en' => 'change sender bus station', 'mm' => 'ပေးပို့သူကားကွင်းကို'],
        // 'change_to_bus_station' => ['en' => 'change receiver bus station', 'mm' => 'လက်ခံသူကားကွင်းကို'],
        // 'change_waybill_gate' => ['en' => 'change waybill gate', 'mm' => 'ဝေးဘေလ်ဂိတ်ကို'],
        // 'change_actual_bus_fee' => ['en' => 'change bus fee', 'mm' => 'ဝေးဘေလ်တန်ဆာခကို'],
        // 'receive_waybill' => ['en' => 'receive waybill', 'mm' => 'ဝေးဘေလ် လက်ခံခဲ့သည်။'],
        // 'delet_waybill' => ['en' => 'delete waybill', 'mm' => 'ဝေးဘေလ်စာရင်းကို ဖျောက်ဖျက်ခဲ့သည်။'],
        // // end waybill
        // 'new_returnsheet' => ['en' => 'add new return sheet', 'mm' => 'ပြန်ပို့ရန်စာရင်းအသစ်တစ်ခု ဖန်တီးခဲ့သည်။'],
        // 'delete_returnsheet' => ['en' => 'delete new return sheet', 'mm' => 'ပြန်ပို့ရန်စာရင်းကို ဖျောက်ဖျက်ခဲ့သည်။'],
        // // end returnsheet
        // 'new_msf' => ['en' => 'add new merchant sheet', 'mm' => 'ကုန်သည်ငွေစာရင်းရှင်းတမ်းအသစ်တစ်ခု ဖန်တီးခဲ့သည်။'],
        // 'delete_msf' => ['en' => 'delete merchant sheet', 'mm' => 'ကုန်သည်ငွေစာရင်းရှင်းတမ်းကို ဖျောက်ဖျက်ခဲ့သည်။'],
        // // end msf
        // 'close' => ['en' => 'close', 'mm' => 'ပိတ်ခဲ့သည်။'],
        // // 'print_list' => ['en' => 'print voucher list', 'mm' => 'ဇယားလိုက် ပရင့်ထုတ်ခဲ့သည်။'],
        // // 'print_all_vouchers' => ['en' => 'print all vouchers', 'mm' => 'ဘောက်ချာအားလုံး ပရင့်ထုတ်ခဲ့သည်။'],
        // 'export_opened_sheet' => ['en' => 'export opened state', 'mm' => 'opened stateတွင် export လုပ်သွားသည်။'],
        // 'export_closed_sheet' => ['en' => 'export closed state', 'mm' => 'closed stateတွင် export လုပ်သွားသည်။'],
        // 'print_opened_sheet' => ['en' => 'print opened state', 'mm' => 'opened stateတွင် print လုပ်သွားသည်။'],
        // 'print_closed_sheet' => ['en' => 'print closed state', 'mm' => 'closed stateတွင် print လုပ်သွားသည်။'],
        // 'print_opened_sheet_vouchers' => ['en' => 'print opened sheet vouchers', 'mm' => 'opened stateတွင် ဘောက်ချာအားလုံးprint လုပ်သွားသည်။'],
        // 'print_closed_sheet_vouchers' => ['en' => 'print closed sheet vouchers', 'mm' => 'closed stateတွင် ဘောက်ချာအားလုံးprint လုပ်သွားသည်။'],
        // 'import_data' => ['en' => 'import data', 'mm' => 'import data'],
        // 'receive_payment' => ['en' => 'receive payment', 'mm' => 'ငွေလက်ခံမှု အတည်ပြုခဲ့သည်။'],
        // // end general
        // 'remove_pickup_voucher' => ['en' => 'remove voucher from pickup', 'mm' => 'voucherကို pickupစာရင်းမှ ဖယ်ထုတ်ခဲ့သည်။'],
        // // click events
        // 'change_deliver_date' => ['en' => 'change deliver date', 'mm' => 'ပေးပို့ရန် ရက်စွဲကို'],
        // 'click_delivered_all' => ['en' => 'clicked delivered all', 'mm' => 'ဘောက်ချာအားလုံး ပို့ဆောင်မှုအောင်မြင်ကြောင်း ခလုတ်နှိပ်ခဲ့သည်။'],
        // 'switch_on_not_delivered' => ['en' => 'switched on not delivered button', 'mm' => 'ပို့ဆောင်မှုမအောင်မြင်ကြောင်း ခလုတ်နှိတ်ခဲ့သည်။'],
        // 'switch_off_not_delivered' => ['en' => 'switched off not delivered button', 'mm' => 'ပို့ဆောင်မှုမအောင်မြင်ကြောင်း ပယ်ဖျက်ခလုတ်နှိပ်ခဲ့သည်။'],
        // // new logs for merchant side and new waybill flow
        // 'new_pickup_voucher' => ['en' => 'add voucher to pickup', 'mm' => 'voucherကို pickupစာရင်းထဲ သွင်းခဲ့သည်။'],
        // 'confirmed_waybill' => ['en' => 'confirmed waybill', 'mm' => 'ဝေးဘေလ်ကို ပို့ဆောင်ရန် အတည်ပြုခဲ့သည်။'],
        // 'delivered_waybill' => ['en' => 'delivered waybill', 'mm' => 'ဝေးဘေလ်ကို ဂိတ်သို့ ပို့ဆောင်ခဲ့သည်။'],

        'add_delivery_invoice' => ['en' => 'add to delivery fee', 'mm' => 'ပေးပို့ငွေကို ပိုမိုထည့်ခဲ့သည်။'],
        'deduce_delivery_invoice' => ['en' => 'remove to delivery fee', 'mm' => 'ပေးပို့ငွေကို ဖယ်ထုတ်ခဲ့သည်။']

    ];
    // manual query 
    // INSERT INTO log_statuses  (value, description, description_mm) VALUES ('new_pickup_voucher', 'add voucher to pickup', 'voucherကို pickupစာရင်းထဲ သွင်းခဲ့သည်။');
    // INSERT INTO log_statuses  (value, description, description_mm) VALUES ('confirmed_waybill', 'confirm waybill', 'ဝေးဘေလ်ကို ပို့ဆောင်ရန် အတည်ပြုခဲ့သည်။');
    // INSERT INTO log_statuses  (value, description, description_mm) VALUES ('delivered_waybill', 'delivered waybill','ဝေးဘေလ်ကို ဂိတ်သို့ ပို့ဆောင်ခဲ့သည်။');
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
        Schema::disableForeignKeyConstraints();

        foreach ($this->statuses as $value => $description) {
            factory(LogStatus::class)->create([
                'value'           => $value,
                'description'     => $description["en"],
                'description_mm'  => $description["mm"]
            ]);
        }
        Schema::enableForeignKeyConstraints();
    }
}
