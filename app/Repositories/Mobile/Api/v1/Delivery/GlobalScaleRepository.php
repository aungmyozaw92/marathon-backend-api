<?php

namespace App\Repositories\Mobile\Api\v1\Delivery;

use App\Models\GlobalScale;
use App\Repositories\BaseRepository;

class GlobalScaleRepository extends BaseRepository
{
    /**
     * @return string
     */
    public function model()
    {
        return GlobalScale::class;
    }
}
