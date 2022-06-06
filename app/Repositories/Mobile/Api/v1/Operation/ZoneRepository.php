<?php

namespace App\Repositories\Mobile\Api\v1\Operation;

use App\Models\Zone;
use App\Repositories\BaseRepository;

class ZoneRepository extends BaseRepository
{
    /**
     * @return string
     */
    public function model()
    {
        return Zone::class;
    }
}
