<?php

namespace App\Repositories\Web\Api\v1;

use App\Models\TrackingVoucher;
use App\Repositories\BaseRepository;

class TrackingVoucherRepository extends BaseRepository
{
    /**
     * @return string
     */
    public function model()
    {
        return TrackingVoucher::class;
    }

    /**
     * @param array $data
     *
     * @return TrackingVoucher
     */
    public function create(array $data) : TrackingVoucher
    {
        return TrackingVoucher::create([
            'voucher_id' => $data['voucher_id'],
            'city_id' => $data['city_id'],
            'tracking_status_id' => $data['tracking_status_id'],            
            'created_by' => auth()->user()->id
        ]);
    }
}

