<?php

namespace App\Http\Controllers\Web\Api\v1;

use App\Models\FinancePosting;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use App\Repositories\Web\Api\v1\FinancePostingRepository;
use App\Http\Resources\FinancePosting\FinancePostingResource;
use App\Http\Resources\FinancePosting\FinancePostingCollection;
use App\Http\Requests\FinancePosting\CreateFinancePostingRequest;
use App\Http\Requests\FinancePosting\UpdateFinancePostingRequest;

class FinancePostingController extends Controller
{
    /**
     * @var FinancePostingRepository
     */
    protected $financePostingRepository;

    /**
     * FinancePostingController constructor.
     *
     * @param FinancePostingRepository $financePostingRepository
     */
    public function __construct(FinancePostingRepository $financePostingRepository)
    {
        $this->financePostingRepository = $financePostingRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $orderBy = (request()->has('orderBy')) ? request()->get('orderBy') : 'desc';
        $finance_postings = FinancePosting::with('branch','from_finance_account','to_finance_account',
                                                'posting','posting.branch','posting.from_finance_account',
                                                'posting.to_finance_account','postingable',
                                                'from_actorable','to_actorable')
                    ->filter(request()->only([
                        'amount', 'status', 'finance_posting_type', 'finance_posting', 
                        'start_date', 'end_date','from_account', 'to_account', 'account'
                    ]))
                    ->orderBy('created_at', $orderBy);
        if (request()->has('paginate')) {
            $finance_postings = $finance_postings->paginate(request()->get('paginate'));
        }else{
            $finance_postings = $finance_postings->get();
        }
    
        return new FinancePostingCollection($finance_postings);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CreateFinancePostingRequest $request)
    {
        $finance_advance = $this->financePostingRepository->create($request->all());

        return new FinancePostingResource($finance_advance->load(['branch','from_finance_account','to_finance_account','posting','posting.branch','posting.from_finance_account','posting.to_finance_account']));
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\FinancePosting  $finance_advance
     * @return \Illuminate\Http\Response
     */
    public function show(FinancePosting $financePosting)
    {
        return new FinancePostingResource($financePosting->load(['branch','from_finance_account','to_finance_account',
                                                                 'posting','posting.branch','posting.from_finance_account',
                                                                 'posting.to_finance_account','postingable',
                                                                 'from_actorable','to_actorable']));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\FinancePosting  $finance_advance
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateFinancePostingRequest $request, FinancePosting $financePosting)
    {
        $finance_advance = $this->financePostingRepository->update($financePosting, $request->all());

        return new FinancePostingResource($finance_advance->load(['branch','from_finance_account','to_finance_account','posting']));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\FinancePosting  $finance_advance
     * @return \Illuminate\Http\Response
     */
    public function destroy(FinancePosting $finance_advance)
    {
        $this->financePostingRepository->destroy($finance_advance);

        return response()->json(['status' => 1], Response::HTTP_OK);
    }
}
