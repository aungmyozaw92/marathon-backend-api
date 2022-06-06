<?php

namespace App\Http\Controllers\Mobile\Api\v1\Delivery;

use App\Models\Attachment;
use Illuminate\Http\Request;
use App\Models\CommissionLog;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use App\Http\Resources\Mobile\Delivery\CommissionLog\CommissionLogResource;
use App\Http\Resources\Mobile\Delivery\CommissionLog\CommissionLogCollection;

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
        $commission_logs = CommissionLog::filter(request()->all())
                            ->where('staff_id', auth()->user()->id)
                            ->order(request()->only([
                                'sortBy', 'orderBy'
                            ]));

        if (request()->has('paginate')) {
            $commission_logs = $commission_logs->paginate(request()->get('paginate'));
        } else {
            $commission_logs = $commission_logs->get();
        }

        return new CommissionLogCollection($commission_logs);
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
        return new CommissionLogResource($commissionLog->load('deli_sheet',
        'deli_sheet.vouchers.payment_type',
        'deli_sheet.vouchers.customer',
        'waybill',
        'pickup',
        'pickup.vouchers',
        'return_sheet'));
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
