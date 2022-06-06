<?php

namespace App\Repositories\Mobile\Api\v1\Operation;

use App\Models\City;
use App\Repositories\BaseRepository;

class CityRepository extends BaseRepository
{
    /**
     * @return string
     */
    public function model()
    {
        return City::class;
    }
}
