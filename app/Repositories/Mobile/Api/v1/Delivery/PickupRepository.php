<?php

namespace App\Repositories\Mobile\Api\v1\Delivery;

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

        $pickup = Pickup::create([
            'sender_type' => 'Merchant',
            'sender_id' => auth()->user()->id,
            'sender_associate_id' => $data['merchant_associate_id'],
            'note' => isset($data['note']) ? $note : null,
            //'opened_by' => isset($data['opened_by']) ? $data['opened_by'] : null,
            'priority' => isset($data['priority']) ? $data['priority'] : 0,
            'created_by_id' => auth()->user()->id,
            'created_by_type' => 'Staff',
        ]);

        $vouchers = Voucher::whereIn('id', $data['voucher_id'])->whereNull('pickup_id')->get();

        foreach ($vouchers as $voucher) {
            $voucher->pickup()->associate($pickup);
            $voucher->sender_city_id = $data['sender_city_id'];
            $voucher->sender_zone_id = isset($data['sender_zone_id']) ? $data['sender_zone_id'] : null;
            $voucher->save();
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
        $pickup->is_pickuped = isset($data['is_pickuped']) ? $data['is_pickuped'] : $pickup->is_pickuped;
        $pickup->pickup_date = now();
        // $staff = auth()->user();

        if ($pickup->isDirty()) {
            // $staff->points += 5 ;
            // $staff->save();
            $pickup->is_came_from_mobile = 1;
            $pickup->actby_mobile = auth()->user()->id;
            $pickup->updated_by = auth()->user()->id;
            $pickup->save();
        }
        return $pickup->refresh();
    }

    /**
     * @param Pickup $pickup
     */
    public function destroy(Pickup $pickup)
    {
        $deleted = $this->deleteById($pickup->id);

        if ($deleted) {
            $pickup->deleted_by = auth()->user()->id;
            $pickup->save();
        }
    }
}
