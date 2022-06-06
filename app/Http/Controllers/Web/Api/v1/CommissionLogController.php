<?php

namespace App\Http\Controllers\Web\Api\v1;

use App\Models\Attachment;
use Illuminate\Http\Request;
use App\Models\CommissionLog;
use Illuminate\Http\Response;
use App\Exports\CommissionLogData;
use App\Http\Controllers\Controller;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Storage;
use App\Http\Resources\CommissionLog\CommissionLogResource;
use App\Http\Resources\CommissionLog\CommissionLogCollection;

class CommissionLogController extends Controller
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
            $filename = 'commission_logs.xlsx';
            Excel::store(new CommissionLogData, $filename, 'public', null, [
                    'visibility' => 'public',
            ]);
            $file = storage_path('/app/public/commission_logs.xlsx');
            return response()->download($file)->deleteFileAfterSend();
        }

        $commission_logs = CommissionLog::with(['staff', 'zone'])
                            ->filter(request()->all())
                            // ->where('staff_id', auth()->user()->id)
                            ->whereHas('staff', function ($query) {
                                $query->where('city_id', auth()->user()->city_id);
                            })
                            ->order(request()->only([
                                'sortBy', 'orderBy'
                            ]));
        $total_base_commission = $commission_logs->sum('zone_commission');
        $total_vouchers_commission = $commission_logs->sum('voucher_commission');
                            
        if (request()->has('paginate')) {
            $commission_logs = $commission_logs->paginate(request()->get('paginate'));
        } else {
            $commission_logs = $commission_logs->get();
        }


        return (new CommissionLogCollection($commission_logs))->additional([
            'total_base_commission' => $total_base_commission,
            'total_vouchers_commission' => $total_vouchers_commission,
            'final_commission'  => $total_base_commission + $total_vouchers_commission
        ]);
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
    public function show(CommissionLog $commissionLog)
    {
        return new CommissionLogResource($commissionLog);
    }

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
