<?php

namespace App\Http\Controllers\Mobile\Api\v1\Delivery;

use App\Models\Staff;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use App\Http\Resources\Attendance\AttendanceResource;
use App\Repositories\Mobile\Api\v1\Delivery\AttendanceRepository;
use App\Http\Requests\Mobile\Delivery\Attendance\AttendanceRequest;

class AttendanceController extends Controller
{
    /**
     * @var AttendanceRepository
     */
    protected $attendanceRepository;

    /**
     * AttachmentController constructor.
     *
     * @param AttendanceRepository $attendanceRepository
     */
    public function __construct(AttendanceRepository $attendanceRepository)
    {
        $this->attendanceRepository = $attendanceRepository;
    }

    public function store(AttendanceRequest $request)
    {
        $staff = Staff::findOrFail(auth()->user()->id);

        if ($staff->is_present) {
            return response()->json([
                'status' => 2, 'message' => 'Already scanned.'
            ], Response::HTTP_OK);
        }

        $qr_code = getAttendanceQrCode();

        if ($qr_code != $request->get('qr_code')) {
            return response()->json([
                'status' => 2, 'message' => 'Invalid.'
            ], Response::HTTP_OK);
        }

        $attendance = $this->attendanceRepository->create($request->all());

        if ($attendance) {
            $staff->is_present = 1;
            $staff->save();
        }

        return response()->json([
            'status' => 1, 'message' => 'OK Thank You.'
        ], Response::HTTP_OK);

        // return new AttendanceResource($attendance);
    }
}
