<?php

namespace App\Http\Controllers\Web\Api\v1;

use App\Models\Journal;
use App\Models\Voucher;
use App\Models\Merchant;
use App\Models\TempJournal;
use Illuminate\Http\Request;
use App\Exports\MerchantData;
use Illuminate\Http\Response;
use App\Models\ContactAssociate;
use App\Http\Controllers\Controller;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\MerchantTransactionHistorySheet;
use App\Exports\MerchantTransactionHistoryExport;
use App\Http\Resources\Merchant\MerchantResource;
use App\Http\Resources\Merchant\MerchantCollection;
use App\Repositories\Web\Api\v1\MerchantRepository;
use App\Http\Requests\Merchant\CreateMerchantRequest;
use App\Http\Requests\Merchant\UpdateMerchantRequest;
use App\Http\Requests\Merchant\RestoreMerchantRequest;
use App\Http\Resources\TempJournal\TempJournalCollection;
use App\Http\Requests\Merchant\UpdateDiscountMerchantRequest;
use App\Http\Resources\TransactionJournal\TransactionJournalCollection;

class MerchantController extends Controller
{
    protected $merchantRepository;

    public function __construct(MerchantRepository $merchantRepository)
    {
        $this->merchantRepository = $merchantRepository;
    }

    public function index()
    {
        if (!request()->exists('balance')) {
            request()->request->add(['balance' => '']);
        }

        if (request()->has('export')) {
            $filename = 'merchants.xlsx';
            Excel::store(new MerchantData, $filename, 'public', null, [
                    'visibility' => 'public',
            ]);
            $file = storage_path('/app/public/merchants.xlsx');
            return response()->download($file)->deleteFileAfterSend();
        }

        $merchants = Merchant::with(
            'merchant_associates',
            'contact_associates',
            'merchant_associates.contact_associates',
            'merchant_associates.phones',
            'merchant_associates.emails',
            'merchant_associates.city',
            'merchant_associates.zone',
            'account_informations',
            'account_informations.bank',
            'city',
            'staff',
            'account',
            'account.accountable',
            'attachments'
        )
        // || auth()->user()->hasRole('CustomerService')
            ->where(function ($query) {
                if(auth()->user()->hasRole('Agent')){
                    $query->whereHas('city', function($q){
                        $q->whereDoesntHave('branch');
                    });
                }elseif(auth()->user()->hasRole('Admin') || auth()->user()->hasRole('HQ')){
                    $query;
                }else{
                    $query->where('city_id', auth()->user()->city_id);
                }   
            })->filter(request()->only([
                'search', 'merchant_id', 'city_id', 'username', 'name', 'staff_id',
                'label', 'address', 'phone', 'email', 'account_name', 'account_no',
                'balance', 'balance_operator', 'is_deleted', 'bank_id'
            ]))->order(request()->only([
                'sortBy', 'orderBy'
            ]));

        if (auth()->user()->hasRole('Admin') || auth()->user()->hasRole('HQ')) {
            $merchants = $merchants->withTrashed();
        }

        if (request()->get('associated_merchant') === "true") {
            $merchants_id = Merchant::where('staff_id', auth()->user()->id)->get()->pluck('id');
            $merchants = $merchants->whereIn('id', $merchants_id);
        }

        if (request()->has('paginate')) {
            $merchants = $merchants->paginate(request()->get('paginate'));
        } else {
            $merchants = $merchants->get();
        }
        // return new MerchantCollection($merchants->load([
        //     'merchant_associates', 'merchant_associates.phones', 'merchant_associates.emails',
        //     'merchant_associates.city', 'merchant_associates.zone', 'city', 'staff'
        // ]));
        return new MerchantCollection($merchants);
    }
    public function all()
    {
        if (!request()->exists('balance')) {
            request()->request->add(['balance' => '']);
        }

        $merchants = Merchant::with(
            'merchant_associates',
            'merchant_associates.phones',
            'merchant_associates.emails',
            'merchant_associates.city',
            'merchant_associates.zone',
            'account_informations',
            'account_informations.bank',
            'city',
            'staff',
            'account'
        )
            ->filter(request()->only([
                'search', 'merchant_id', 'city_id', 'username', 'name', 'staff_id' ,
                'label', 'address', 'phone', 'email', 'account_name', 'account_no',
                'balance', 'balance_operator', 'is_deleted'
            ]))->orderBy('id', 'desc')
            ->withTrashed()
            ->get();
        return new MerchantCollection($merchants);
    }

    public function store(CreateMerchantRequest $request)
    {
        $merchant = $this->merchantRepository->create($request->all());

        return new MerchantResource($merchant->load([
            'merchant_associates', 'merchant_associates.phones', 'merchant_associates.emails',
            'merchant_associates.city', 'merchant_associates.zone', 'city', 'staff', 'account_informations',
            'account_informations.bank'
        ]));
    }

    public function show(Merchant $merchant)
    {
        if (auth()->user()->hasRole('Agent') || auth()->user()->hasRole('Admin') || $merchant->city_id == auth()->user()->city_id) {
            return new MerchantResource($merchant->load([
                'merchant_associates', 'merchant_associates.phones', 'merchant_associates.emails',
                'merchant_associates.city', 'merchant_associates.zone', 'merchant_discounts', 'city', 'staff'
                ,'merchant_associates.account_informations', 'account_informations', 'account_informations.bank'
            ]));
        }
        return response()->json([
            'status' => 2,
            'message' => 'Unauthenticated!'
        ], Response::HTTP_OK);
    }

    public function update(UpdateMerchantRequest $request, Merchant $merchant)
    {
        $merchant = $this->merchantRepository->update($merchant, $request->all());

        return new MerchantResource($merchant->load([
            'merchant_associates', 'merchant_associates.phones', 'merchant_associates.emails',
            'merchant_associates.city', 'merchant_associates.zone', 'city', 'staff', 'account_informations',
            'account_informations.bank'
        ]));
    }

    public function destroy(Merchant $merchant)
    {
       $pickups = $merchant->pickups->pluck('id');
       $openVoucherCount = Voucher::whereIn('pickup_id',$pickups)->where('is_closed', 0)->count();
       if ($openVoucherCount > 0) {
        return response()->json(['status' => 2, 'message'=> 'Cannot delete bcoz this merchant has opening vouchers'], Response::HTTP_OK);
       }
        // $this->merchantRepository->deleteById($merchant->id);
        // $this->merchantRepository->destroy($merchant);

        return response()->json(['status' => 1], Response::HTTP_OK);
    }

    public function search(Request $request)
    {
        $merchants = $this->merchantRepository->scopeLike('name', $request->data);

        return new MerchantCollection($merchants);
    }
    
    public function add_contact(Request $request)
    {
        $merchants = Merchant::with('merchant_associates')->get();

        foreach ($merchants as $merchant) {
            foreach ($merchant->merchant_associates as $ms) {
                ContactAssociate::create([
                        'merchant_id'            => $merchant->id,
                        'merchant_associate_id'  => $ms->id,
                        'type'                   => 'phone',
                        'value'                  => 1
                ]);
            }
        }
    }

    public function transaction_lists(Merchant $merchant)
    {
        $account_id = $merchant->account->id;
        if (request()->has('export')) {
            $filename = 'merchant_transaction.xlsx';
            Excel::store(new MerchantTransactionHistorySheet($account_id), $filename, 'public', null, [
                    'visibility' => 'public',
            ]);
            $file = storage_path('/app/public/merchant_transaction.xlsx');
            return response()->download($file)->deleteFileAfterSend();
        }
        //$account_id = $merchant->account->id;
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
                                'start_date', 'end_date',
                            ]))->paginate(25);

        return new TransactionJournalCollection($journals);
    }

    public function update_discount_status(UpdateDiscountMerchantRequest $request, Merchant $merchant)
    {
        $merchant = $this->merchantRepository->update_discount_status($merchant, $request->all());

        return new MerchantResource($merchant->load([
            'merchant_associates', 'merchant_associates.phones', 'merchant_associates.emails',
            'merchant_associates.city', 'merchant_associates.zone', 'city', 'staff', 'account_informations',
            'account_informations.bank'
        ]));
    }

    public function restore(Request $request)
    {
        $trash_merchant = Merchant::withTrashed()->where('id', request()->only(['merchant_id']))->firstOrFail();
        if ($trash_merchant->deleted_at) {
            $merchant = $this->merchantRepository->restore($trash_merchant);

            return new MerchantResource($merchant->load([
                'merchant_associates', 'merchant_associates.phones', 'merchant_associates.emails',
                'merchant_associates.city', 'merchant_associates.zone', 'city', 'staff', 'account_informations',
                'account_informations.bank'
            ]));
        } else {
            return response()->json(['status' => 2, 'message' => 'This merchant is already restored!'], Response::HTTP_OK);
        }
    }

    public function temp_journal_lists(Merchant $merchant)
    {
        $temp_journals = TempJournal::where('merchant_id', $merchant->id)
                                ->where('city_id', auth()->user()->city_id)
                                ->where('status', 0)
                                ->where('balance_status', 1)
                                ->filter(request()->only([
                                    'start_date', 'end_date'
                                ]));
        if (request()->has('paginate')) {
            $paginate_count = request()->get('paginate') ? request()->get('paginate') : 25;
            $temp_journals = $temp_journals->paginate($paginate_count);
        } else {
            $temp_journals = $temp_journals->get();
        }

        return new TempJournalCollection($temp_journals);
    }
}
