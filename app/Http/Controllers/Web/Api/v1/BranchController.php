<?php

namespace App\Http\Controllers\Web\Api\v1;

use App\Models\Branch;
use App\Models\Journal;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Resources\Branch\BranchResource;
use App\Exports\BranchTransactionHistorySheet;
use App\Http\Resources\Branch\BranchCollection;
use App\Http\Requests\Branch\CreateBranchRequest;
use App\Http\Requests\Branch\UpdateBranchRequest;
use App\Repositories\Web\Api\v1\BranchRepository;
use App\Http\Resources\TransactionJournal\TransactionJournalCollection;

class BranchController extends Controller
{
    /**
     * @var BranchRepository
     */
    protected $branchRepository;

    /**
     * BranchController constructor.
     *
     * @param BranchRepository $branchRepository
     */
    public function __construct(BranchRepository $branchRepository)
    {
        $this->branchRepository = $branchRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // $branches = $this->branchRepository->all();
        $branches = Branch::all();

        return new BranchCollection($branches->load(['city','zone','account']));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CreateBranchRequest $request)
    {
        $branch =$this->branchRepository->create($request->all());

        return new BranchResource($branch->load(['city','zone','account']));
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Branch  $branch
     * @return \Illuminate\Http\Response
     */
    public function show(Branch $branch)
    {
        return new BranchResource($branch->load(['city','zone','account']));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Branch  $branch
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateBranchRequest $request, Branch $branch)
    {
        $branch = $this->branchRepository->update($branch, $request->all());

        return new BranchResource($branch->load(['city','zone','account']));
    }

    public function transaction_lists(Branch $branch)
    {
        $branch_account_id = $branch->account->id;
        if (request()->has('export')) {
            $filename = 'branch_transaction_histories.xlsx';
            Excel::store(new BranchTransactionHistorySheet($branch_account_id), $filename, 'public', null, [
                    'visibility' => 'public',
            ]);
            $file = storage_path('/app/public/branch_transaction_histories.xlsx');
            return response()->download($file)->deleteFileAfterSend();
        }
        $journals =  Journal::with(['resourceable',
                                    'resourceable.payment_type',
                                    'resourceable.delivery_status',
                                    'resourceable.receiver',
                                    'resourceable.receiver_city',
                                    'resourceable.sender_city',
                                    'resourceable.pickup.sender',
                                    'credit_account','debit_account'
                                    ])
                                    ->getTransactionJournal($branch_account_id,request()->only([
                                        'start_date', 'end_date', 
                                    ]))->paginate(50);
        return new TransactionJournalCollection($journals); 
    }

    // /**
    //  * Remove the specified resource from storage.
    //  *
    //  * @param  \App\Branch  $branch
    //  * @return \Illuminate\Http\Response
    //  */
    // public function destroy(Branch $branch)
    // {
    //     $this->branchRepository->destroy($branch);

    //     return response()->json([ 'status' => 1 ], Response::HTTP_OK);
    // }
}
