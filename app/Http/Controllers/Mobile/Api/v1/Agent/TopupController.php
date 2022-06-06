<?php

namespace App\Http\Controllers\Mobile\Api\v1\Agent;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use App\Http\Requests\Mobile\Agent\Topup\CreateTopupRequest;
use App\Repositories\Mobile\Api\v1\Agent\TransactionRepository;
use App\Http\Resources\Mobile\Agent\Transaction\TransactionResource;

class TopupController extends Controller
{

    /**
     * @var transactionRepository
     */
    protected $transactionRepository;

    /**
     * AgentController constructor.
     *
     * @param transactionRepository $transactionRepository
     */
    public function __construct(TransactionRepository $transactionRepository)
    {
        $this->transactionRepository = $transactionRepository;
    }

   /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CreateTopupRequest $request)
    {
        $transaction = $this->transactionRepository->create($request->all());

        if ($transaction) {
            return new TransactionResource($transaction->load(['from_account','to_account','attachments']));
        } else {
            return response()->json([
                'status' => 2, 'message' => 'Selected city does not have an account'
            ], Response::HTTP_OK);
        }
    }
}
