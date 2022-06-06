<?php

namespace App\Http\Controllers\Web\Api\v1;

use App\Models\Bank;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use App\Http\Resources\Bank\BankResource;
use App\Http\Resources\Bank\BankCollection;
use App\Http\Requests\Bank\CreateBankRequest;
use App\Http\Requests\Bank\UpdateBankRequest;
use App\Repositories\Web\Api\v1\BankRepository;

class BankController extends Controller
{
    /**
    * @var BankRepository
    */
    protected $bankRepository;

    /**
     * BankController constructor.
     *
     * @param BankRepository $bankRepository
     */
    public function __construct(BankRepository $bankRepository)
    {
        $this->bankRepository = $bankRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $banks = Bank::all();

        return new BankCollection($banks);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CreateBankRequest $request)
    {
        $bank =$this->bankRepository->create($request->all());

        return new BankResource($bank);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Bank  $bank
     * @return \Illuminate\Http\Response
     */
    public function show(Bank $bank)
    {
        return new BankResource($bank);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Bank  $bank
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateBankRequest $request, Bank $bank)
    {
        $bank = $this->bankRepository->update($bank, $request->all());

        return new BankResource($bank);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Bank  $bank
     * @return \Illuminate\Http\Response
     */
    public function destroy(Bank $bank)
    {
        $this->bankRepository->destroy($bank);

        return response()->json([ 'status' => 1 ], Response::HTTP_OK);
    }
}
