<?php

namespace App\Http\Controllers\Web\Api\v1;

use Illuminate\Http\Response;
use App\Models\FinanceAdvance;
use App\Http\Controllers\Controller;
use App\Http\Requests\Transaction\FileRequest;
use App\Http\Resources\Attachment\AttachmentCollection;
use App\Repositories\Web\Api\v1\FinanceAdvanceRepository;
use App\Http\Resources\FinanceAdvance\FinanceAdvanceResource;
use App\Http\Resources\FinanceAdvance\FinanceAdvanceCollection;
use App\Http\Requests\FinanceAdvance\CreateFinanceAdvanceRequest;
use App\Http\Requests\FinanceAdvance\UpdateFinanceAdvanceRequest;
use App\Http\Requests\FinanceAdvance\ConfirmFinanceAdvanceRequest;

class FinanceAdvanceController extends Controller
{
    /**
     * @var FinanceAdvanceRepository
     */
    protected $financeAdvanceRepository;

    /**
     * FinanceAdvanceController constructor.
     *
     * @param FinanceAdvanceRepository $financeAdvanceRepository
     */
    public function __construct(FinanceAdvanceRepository $financeAdvanceRepository)
    {
        $this->financeAdvanceRepository = $financeAdvanceRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $finance_advances = FinanceAdvance::with('branch', 'staff', 'from_finance_account', 'to_finance_account')
            ->filter(request()->only(['staff_id', 'start_date', 'end_date', 'issuer']));
        if (request()->has('paginate')) {
            $finance_advances = $finance_advances->paginate(request()->get('paginate'));
        } else {
            $finance_advances = $finance_advances->get();
        }

        return new FinanceAdvanceCollection($finance_advances);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CreateFinanceAdvanceRequest $request)
    {
        $finance_advance = $this->financeAdvanceRepository->create($request->all());

        return new FinanceAdvanceResource($finance_advance->load(['branch', 'staff', 'from_finance_account', 'to_finance_account']));
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\FinanceAdvance  $finance_advance
     * @return \Illuminate\Http\Response
     */
    public function show(FinanceAdvance $financeAdvance)
    {
        return new AttachmentCollection($financeAdvance->attachments);
        // return new FinanceAdvanceResource($financeAdvance->load(['branch', 'staff', 'from_finance_account', 'to_finance_account']));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\FinanceAdvance  $finance_advance
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateFinanceAdvanceRequest $request, FinanceAdvance $financeAdvance)
    {
        $finance_advance = $this->financeAdvanceRepository->update($financeAdvance, $request->all());

        return new FinanceAdvanceResource($finance_advance->load(['branch', 'staff', 'from_finance_account', 'to_finance_account']));
    }

     /**
     * Display the specified resource.
     *
     * @param  \App\FinanceAdvance  $finance_advance
     * @return \Illuminate\Http\Response
     */
    public function confirm(ConfirmFinanceAdvanceRequest $request, FinanceAdvance $financeAdvance)
    {
        if($financeAdvance->status){
            return response()->json(['status' => 2, 'message'=> 'Alread confirm'], Response::HTTP_OK);
        }
        $finance_advance = $this->financeAdvanceRepository->confirm($financeAdvance,$request->all());
        return new FinanceAdvanceResource($finance_advance->load(['branch', 'staff', 'from_finance_account', 'to_finance_account']));
    }

    public function upload(FileRequest $request, FinanceAdvance $financeAdvance)
    {
        $finance_expense = $this->financeAdvanceRepository->upload($financeAdvance, $request->all());
        return new AttachmentCollection($financeAdvance->attachments);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\FinanceAdvance  $finance_advance
     * @return \Illuminate\Http\Response
     */
    public function destroy(FinanceAdvance $finance_advance)
    {
        $this->financeAdvanceRepository->destroy($finance_advance);

        return response()->json(['status' => 1], Response::HTTP_OK);
    }
}
