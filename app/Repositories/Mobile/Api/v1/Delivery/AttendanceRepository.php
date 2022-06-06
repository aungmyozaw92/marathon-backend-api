<?php

namespace App\Repositories\Mobile\Api\v1\Delivery;

use App\Models\Attendance;
use App\Repositories\BaseRepository;

class AttendanceRepository extends BaseRepository
{
    /**
     * @return string
     */
    public function model()
    {
        return Attendance::class;
    }

    public function create(array $data) : Attendance
    {
        $attendance = Attendance::create([
            'qr_code' => $data['qr_code'],
            'staff_id' => auth()->user()->id,
            'scanned_date' =>now(),
        ]);

        return $attendance;
    }

    
}
