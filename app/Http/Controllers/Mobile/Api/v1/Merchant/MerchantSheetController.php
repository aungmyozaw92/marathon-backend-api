<?php

namespace App\Http\Controllers\Mobile\Api\v1\Merchant;

use App\Models\Journal;
use App\Models\MerchantSheet;
use App\Http\Controllers\Controller;
use App\Repositories\Mobile\Api\v1\MerchantSheetRepository;
use App\Http\Resources\Mobile\Transaction\TransactionResource;
use App\Http\Requests\Mobile\Transaction\CreateWithdrawRequest;
use App\Http\Resources\Mobile\MerchantSheet\MerchantSheetResource;
use App\Http\Resources\Mobile\MerchantSheet\MerchantSheetCollection;
use App\Http\Resources\Mobile\MerchantJournal\MerchantJournalResource;
use App\Http\Resources\Mobile\MerchantJournal\MerchantJournalCollection;

class MerchantSheetController extends Controller
{
    /**
     * @var MerchantSheetRepository
     */

    protected $merchantsheetRepository;

    /**
     * MerchantSheetController constructor.
     *
     * @param MerchantSheetRepository $merchantsheetRepository
     */
    public function __construct(MerchantSheetRepository $merchantsheetRepository)
    {
        $this->merchantsheetRepository = $merchantsheetRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // $merchantSheets =  MerchantSheet::filter(request()->all())->get();
        $merchant = auth()->user();
        
        $merchantSheets =  $merchant->merchant_sheets->where('is_paid', 1);

        return new MerchantSheetCollection($merchantSheets);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\MerchantSheet  $merchantSheet
     * @return \Illuminate\Http\Response
     */
    public function show(MerchantSheet $merchantSheet)
    {
        return new MerchantSheetResource($merchantSheet->load([
            'vouchers', 'vouchers.customer','vouchers.receiver_city','vouchers.receiver_zone',
           'vouchers.delivery_status'
        ]));
    }

    public function transaction_lists()
    {
        $merchant = auth()->user();
        $account_id = $merchant->account->id;
        $journals =  Journal::with(['resourceable',
                                'resourceable.payment_type',
                                'resourceable.delivery_status',
                                'resourceable.receiver',
                                'resourceable.receiver_city',
                                'resourceable.sender_city',
                                'resourceable.pickup.sender',
                                'credit_account','debit_account'
                                ])
                                ->getTransactionJournal($account_id,request()->only([
                                    'start_date', 'end_date', 
                                ]))->paginate(40);

        return new MerchantJournalCollection($journals); 
    }

    public function search_transaction()
    {
        $merchant = auth()->user();
        $account_id = $merchant->account->id;
        $journals =  Journal::with(['resourceable',
                                'resourceable.payment_type',
                                'resourceable.delivery_status',
                                'resourceable.receiver',
                                'resourceable.receiver_city',
                                'resourceable.sender_city',
                                'resourceable.pickup.sender',
                                'credit_account','debit_account'
                                ])
                                ->getTransactionJournalSearch(request()->only(['search']))->get();

        return new MerchantJournalCollection($journals); 

        // $journals =  Journal::with(['resourceable' => function ($query) use ($search) {
        //             return  $query->whereHas('receiver', function ($qr) use ($search) {
        //                 $qr->where('name', 'ILIKE', "%{$search}%")
        //                                             ->orWhere('phone', 'ILIKE', "%{$search}%");
        //             });
        //         } ])->getTransactionJournal($account_id)->get();

    }

    public function create_withdraw(CreateWithdrawRequest $request)
    {
        $transaction = $this->merchantsheetRepository->create_withdraw($request->all());
        if ($transaction) {
            if ($transaction['status'] == 1) {
                return new TransactionResource($transaction['data']->load(['from_account','to_account','attachments', 'account_information', 'account_information.bank']));
            } else {
                return $transaction;
            }
        } 

    }
}
