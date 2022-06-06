<?php

namespace App\Http\Controllers\Web\Api\v1;

use App\Models\Staff;
use App\Models\PointLog;
use App\Models\Deduction;
use App\Models\Attachment;
use Illuminate\Http\Request;
use App\Exports\PointLogData;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Storage;
use App\Http\Resources\PointLog\PointLogCollection;

class PointLogController extends Controller
{
    // /**
    //  * @var DeductionRepository
    //  */
    // protected $deductionRepository;

    // /**
    //  * BadgeController constructor.
    //  *
    //  * @param DeductionRepository $deductionRepository
    //  */
    // public function __construct(DeductionRepository $deductionRepository)
    // {
    //     $this->deductionRepository = $deductionRepository;
    // }
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (request()->has('export')) {
            $filename = 'point_logs.xlsx';
            Excel::store(new PointLogData, $filename, 'public', null, [
                    'visibility' => 'public',
            ]);
            $file = storage_path('/app/public/point_logs.xlsx');
            return response()->download($file)->deleteFileAfterSend();
        }

        $point_logs = PointLog::with(['staff', 'hero_badge', 'attachments'])
                            ->filter(request()->all())
                            ->whereHas('staff', function ($query) {
                                $query->where('city_id', auth()->user()->city_id);
                            })
                            ->order(request()->only([
                                'sortBy', 'orderBy'
                            ]));

        if (request()->has('paginate')) {
            $point_logs = $point_logs->paginate(request()->get('paginate'));
        } else {
            $point_logs = $point_logs->get();
        }

        return new PointLogCollection($point_logs);
    }

    /**
     * Deduct point from staff
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function pointDeduction(Request $request)
    {
        $deduction = Deduction::findOrFail($request->get('deduction_id'));
        $staff = Staff::findOrFail($request->get('staff_id'));
        
        /**
         * Create Point Log
         */
        $pointLog = PointLog::create([
            'staff_id'          => $staff->id,
            'points'            => $deduction->points,
            'note'              => $request->get('note'),
            'status'            => 'Remove',
            'resourceable_type' => 'Deduction',
            'resourceable_id'   => $deduction->id,
            'hero_badge_id'     => optional($staff->hero_badge)->id,
            'created_by'        => auth()->user()->id
        ]);

        /**
         * Point Deduct from Staff
         */
        $staff->points = $staff->points - $deduction->points;
        $staff->save();
        
        /**
         * Check Request has File
         */
        if ($request->hasFile('file') && $file = $request->file('file')) {
            $file_name = null;
            $folder  = 'deduction';
            $date_folder = date('F-Y');
            $path = $folder.'/'.$date_folder;
            if (gettype($file) == 'string') {
                $file_name = $deduction->description . '_image_' . time() . '.' . 'png';
                $file_content = base64_decode($file);
            } else {
                $file_name = $deduction->description . '_image_' . time() . '_' . $file->getClientOriginalName();
                $file_content = file_get_contents($file);
            }
            Storage::disk('dospace')->put($path . '/' . $file_name, $file_content);
            Storage::setVisibility($path . '/' . $file_name, "public");
            
            return Attachment::create([
                    'resource_type' => 'PointLog',
                    'image' => $file_name,
                    'resource_id' => $pointLog->id,
                    'note' => $pointLog->note,
                    'latitude' => null,
                    'longitude' => null,
                    'is_sign' => 0,
                    'created_by' => auth()->user()->id
                ]);
        }

        return response()->json([ 'status' => 1 ], Response::HTTP_OK);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    // public function store(CreateDeductionRequest $request)
    // {
    //     $deduction = $this->deductionRepository->create($request->all());

    //     return new DeductionResource($deduction);
    // }

    /**
     * Display the specified resource.
     *
     * @param  \App\Deduction  $deductionBadge
     * @return \Illuminate\Http\Response
     */
    // public function show(Deduction $deduction)
    // {
    //     return new DeductionResource($deduction);
    // }

    /**
    * Update the specified resource in storage.
    *
    * @param  \Illuminate\Http\Request  $request
    * @param  \App\Deduction  $deduction
    * @return \Illuminate\Http\Response
    */
    // public function update(UpdateDeductionRequest $request, Deduction $deduction)
    // {
    //     $deduction = $this->deductionRepository->update($deduction, $request->all());

    //     return new DeductionResource($deduction);
    // }

    /**
    * Remove the specified resource from storage.
    *
    * @param  \App\Deduction  $deduction
    * @return \Illuminate\Http\Response
    */
    // public function destroy(Deduction $deduction)
    // {
    //     $this->deductionRepository->destroy($deduction);

    //     return response()->json([ 'status' => 1 ], Response::HTTP_OK);
    // }
}
