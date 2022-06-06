<?php

namespace App\Repositories\Mobile\Api\v1\Delivery;

use App\Models\BusStation;
use App\Repositories\BaseRepository;

class BusStationRepository extends BaseRepository
{
    /**
     * @return string
     */
    public function model()
    {
        return BusStation::class;
    }
}
