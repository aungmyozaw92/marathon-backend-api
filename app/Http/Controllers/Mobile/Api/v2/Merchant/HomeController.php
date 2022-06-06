<?php

namespace App\Http\Controllers\Mobile\Api\v2\Merchant;

use App\Models\Pickup;
use App\Models\Voucher;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use App\Models\ReturnSheet;
use App\Models\Transaction;

class HomeController extends Controller
{
    public function __construct()
    {
    }

    public function index(){

        $vouchers = Voucher::getMerchantAllVouchers('all_vouchers')->count();
        $vouchers_in_pickups = Voucher::getMerchantPickupVouchers()->count();
        $draft_vouchers = Voucher::getMerchantDraftVouchers('draft_vouchers')->count();
        $incomplete_vouchers = Voucher::getMerchantDraftVouchers('incomplete_vouchers')->count();
        $delivering_vouchers = Voucher::getMerchantDeliveryVouchers('delivering_vouchers')->count();
        $delivered_vouchers = Voucher::getMerchantDeliveryVouchers('delivered_vouchers')->count();

        $pending_return_vouchers = Voucher::getMerchantReturnVouchers('pending_return_vouchers')->count();
        $returning_vouchers = Voucher::getMerchantReturnVouchers('returning_vouchers')->count();
        $returned_vouchers = Voucher::getMerchantReturnVouchers('returned_vouchers')->count();

        // $cant_delivered_vouchers = Voucher::getMerchantCantDeliveredVouchers('all')->count();
        $cant_delivered_solve_vouchers = Voucher::getMerchantCantDeliveredVouchers('cant_delivered_solved')->count();
        $cant_delivered_unsolve_vouchers = Voucher::getMerchantCantDeliveredVouchers('cant_delivered_unsolved')->count();
        
        $pickups = Pickup::getMerchantPickups()->count();
        $return_sheets = ReturnSheet::getMerchantReturnSheets()->count();

        $pending_transactions = Transaction::getMerchantPendingTransactions()->count();
   
        return response()->json(
            [
                'status' => 1,
                'message' => 'Success',
                'data' => [
                    'total_vouchers' => $vouchers,
                    'total_pickups' => $pickups,
                    'draft_vouchers' => $draft_vouchers,
                    'incomplete_vouchers' => $incomplete_vouchers,
                    'delivering_vouchers' => $delivering_vouchers,
                    'delivered_vouchers' => $delivered_vouchers,
                    // 'cant_delivered_vouchers' => $cant_delivered_vouchers,
                    'cant_delivered_solve_vouchers' => $cant_delivered_solve_vouchers,
                    'cant_delivered_unsolve_vouchers' => $cant_delivered_unsolve_vouchers,
                    'vouchers_in_pickups' => $vouchers_in_pickups,
                    'return_sheets' => $return_sheets,
                    'pending_return_vouchers' => $pending_return_vouchers,
                    'returning_vouchers' => $returning_vouchers,
                    'returned_vouchers' => $returned_vouchers,
                    'pending_transactions' => $pending_transactions,
                    'unconfirmed_balance' => auth()->user()->pending_balance(),
                    
                ]
            ],
            Response::HTTP_OK
        );
    }
     
}

// $query->where('is_return', false)
//                 ->where('delivery_status_id', 9)
//                 ->whereDoesntHave('return_sheets');

// 23
// can't_delivered_vouchers_total
// 157
// can't_delivered_vouchers_unsolved
// 135

// vouchers_in_pickups