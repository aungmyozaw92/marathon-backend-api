<?php

namespace App\Repositories\Mobile\Api\v1;

use App\Models\TrackingStatus;
use App\Repositories\BaseRepository;

class TrackingStatusRepository extends BaseRepository
{
    /**
     * @return string
     */
    public function model()
    {
        return TrackingStatus::class;
    }
}
