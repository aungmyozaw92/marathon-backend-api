<?php

namespace App\Http\Controllers\Mobile\Api\v2\Merchant;

use App\Models\Journal;
use App\Models\Transaction;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use App\Http\Requests\Mobile\Transaction\CreateWithdrawRequest;
use App\Repositories\Mobile\Api\v2\Merchant\MerchantSheetRepository;
use App\Http\Resources\Mobile\v2\Merchant\Transaction\TransactionResource;
use App\Http\Resources\Mobile\v2\Merchant\MerchantJournal\MerchantJournalCollection;

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
		$this->middleware('can:view,transaction')->only('transaction_detail');
        $this->merchantsheetRepository = $merchantsheetRepository;
    }

    public function create_withdraw(CreateWithdrawRequest $request)
    {
        $response = $this->merchantsheetRepository->create_withdraw($request->all());
        return  $response;
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
				->getTransactionJournal($account_id, request()->only([
					'start_date', 'end_date', 'transaction'
				]))->paginate(40);
		return new MerchantJournalCollection($journals);
	}

	public function transaction_detail(Transaction $transaction)
	{
		return new TransactionResource($transaction->load(['bank','attachments']));
	}

	public function search_transaction()
	{
		$merchant = auth()->user();
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

	}
}
