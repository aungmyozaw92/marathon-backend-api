<?php

namespace App\Repositories\Web\Api\v1\MerchantDashboard;

use App\Models\Meta;
use App\Models\Pickup;
use App\Models\Voucher;
use App\Repositories\BaseRepository;

class PickupRepository extends BaseRepository
{
    /**
     * @return string
     */
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
        if (isset($data['note'])) {
            $note = getConvertedString($data['note']);
        }

        if(isset($data['requested_date'])){
            $requested_date = $data['requested_date'];
        }else{
            if (date('H') > 17) {
                $requested_date = date('Y-m-d', strtotime(' + 1 days'));
            }else{
              $requested_date = date('Y-m-d');  
            }
        }

        $pickup = Pickup::create([
            'sender_type' => 'Merchant',
            'sender_id' => auth()->user()->id,
            'sender_associate_id' => $data['merchant_associate_id'],
            'note' => isset($data['note']) ?  $note : null,
            // 'opened_by' => isset($data['opened_by']) ? $data['opened_by'] : null,
            'priority' => isset($data['priority']) ? $data['priority'] : 0,
            'created_by_id' => auth()->user()->id,
            'city_id' => auth()->user()->city_id,
            'created_by_type' => 'Merchant',
            // 'is_called' => isset($data['is_called']) ? $data['is_called'] : 0,
            'requested_date' => $requested_date,
            'platform' => isset($data['platform']) ? $data['platform'] : null
        ]);

        $vouchers = Voucher::whereIn('id', $data['voucher_id'])->whereNull('pickup_id')->get();

        foreach ($vouchers as $voucher) {
            // $voucher->pickup()->associate($pickup);
            $voucher->pickup_id = $pickup->id;
            $voucher->sender_city_id = $data['sender_city_id'];
            $voucher->sender_zone_id = isset($data['sender_zone_id']) ? $data['sender_zone_id'] : null;
            $voucher->save();
            $voucher->voucherPickupFire('new_pickup_voucher', $pickup->id);
            $pickup->pickupVoucherFire('new_pickup_voucher', $voucher->id);
        }

        return $pickup->refresh();
    }

    /**
     * @param Pickup  $pickup
     * @param array $data
     *
     * @return mixed
     */
    public function update(Pickup $pickup, array $data): Pickup
    {
        if (isset($data['pickup_fee'])) {
            $meta = Meta::where('key', 'pickup_price')->first();
            $pickup->pickup_fee = $data['pickup_fee'] ? $meta->value : 0;
            $pickup->save();

            return $pickup->refresh();
        }

        if (isset($data['note'])) {
            $note = getConvertedString($data['note']);
        }

        $pickup->sender_associate_id = isset($data['merchant_associate_id']) ? $data['merchant_associate_id'] : $pickup->merchant_associate_id;
        $pickup->note = isset($data['note']) ? $note : $pickup->note;
        // $pickup->opened_by = isset($data['opened_by']) ? $data['opened_by'] : $pickup->opened_by;
        $pickup->pickuped_by_id = isset($data['pickuped_by']) ? $data['pickuped_by'] : $pickup->pickuped_by_id;
        $pickup->pickuped_by_type = isset($data['pickuped_by']) ? 'Staff' : $pickup->pickuped_by_type;
        $pickup->priority = isset($data['priority']) ? $data['priority'] : $pickup->priority;
        $pickup->requested_date = isset($data['requested_date']) ? $data['requested_date'] : $pickup->requested_date;
        // $pickup->is_called = isset($data['is_called']) ? $data['is_called'] : $pickup->is_called;
        // $pickup->requested_date = isset($data['requested_date']) ? $data['requested_date'] : $pickup->requested_date;

        if ($pickup->isDirty()) {
            $pickup->updated_by_type = 'Merchant';
            $pickup->updated_by = auth()->user()->id;
            $pickup->save();
        }

        $vouchers = Voucher::whereIn('id', $data['voucher_id'])->whereNull('pickup_id')->get();
        // $voucher_removes = Voucher::whereIn('id', $data['voucher_remove_id'])->get();

        foreach ($vouchers as $voucher) {
            if (!$voucher->pickup()->exists()) {
                $voucher->voucherPickupFire('new_pickup_voucher', $pickup->id);
                $pickup->pickupVoucherFire('new_pickup_voucher', $voucher->id);
            }
            // $voucher->pickup()->associate($pickup);
            $voucher->pickup_id = $pickup->id;
            $voucher->sender_city_id = isset($data['sender_city_id']) ? $data['sender_city_id'] : $voucher->sender_city_id;
            $voucher->sender_zone_id = isset($data['sender_zone_id']) ? $data['sender_zone_id'] : null;
            $voucher->save();
        }

        if (isset($data['voucher_remove_id']) && $data['voucher_remove_id']) {
            $voucher_removes = Voucher::whereIn('id', $data['voucher_remove_id'])->get();
       
            foreach ($voucher_removes as $voucher) {
                $voucher->pickup()->dissociate();
                $voucher->save();
                $voucher->voucherPickupFire('remove_pickup_voucher', $pickup->id);
                $pickup->pickupVoucherFire('remove_pickup_voucher', $voucher->id);
            }
        }

        // foreach ($voucher_removes as $voucher) {
        //     $voucher->pickup()->dissociate();
        //     $voucher->save();
        //     $voucher->voucherPickupFire('remove_pickup_voucher', $pickup->id);
        //     $pickup->pickupVoucherFire('remove_pickup_voucher', $voucher->id);
        // }

        return $pickup->refresh();
    }

    /**
     * @param Pickup $pickup
     */
    public function destroy(Pickup $pickup)
    {
        $pickup->vouchers()->delete();

        $deleted = $this->deleteById($pickup->id);

        if ($deleted) {
            $pickup->deleted_by = auth()->user()->id;
            $pickup->save();
        }
    }

    public function closed(Pickup $pickup): Pickup
    {
        $pickup->is_closed = 1;
        $pickup->is_pickuped = 1;

        if ($pickup->isDirty()) {
            $pickup->updated_by = auth()->user()->id;
            $pickup->save();

            // foreach ($pickup->vouchers as $key => $voucher) {
            //     if (!$voucher->is_closed) {
            //         $voucherRepository = new VoucherRepository();
            //         $voucher = $voucherRepository->closed($voucher);
            //     }
            // }
        }

        return $pickup->refresh();
    }
}
