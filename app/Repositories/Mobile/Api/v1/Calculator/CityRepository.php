<?php

namespace App\Repositories\Mobile\Api\v1\Calculator;

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
