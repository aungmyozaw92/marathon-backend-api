<?php

namespace App\Http\Controllers\Web\Api\v1;

use App\Models\Account;
use App\Models\DeliSheet;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Http\Resources\Account\AccountCollection;
use App\Repositories\Web\Api\v1\AccountRepository;
use App\Http\Requests\Account\PickupConfirmRequest;
use App\Http\Requests\Account\UpdateBalanceRequest;
use App\Http\Requests\Account\WaybillConfirmRequest;
use App\Http\Requests\Account\BusSheetConfirmRequest;
use App\Http\Requests\Account\DelisheetConfirmRequest;
use App\Http\Requests\Account\BranchSheetConfirmRequest;
use App\Http\Requests\Account\MerchantSheetConfirmRequest;

class AccountController extends Controller
{
    /**
     * @var AccountRepository
     */
    protected $accountRepository;

    /**
     * AccountController constructor.
     *
     * @param AccountRepository $accountRepository
     */
    public function __construct(AccountRepository $accountRepository)
    {
        $this->accountRepository = $accountRepository;
    }

    public function getAccountBalance()
    {
         $accounts = Account::where('accountable_type', 'HQ')
                             ->orWhere('accountable_type', 'Branch')
                             ->orWhere('accountable_type', 'Agent')
                             ->get();

        return new AccountCollection($accounts->load(['city']));
    }

    public function get_merchant_account_balance()
    {
        $accounts = Account::with(['city','accountable'])
                    ->merchantFilter(request()->only([
                        'city_id', 'name', 
                    ]))->paginate(25);
        //->where('accountable_type', 'Merchant')->paginate(25);
        $total_balance = Account::where('accountable_type', 'Merchant')->sum('balance');
        $total_hq_balance = getHqAccount()->balance;
        return (new AccountCollection($accounts))->additional([
                    'total_balance' => $total_balance,
                    'total_hq_balance' => $total_hq_balance,
                    ]);
    }

    public function update_balance(UpdateBalanceRequest $request)
    {
        $this->accountRepository->update_balance($request->all());

        return response()->json([
            'status' => 1,
            'message' => "Successful"
        ], Response::HTTP_OK);

    }

    public function delisheet_financeConfirm(DelisheetConfirmRequest $request)
    {
        $deliSheet = DeliSheet::findOrFail($request->get('delisheet_id'));
        if ($deliSheet->payment_token != $request->get('payment_token')) {
            return response()->json([
                'status' => 2,
                'message' => 'DeliSheet payment token mismatch.'
            ], Response::HTTP_OK);
        }
        if (!$deliSheet->is_closed) {
            return response()->json([
                'status' => 2,
                'message' => 'DeliSheet need to close first.'
            ], Response::HTTP_OK);
        }
        if ($deliSheet->is_paid) {
            return response()->json([
                'status' => 2,
                'message' => 'DeliSheet is already paid.'
            ], Response::HTTP_OK);
        }

        if (!$deliSheet->payment_token) {
            return response()->json([
                'status' => 2,
                'message' => 'DeliSheet payment token does not exit.'
            ], Response::HTTP_OK);
        }
        Log::info('Delisheet Finance Confirm'.$request->get('delisheet_id'));
        $responses = $this->accountRepository->delisheet_finance_confirm($request->all());

        return response()->json([
            'status' => $responses['status'],
            'message' => $responses['message']
        ], Response::HTTP_OK);
    }

    public function pickup_financeConfirm(PickupConfirmRequest $request)
    {
        $voucher = $this->accountRepository->pickup_finance_confirm($request->all());

        return response()->json([
            'status' => 1,
            "message" => "Pickup and prepaid vouchers are successfully confirm."
        ], Response::HTTP_OK);
    }

    public function waybill_financeConfirm(WaybillConfirmRequest $request)
    {
        $voucher = $this->accountRepository->waybill_finance_confirm($request->all());

        if ($voucher) {
            return response()->json([
                'status' => 1,
                "message" => "Waybill vouchers are successfully confirm."
            ], Response::HTTP_OK);
        }
        
        return response()->json([
                'status' => 2,
                "message" => "This waybill is opening or already receive payment, so cannot receive payment !"
            ], Response::HTTP_OK);
    }

    public function bus_sheet_financeConfirm(BusSheetConfirmRequest $request)
    {
        $responses = $this->accountRepository->bus_sheet_finance_confirm($request->all());

        return response()->json([
            'status' => $responses['status'],
            'message' => $responses['message']
        ], Response::HTTP_OK);
    }

    public function merchant_sheet_financeConfirm(MerchantSheetConfirmRequest $request)
    {
        $responses = $this->accountRepository->merchant_sheet_finance_confirm($request->all());

        return response()->json([
            'status' => $responses['status'],
            'message' => $responses['message']
        ], Response::HTTP_OK);
    }

    public function branch_sheet_financeConfirm(BranchSheetConfirmRequest $request)
    {
        $responses = $this->accountRepository->branch_sheet_finance_confirm($request->all());

        return response()->json([
            'status' => $responses['status'],
            'message' => $responses['message']
        ], Response::HTTP_OK);
    }
    public function manual_delisheet_financeConfirm(Request $request)
    {
        $responses = $this->accountRepository->manual_delisheet_finance_confirm($request->all());

        return response()->json([
            'status' => $responses['status'],
            'message' => $responses['message']
        ], Response::HTTP_OK);
    }
    
}
