<?php

namespace App\Repositories\Web\Api\v1;

use App\Models\City;
use App\Models\Meta;
use App\Models\Staff;
use App\Models\Pickup;
use App\Models\Voucher;
use App\Models\Customer;
use App\Models\Attachment;
use Illuminate\Support\Arr;
use App\Services\SmsService;
use App\Models\CommissionLog;
use App\Repositories\BaseRepository;
use App\Contracts\MembershipContract;
use Illuminate\Support\Facades\Storage;
use Googlei18n\MyanmarTools\ZawgyiDetector;
use App\Repositories\Web\Api\v1\AccountRepository;
use App\Repositories\Web\Api\v1\JournalRepository;
use App\Repositories\Web\Api\v1\VoucherRepository;

class PickupRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $sender_id;

    /**
     * @return string
     */
    protected $membershipContract;
    public function __construct(MembershipContract $membershipContract)
    {
        $this->membershipContract = $membershipContract;
    }
    public function model()
    {
        return Pickup::class;
    }

    /**
     * @param array $data
     *
     * @return Pickup
     */
    public function create(array $data): Pickup
    {
        if ($data['sender_type'] == 'Customer') {
            $customerRepository = new CustomerRepository();
            $customer = Customer::phone($data['sender_phone'])->first();
            $sender = [
                'name' => $data['sender_name'],
                'phone' => $data['sender_phone'],
                'other_phone' => isset($data['other_phone']) ? $data['other_phone'] : null,
                'address' => $data['sender_address'],
                'city_id' => $data['sender_city_id'],
                'zone_id' => isset($data['sender_zone_id']) ? $data['sender_zone_id'] : null
            ];
            $pickup_id = 0;

            if (Pickup::count()) {
                $pickup_id  = Pickup::latest()->first()->id;
            }

            $pickup_id += 1;

            if ($customer) {
                setPickupId($pickup_id);
                $customer = $customerRepository->update($customer, $sender);
            } else {
                setPickupId($pickup_id);
                $customer = $customerRepository->create($sender);
            }

            $this->sender_id = $customer->id;
        } elseif ($data['sender_type'] == 'Merchant') {
            $this->sender_id = $data['sender_id'];
        }

        // if (isset($data['pickup_fee'])) {
        //     $meta = Meta::where('key', 'pickup_price')->first();
        //     $pickup_fee = $data['pickup_fee'] ? $meta->value : 0;
        // }
        $meta = Meta::where('key', 'pickup_price')->first();
        $pickup_fee = $meta->value;

        if (isset($data['note'])) {
            $note = getConvertedString($data['note']);
        }
        if (isset($data['agent_city_id'])) {
            $city_id = $data['agent_city_id'];
            $agent_id = isset($data['pickuped_by']) ? $data['pickuped_by'] : City::find($city_id)->agent->id;
            $pickuped_by = isset($data['pickuped_by']) ? $data['pickuped_by'] : $agent_id;
            $pickuped_by_type = 'Agent';
            $assigned_by = isset($data['pickuped_by']) ? $data['pickuped_by'] : $agent_id;
            $assigned_by_type = 'Agent';
            $courier_type_id = null;
            $is_commissionable = 0;
            $is_pointable = 0;
        } else {
            $city_id = auth()->user()->city_id;
            $pickuped_by = isset($data['pickuped_by']) ? $data['pickuped_by'] : null;
            $pickuped_by_type = isset($data['pickuped_by']) ? 'Staff' : null;
            $assigned_by = isset($data['pickuped_by']) ? auth()->user()->id : null;
            $assigned_by_type = isset($data['pickuped_by']) ? 'Staff' : null;
            $courier_type_id = isset($data['pickuped_by']) ? optional(Staff::find($data['pickuped_by']))->courier_type_id : null;
            $is_commissionable = isset($data['pickuped_by']) ? Staff::find($data['pickuped_by'])->is_commissionable : 0;
            $is_pointable = isset($data['pickuped_by']) ? Staff::find($data['pickuped_by'])->is_pointable : 0;
        }
        if (isset($data['requested_date'])) {
            $requested_date = $data['requested_date'];
        } else {
            if (date('H') > 17) {
                $requested_date = date('Y-m-d', strtotime(' + 1 days'));
            } else {
                $requested_date = date('Y-m-d');
            }
        }
        return Pickup::create([
            'city_id' => $city_id,
            'sender_type' => $data['sender_type'],
            'sender_id' => $this->sender_id,
            'sender_associate_id' => isset($data['sender_associate_id']) ? $data['sender_associate_id'] : null,
            'qty' => isset($data['qty']) ? $data['qty'] : null,
            // 'total_delivery_amount' => isset($data['total_delivery_amount']) ? $data['total_delivery_amount'] : null,
            'total_amount_to_collect' => isset($data['total_amount_to_collect']) ? $data['total_amount_to_collect'] : null,
            'pickup_fee' => $pickup_fee,
            'note' => isset($data['note']) ? $note : null,
            //'type' => isset($data['type']) ? $data['type'] : null,
            //'opened_by' => isset($data['opened_by']) ? $data['opened_by'] : null,
            'assigned_by_id' => $assigned_by,
            'assigned_by_type' => $assigned_by_type,
            'pickuped_by_id' => $pickuped_by,
            'pickuped_by_type' => $pickuped_by_type,
            'priority' => isset($data['priority']) ? $data['priority'] : 0,
            'created_by_id' => auth()->user()->id,
            // 'is_pickuped' => 1,
            'created_by_type' => 'Staff',
            // 'is_called' => true,
            'requested_date' => $requested_date,
            'platform' => isset($data['platform']) ? $data['platform'] : null,
            'courier_type_id' => $courier_type_id,
            'is_commissionable' => $is_commissionable,
            'is_pointable' => $is_pointable

        ]);
    }

    /**
     * @param Pickup  $pickup
     * @param array $data
     *
     * @return mixed
     */
    public function update(Pickup $pickup, array $data): Pickup
    {
        if (isset($data['take_pickup_fee']) && $data['take_pickup_fee']) {
            $meta = Meta::where('key', 'pickup_price')->first();
            $pickup->pickup_fee = $meta->value;
        }

        if ($data['sender_type'] == 'Customer') {
            $customerRepository = new CustomerRepository();
            $customer = Customer::findOrFail($data['customer_id']);
            $sender = [
                'name' => $data['sender_name'],
                'phone' => $data['sender_phone'],
                'other_phone' => isset($data['other_phone']) ? $data['other_phone'] : null,
                'address' => $data['sender_address'],
                'city_id' => $data['sender_city_id'],
                'zone_id' => isset($data['sender_zone_id']) ? $data['sender_zone_id'] : null
            ];

            setPickupId($pickup->id);
            $customerRepository->update($customer, $sender);

            $this->sender_id = $customer->id;
        } elseif ($data['sender_type'] == 'Merchant') {
            $this->sender_id = $data['sender_id'];
        }

        if (isset($data['note'])) {
            $note = getConvertedString($data['note']);
        }

        $pickup->sender_type = isset($data['sender_type']) ? $data['sender_type'] : $pickup->sender_type;
        $pickup->sender_id = $this->sender_id;
        $pickup->sender_associate_id = isset($data['sender_associate_id']) ? $data['sender_associate_id'] : $pickup->sender_associate_id;
        $pickup->qty = isset($data['qty']) ? $data['qty'] : $pickup->qty;
        // $pickup->total_delivery_amount = isset($data['total_delivery_amount']) ? $data['total_delivery_amount'] : $pickup->total_delivery_amount;
        $pickup->total_amount_to_collect = isset($data['total_amount_to_collect']) ? $data['total_amount_to_collect'] : $pickup->total_amount_to_collect;
        $pickup->note = isset($data['note']) ? $note : $pickup->note;
        //$pickup->type = isset($data['type']) ? $data['type'] : $pickup->type;
        if ($pickup->pickuped_by_type === 'Agent') {
            $pickup->assigned_by_id = isset($data['pickuped_by']) ? $data['pickuped_by'] : $pickup->assigned_by_id;
            $pickup->assigned_by_type =  $pickup->assigned_by_type;
            $pickup->pickuped_by_id = isset($data['pickuped_by']) ? $data['pickuped_by'] : $pickup->pickuped_by_id;
            $pickup->pickuped_by_type = $pickup->pickuped_by_type;
            $pickup->courier_type_id = $pickup->courier_type_id;
            $pickup->is_commissionable = $pickup->is_commissionable;
            $pickup->is_pointable = $pickup->is_pointable;
        } else {
            $pickup->assigned_by_id = isset($data['pickuped_by']) ? auth()->user()->id : $pickup->assigned_by_id;
            $pickup->assigned_by_type = isset($data['pickuped_by']) ? 'Staff' : $pickup->assigned_by_type;
            $pickup->pickuped_by_id = isset($data['pickuped_by']) ? $data['pickuped_by'] : $pickup->pickuped_by_id;
            $pickup->pickuped_by_type = isset($data['pickuped_by']) ? 'Staff' : $pickup->pickuped_by_type;
            $pickup->courier_type_id = isset($data['pickuped_by']) ? Staff::find($data['pickuped_by'])->courier_type_id : $pickup->courier_type_id;
            $pickup->is_commissionable = isset($data['pickuped_by']) ? Staff::find($data['pickuped_by'])->is_commissionable : $pickup->is_commissionable;
            $pickup->is_pointable = isset($data['pickuped_by']) ? Staff::find($data['pickuped_by'])->is_pointable : $pickup->is_pointable;
        }
        $pickup->priority = isset($data['priority']) ? $data['priority'] : $pickup->priority;
        $pickup->requested_date = isset($data['requested_date']) ? $data['requested_date'] : $pickup->requested_date;

        if ($pickup->isDirty()) {
            $pickup->updated_by_type = 'Staff';
            $pickup->updated_by = auth()->user()->id;
            $pickup->save();
        }

        return $pickup->refresh();
    }

    /**
     * @param Pickup  $pickup
     *
     * @return mixed
     */
    public function update_store_status(Pickup $pickup): Pickup
    {
        // dd($pickup->vouchers);
        foreach ($pickup->vouchers as $voucher) {
            $voucher->store_status_id = 2;
            if ($voucher->isDirty()) {
                $voucher->updated_by_type = 'Staff';
                $voucher->updated_by = auth()->user()->id;
                $voucher->save();
            }
        }
        return $pickup->refresh();
    }

    /**
     * @param Pickup  $pickup
     *
     * @return mixed
     */
    public function update_undo_store_status(Pickup $pickup): Pickup
    {
        foreach ($pickup->vouchers as $voucher) {
            $voucher->store_status_id = 1;
            if ($voucher->isDirty()) {
                $voucher->updated_by_type = 'Staff';
                $voucher->updated_by = auth()->user()->id;
                $voucher->save();
            }
        }
        return $pickup->refresh();
    }

    /**
     * @param Pickup $pickup
     */
    public function destroy(Pickup $pickup)
    {
        // $deleted = $this->deleteById($pickup->id);
        $deleted = $pickup->delete();

        if ($deleted) {
            $pickup->deleted_by_type = 'Staff';
            $pickup->deleted_by = auth()->user()->id;
            $pickup->save();
        }
    }

    /**
     * @param Pickup $pickup
     *
     * @return array
     */
    public function closed(Pickup $pickup, array $data): Pickup
    {
        // dd('hi');
        $phone = array();

        $pickup->is_closed = 1;
        $pickup->closed_date = date('Y-m-d H:i:s');
        $pickup->pickuped_by_id = isset($data['pickuped_by_id']) ? $data['pickuped_by_id'] : $pickup->pickuped_by_id;
        $pickup->courier_type_id = isset($data['courier_type_id']) ? $data['courier_type_id'] : $pickup->courier_type_id;
        $pickup->is_commissionable = isset($data['is_commissionable']) ? $data['is_commissionable'] : $pickup->is_commissionable;
        $pickup->is_pointable = isset($data['is_pointable']) ? $data['is_pointable'] : $pickup->is_pointable;
        // $pickup->is_pickuped = 1;
        if (!$pickup->is_pickuped) {
            $pickup->is_pickuped = 1;
            $pickup->pickup_date = now();
        }

        $accountRepository = new AccountRepository();
        // old
        if ($pickup->vouchers()->prepaidAmount() === 0) {
            // $accountRepository = new AccountRepository();
            $accountRepository->pickup_finance_confirm(['pickup_id' => $pickup->id]);
        }
        // 
        $hero = Staff::findOrFail($pickup->pickuped_by_id);
        $alreadyEarned = $this->membershipContract->checkCommission($hero, $pickup);
        if ($hero && $pickup->getQtyAttribute() > 0 && $pickup->is_commissionable && isHero($pickup) && $alreadyEarned < 1) {
            // cal commmission
            $total_voucher = $pickup->getQtyAttribute();
            $per_voucher = $total_voucher * 100;
            $pickup_zone = $pickup->sender_type == 'Merchant' ? $pickup->sender_associate->zone : $pickup->customer->zone;
            $pickp_commission = Meta::where('key', 'pickup_commission')->first();
            $commission_amount = $per_voucher + (int) $pickp_commission->value;
            $pickup->commission_amount = $commission_amount;
            // delivery commmission journal
            $journalRepository = new JournalRepository();
            $branch_account_id  = auth()->user()->city->branch->account->id;
            $hero_account = $hero->account ? $hero->account : $journalRepository->create_account($hero, 'Staff');
            $accountRepository->JournalCreateData($branch_account_id, $hero_account->id, $commission_amount, $pickup, 'Pickup');
            $this->membershipContract->loggingCommission($hero, $pickup, $pickup_zone, $pickup->getQtyAttribute());
        }
        if (
            $hero && $pickup->getQtyAttribute() > 0 && $pickup->is_pointable && isHero($pickup)
            && !isBlackList($hero) && !isFreelancerCar($hero)
        ) {
            $this->membershipContract->earnPointPerVoucher($pickup, $pickup->getQtyAttribute(), 'Pickup');
        }
        $merchant_rate_cards = ($pickup->sender_type == 'Merchant') ? $pickup->sender_associate->merchant_rate_cards : null;

        // update picked_date in vouchers
        $voucher_qty = $pickup->vouchers->count();
        foreach ($pickup->vouchers as $key => $voucher) {
            $count = $key + 1;
            if ($merchant_rate_cards) {
                $voucherRepository = new VoucherRepository();
                $data['bus_station'] = $voucher->bus_station;
                $data['sender_bus_station_id'] = $voucher->sender_bus_station_id;
                $data['receiver_bus_station_id'] = $voucher->receiver_bus_station_id;
                $data['receiver_zone_id'] = $voucher->receiver_zone_id;
                $data['sender_zone'] = $voucher->sender_zone_id;
                $data['receiver_city_id'] = $voucher->receiver_city_id;
                $data['sender_city'] = $voucher->sender_city_id;
                $data['qty_rate_card'] = true;
                $data['weight'] = ($voucher->parcels[0]) ? $voucher->parcels[0]->weight : 2;
                $rate_card = $voucherRepository->get_merchant_rate_card($pickup->sender_associate, $data, 'Merchant');
                if ($rate_card && $rate_card['min_threshold'] && $rate_card['min_threshold'] <= $voucher_qty) {
                    if (strtolower($rate_card['qty_status']) == 'inclusive') {
                        if ($rate_card['discount_type_id'] == 1) {
                            $percentage_amount = $voucher->total_delivery_amount * ($rate_card['amount'] / 100);
                            $voucher->total_delivery_amount = $voucher->total_delivery_amount - $percentage_amount;
                        } else {
                            $voucher->total_delivery_amount = $rate_card['amount'];
                        }
                    } else {
                        if ($count > $rate_card['min_threshold']) {
                            if ($rate_card['discount_type_id'] == 1) {
                                $percentage_amount = $voucher->total_delivery_amount * ($rate_card['amount'] / 100);
                                $voucher->total_delivery_amount = $voucher->total_delivery_amount - $percentage_amount;
                            } else {
                                $voucher->total_delivery_amount = $rate_card['amount'];
                            }
                        }
                    }
                }
            }
            $voucher->picked_date = date('Y-m-d H:i:s');
            $voucher->save();
            // array_push($phone, $voucher->receiver->phone);
            //Send Message to customer
            // $sms_service = new SmsService;
            // $phone = $voucher->receiver->phone;
            // $data_message = [
            //     'invoice_no' => $voucher->id,
            //     'phone' => $phone,
            // ];
            // $sms_service->sendSmsRequest($phone,$data_message);
        }
        if ($pickup->isDirty()) {
            $pickup->updated_by = auth()->user()->id;
            $pickup->save();
        }

        return $pickup->refresh();
    }

    public function update_pickup_fee(Pickup $pickup, array $data): Pickup
    {
        $pickup_fee = 0;
        if ($data['take_pickup_fee']) {
            $meta = Meta::where('key', 'pickup_price')->first();
            $pickup_fee = $meta->value;
        }
        $pickup->pickup_fee = $pickup_fee;

        if ($pickup->isDirty()) {
            $pickup->updated_by = auth()->user()->id;
            $pickup->save();
        }

        return $pickup->refresh();
    }

    public function update_requested_date(Pickup $pickup, array $data): Pickup
    {
        $pickup->requested_date = $data['requested_date'];

        if ($pickup->isDirty()) {
            $pickup->updated_by = auth()->user()->id;
            $pickup->save();
        }
        return $pickup->refresh();
    }

    /**
     * @param Array $data
     *
     * @return Pickup
     */
    public function assign_pickup(array $data): Pickup
    {
        $pickup = Pickup::findOrFail($data['pickup_id']);
        $pickup->pickuped_by_type = "Staff";
        $pickup->pickuped_by_id = $data['pickuped_by'];
        $pickup->assigned_by_id = auth()->user()->id;
        $pickup->assigned_by_type = 'Staff';
        $pickup->courier_type_id = isset($data['pickuped_by']) ? Staff::find($data['pickuped_by'])->courier_type_id : null;
        $pickup->is_commissionable = isset($data['pickuped_by']) ? Staff::find($data['pickuped_by'])->is_commissionable : 0;
        $pickup->is_pointable = isset($data['pickuped_by']) ? Staff::find($data['pickuped_by'])->is_pointable : 0;
        if ($pickup->isDirty()) {
            $pickup->updated_by = auth()->user()->id;
            $pickup->save();
        }

        return $pickup->refresh();
    }


    /**
     * Upload Attachment
     */
    public function upload($pickup, $file): Pickup
    {
        /**
         * Check Request has File
         */
        $file_name = null;
        $folder  = 'pickup';
        $date_folder = date('F-Y');
        $path = $folder . '/' . $date_folder;
        if (gettype($file) == 'string') {
            $file_name = $pickup->pickup_invoice . '_image_' . time() . '.' . 'png';
            $file_content = base64_decode($file);
        } else {
            $file_name = $pickup->pickup_invoice . '_image_' . time() . '_' . $file->getClientOriginalName();
            $file_content = file_get_contents($file);
        }
        Storage::disk('dospace')->put($path . '/' . $file_name, $file_content);
        Storage::setVisibility($path . '/' . $file_name, "public");

        Attachment::create([
            'resource_type' => 'Pickup',
            'image' => $file_name,
            'resource_id' => $pickup->id,
            'note' => $pickup->note,
            'latitude' => null,
            'longitude' => null,
            'is_sign' => 0,
            'created_by' => auth()->user()->id
        ]);

        return $pickup->refresh();
    }
    // change or assign hero 
    public function change_hero(Pickup $pickup, array $data): Pickup
    {
        $delivery = Staff::find($data['pickuped_by_id']);
        $pickup->pickuped_by_id = $delivery->id;
        $pickup->pickuped_by_type = 'Staff';
        $pickup->courier_type_id = isset($data['courier_type_id']) ? $data['courier_type_id'] : $pickup->courier_type_id;
        $pickup->is_commissionable = isset($data['is_commissionable']) ? $data['is_commissionable'] : $pickup->is_commissionable;
        $pickup->is_pointable = isset($data['is_pointable']) ? $data['is_pointable'] : $pickup->is_pointable;

        if ($pickup->isDirty()) {
            $pickup->save();
        }

        return $pickup->refresh();
    }
}
