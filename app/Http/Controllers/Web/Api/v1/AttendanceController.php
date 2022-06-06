<?php

namespace App\Http\Controllers\Web\Api\v1;

use App\Models\Attendance;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use App\Http\Resources\Attendance\AttendanceResource;
use App\Http\Resources\Attendance\AttendanceCollection;
use App\Repositories\Web\Api\v1\AttendanceRepository;

class AttendanceController extends Controller
{
    /**
     * @var AttendanceRepository
     */
    protected $attendanceRepository;

    /**
     * AttendanceController constructor.
     *
     * @param AttendanceRepository $attendanceRepository
     */
    public function __construct(AttendanceRepository $attendanceRepository)
    {
        $this->attendanceRepository = $attendanceRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $attendances =  $this->attendanceRepository->all();

        return new AttendanceCollection($attendances);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Attendance  $attendance
     * @return \Illuminate\Http\Response
     */
    public function show(Attendance $attendance)
    {
        return new AttendanceResource($attendance->load(['city']));
    }

    public function generateAttendanceCode()
    {
        $qr_code = generateAttendanceQrCode();

        return response()->json([
            'status' => 1, 'data' => $qr_code
        ], Response::HTTP_OK);
    }
}
