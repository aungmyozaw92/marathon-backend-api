<?php

namespace App\Repositories\Web\Api\v1;

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

    /**
     * @param array $data
     *
     * @return Attendance
     */
    public function create(array $data) : Attendance
    {
        $attendance = Attendance::create([
            'qr_code'              => $data['qr_code'],
            'staff_id'          => $data['staff_id'],
            'scanned_date'           => $data['scanned_date'],
        ]);

        return $attendance;
    }

    /**
     * @param Attendance  $attendance
     * @param array $data
     *
     * @return mixed
     */
    public function update(Attendance $attendance, array $data) : Attendance
    {
        $attendance->qr_code = $data['name'];
        $attendance->staff_id = $data['city_id'];
        $attendance->scanned_date = $data['username'];
        
        if ($attendance->isDirty()) {
            $attendance->updated_by = auth()->user()->id;
            $attendance->save();
        }

        return $attendance->refresh();
    }
}
