<?php

namespace App\Repositories\Web\Api\v1\SuperMerchant;

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
            'sender_id' => $data['merchant_id'],
            'sender_associate_id' => $data['merchant_associate_id'],
            'note' => isset($data['note']) ?  $note : null,
            // 'opened_by' => isset($data['opened_by']) ? $data['opened_by'] : null,
            'priority' => isset($data['priority']) ? $data['priority'] : 0,
            'created_by_id' => auth()->user()->id,
            'city_id' => $data['city_id'],
            'created_by_type' => 'Merchant',
            // 'is_called' => isset($data['is_called']) ? $data['is_called'] : 0,
            // 'requested_date' => isset($data['requested_date']) ? $data['requested_date'] : null,
            'platform' => isset($data['platform']) ? $data['platform'] : null
        ]);

        return $pickup->refresh();
    }
}
