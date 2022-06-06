<?php

namespace App\Http\Controllers\Web\Api\v1\MerchantDashboard;

use App\Models\Journal;
use App\Models\Merchant;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\MerchantTransactionHistorySheet;
use App\Http\Resources\Merchant\MerchantResource;
use App\Http\Resources\Merchant\MerchantCollection;
use App\Repositories\Web\Api\v1\MerchantRepository;
use App\Http\Requests\Merchant\CreateMerchantRequest;
use App\Http\Requests\Merchant\UpdateMerchantRequest;
use App\Http\Resources\MerchantDashboard\TransactionJournal\TransactionJournalCollection;

class MerchantController extends Controller
{
    protected $merchantRepository;

    public function __construct(MerchantRepository $merchantRepository)
    {
        $this->merchantRepository = $merchantRepository;
    }

    public function index()
    {
        $merchants = Merchant::with(
            'merchant_associates',
            'merchant_associates.phones',
            'merchant_associates.emails',
            'merchant_associates.city',
            'merchant_associates.zone',
            'city',
            'staff'
        )
            ->filter(request()->only(['search']))
            ->get();

        // return new MerchantCollection($merchants->load([
        //     'merchant_associates', 'merchant_associates.phones', 'merchant_associates.emails',
        //     'merchant_associates.city', 'merchant_associates.zone', 'city', 'staff'
        // ]));
        return new MerchantCollection($merchants);
    }

    public function store(CreateMerchantRequest $request)
    {
        $merchant = $this->merchantRepository->create($request->all());

        return new MerchantResource($merchant->load([
            'merchant_associates', 'merchant_associates.phones', 'merchant_associates.emails',
            'merchant_associates.city', 'merchant_associates.zone', 'city', 'staff'
        ]));
    }

    public function show(Merchant $merchant)
    {
        return new MerchantResource($merchant->load([
            'merchant_associates', 'merchant_associates.phones', 'merchant_associates.emails',
            'merchant_associates.city', 'merchant_associates.zone', 'merchant_discounts', 'city', 'staff'
            ,'merchant_associates.account_informations'
        ]));
    }

    public function update(UpdateMerchantRequest $request, Merchant $merchant)
    {
        $merchant = $this->merchantRepository->update($merchant, $request->all());

        return new MerchantResource($merchant->load([
            'merchant_associates', 'merchant_associates.phones', 'merchant_associates.emails',
            'merchant_associates.city', 'merchant_associates.zone', 'city', 'staff'
        ]));
    }

    public function destroy(Merchant $merchant)
    {
        $this->merchantRepository->deleteById($merchant->id);

        return response()->json(['status' => 1], Response::HTTP_OK);
    }

    public function search(Request $request)
    {
        $merchants = $this->merchantRepository->scopeLike('name', $request->data);

        return new MerchantCollection($merchants);
    }

    public function transaction_lists()
    {
        $account_id = auth()->user()->account->id;
        if (request()->has('export')) {
            $filename = 'merchant_transaction.xlsx';
            Excel::store(new MerchantTransactionHistorySheet($account_id), $filename, 'public', null, [
                    'visibility' => 'public',
            ]);
            $file = storage_path('/app/public/merchant_transaction.xlsx');
            return response()->download($file)->deleteFileAfterSend();
        }

        if (request()->has(['paginate'])) {
            $paginate_count = request()->get('paginate');
        } else {
            $paginate_count = 20;
        }
    
        if (request()->get('transaction') === 'true') {
            $journals =  Journal::with(['resourceable','credit_account','debit_account'])
                            ->getOnlyTransactionJournal($account_id, request()->only([
                                'start_date', 'end_date'
                            ]))->paginate($paginate_count);
        }else{
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
                                'start_date', 'end_date'
                            ]))->paginate($paginate_count);
        }

        return new TransactionJournalCollection($journals);
    }
}
