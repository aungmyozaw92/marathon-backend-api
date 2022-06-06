<?php

namespace App\Http\Controllers\Web\Api\v1;

use App\Models\Pickup;
use App\Models\Account;
use App\Models\Voucher;
use App\Models\Waybill;
use App\Models\Merchant;
use App\Models\DeliSheet;
use App\Services\SmsService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\DeliSheetVoucher;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Http\Resources\Account\AccountCollection;
use App\Http\Resources\Merchant\MerchantCollection;

class DashboardController extends Controller
{
    protected $smsService;

    public function __construct(SmsService $smsService)
    {
        $this->smsService = $smsService;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $date = request()->get('date');
        $totalVouchers = [];
        $doorToDoorVouchers = [];
        $waybillVouchers = [];
        if ($date) {
            $pickups = Pickup::with('vouchers')
                // ->where('sender_type', 'Merchant')
                // ->where('sender_id', auth()->user()->id)
                ->whereDate('created_at', $date)
                ->get();

            $vouchers = Voucher::with('pickup')->whereDate('created_at', $date)
                        ->whereNotNull('pickup_id')
                        ->whereNotNull('receiver_id')
                        ->get();
            foreach ($vouchers as $voucher) {
                // if ($voucher->pickup->sender_type == 'Merchant' && $voucher->pickup->sender_id == auth()->user()->id) {
                $totalVouchers[] = $voucher;
                // if ($voucher->receiver_city_id == getBranchCityId()) {
                if ($voucher->receiver_city_id == auth()->user()->city_id) {
                    $doorToDoorVouchers[] = $voucher;
                } else {
                    $waybillVouchers[] = $voucher;
                }
                // }
            }

            return response()->json([
                'status' => 1,
                "total_pickups_count" => $pickups->count(),
                "total_vouchers_count" => count($totalVouchers),
                "total_dtd_vouchers_count" => count($doorToDoorVouchers),
                "total_waybill_vouchers_count" => count($waybillVouchers)
            ], Response::HTTP_OK);
        }
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function cs_dashboard()
    {
        $date = request()->get('date');
        $total_waybill_voucher_count = 0;
        $total_delisheet_voucher_count =0;
        $total_return_voucher_count =0;
        $user = auth()->user();
        $merchants_id = Merchant::where('staff_id', auth()->user()->id)->get()->pluck('id');
        $last_10days_merchants = Merchant::where('staff_id', auth()->user()->id)->where('created_at', '>=', \Carbon\Carbon::today()->subDays(10))->orderBy('id', 'desc')->get();
        $accounts = Account::with(['city','accountable'])
                        ->where('accountable_type', 'Merchant')
                        ->whereIn('accountable_id', $merchants_id)
                        ->get();
        if ($date) {
            $total_waybill_voucher_count = Voucher::whereNotIn('delivery_status_id', [8,9])
                        ->whereNotNull('pickup_id')
                        ->where('origin_city_id', $user->city_id)
                        ->where(function ($query) use ($date) {
                            $query->whereHas('waybills', function ($query) use ($date) {
                                $query->where('is_closed', 0);
                                $query->whereDate('waybill_vouchers.created_at', $date);
                            });
                        })
                        ->count();
                       
            $total_delisheet_voucher_count = Voucher::whereNotIn('delivery_status_id', [8,9])
                        ->whereNotNull('pickup_id')
                        ->where('origin_city_id', $user->city_id)
                        ->whereHas('delisheets', function ($q) use ($date) {
                            $q->where('is_closed', 0)
                            ->whereDate('deli_sheet_vouchers.created_at', $date);
                        })
                        ->count();
            $total_return_voucher_count = Voucher::whereNotIn('delivery_status_id', [8])
                        ->whereNotNull('pickup_id')
                        ->where('origin_city_id', $user->city_id)
                        ->whereHas('return_sheets', function ($q) use ($date) {
                            $q->where('is_returned', 0)
                            ->whereDate('return_sheet_vouchers.created_at', $date);
                        })
                        ->count();
        }
        $voucher = Voucher::whereNotIn('delivery_status_id', [8,9])
                            ->whereNotNull('pickup_id')
                            ->where('origin_city_id', $user->city_id)
                            ->select(DB::raw("COUNT(CASE call_status_id WHEN 1 THEN 1 ELSE NULL END) AS first,
                                            COUNT(CASE call_status_id WHEN 2 THEN 2 ELSE NULL END) AS second,
                                            COUNT(CASE call_status_id WHEN 3 THEN 3 ELSE NULL END) AS third,
                                            COUNT(CASE call_status_id WHEN 4 THEN 4 ELSE NULL END) AS four,
                                            COUNT(CASE call_status_id WHEN 5 THEN 5 ELSE NULL END) AS five,
                                            COUNT(CASE call_status_id WHEN 8 THEN 8 ELSE NULL END) AS six,
                                            COUNT(CASE store_status_id WHEN 1 THEN 1 ELSE NULL END) AS store_status,
                                            COUNT(CASE delivery_status_id WHEN 1 THEN 1 ELSE NULL END) AS no_attempt,
                                            COUNT(CASE delivery_status_id WHEN 2 THEN 2 ELSE NULL END) AS first_attempt,
                                            COUNT(CASE delivery_status_id WHEN 3 THEN 3 ELSE NULL END) AS second_attempt,
                                            COUNT(CASE delivery_status_id WHEN 4 THEN 4 ELSE NULL END) AS third_attempt,
                                            COUNT(CASE outgoing_status IS NULL WHEN true THEN true ELSE NULL END) AS to_assign_delisheet,
                                            COUNT(CASE delivery_counter >= 3 WHEN true THEN true ELSE NULL END) AS over_third_attempt
                                            "))
                            ->first();
        $to_try_voucher = Voucher::whereNotIn('delivery_status_id', [8,9])
                            ->whereNotNull('pickup_id')
                            ->where('origin_city_id', $user->city_id)
                            ->whereHas('delisheets', function ($q) {
                                $q->where('is_closed', 0);
                            })
                            ->count();

        $to_try_waybill_voucher = Voucher::whereNotIn('delivery_status_id', [8,9])
                            ->whereNotNull('pickup_id')
                            ->where('origin_city_id', $user->city_id)
                            ->whereHas('waybills', function ($q) {
                                $q->where('is_closed', 0);
                            })
                            ->count();
 
        $return_voucher = Voucher::where('delivery_status_id', 9)
                            // ->whereNotNull('pickup_id')
                            ->where('origin_city_id', $user->city_id)
                            ->where('is_return', 0)
                            ->count();

        $delisheet = Delisheet::where(function ($qr) use ($user) {
            $qr->whereHas('delivery', function ($q) use ($user) {
                $q->where('city_id', $user->city_id);
            })
                                    ->orWhereHas('staff', function ($q) use ($user) {
                                        $q->where('city_id', $user->city_id);
                                    });
        })
                            ->select(DB::raw("COUNT(CASE is_closed WHEN false THEN false ELSE NULL END) AS open,
                                            COUNT(CASE is_paid WHEN false THEN false ELSE NULL END) AS unpaid,
                                            COUNT(CASE delivery_id IS NULL WHEN true THEN true ELSE NULL END) AS unassigned"))
                                            
                                            ->first();
                                            
        $waybill = DB::table("waybills")->where('from_city_id', $user->city_id)
                    ->select(DB::raw("COUNT(CASE is_closed WHEN false THEN false ELSE NULL END) AS open,
                                COUNT(CASE is_paid WHEN false THEN false ELSE NULL END) AS unpaid"))
                   ->first();
        $pickup = Pickup::where('city_id', $user->city_id)
                        ->select(DB::raw("COUNT(CASE is_closed WHEN false THEN false ELSE NULL END) AS open,
                                        COUNT(CASE is_paid WHEN false THEN false ELSE NULL END) AS unpaid"))
                              ->first();

        $total_voucher_count = Voucher::whereDate('created_at', $date)
                            ->where('origin_city_id', $user->city_id)
                            ->count();
        
        $total_voucher_count_in_pickup = Voucher::whereDate('created_at', $date)
                            ->whereNotNull('pickup_id')
                            ->where('origin_city_id', $user->city_id)
                            ->count();
        
        $total_pickup_count = Pickup::where('city_id', $user->city_id)
                                    ->whereDate('created_at', $date)
                                    ->count();
        $total_delisheet_count = Delisheet::whereDate('created_at', $date)
                                ->whereHas('staff', function ($q) use ($user) {
                                    $q->where('city_id', $user->city_id);
                                })->count();
        $total_waybill_count = Waybill::whereDate('created_at', $date)
                            ->select(DB::raw("
                            COUNT(CASE from_city_id WHEN $user->city_id THEN 0 ELSE NULL END) AS outgoing_waybill,
                            COUNT(CASE to_city_id WHEN $user->city_id THEN 0 ELSE NULL END) AS incoming_waybill"))
                            ->first();
        return response()->json([
                'status' => 1,
                'accounts' => new AccountCollection($accounts),
                'last_10days_merchants' => new MerchantCollection($last_10days_merchants),
                'total_voucher_count' => $total_voucher_count,
                'total_voucher_count_in_pickup' => $total_voucher_count_in_pickup,
                'total_pickup_count' => $total_pickup_count,
                'total_delisheet_count' => $total_delisheet_count,
                'total_outgoing_waybill_count' => $total_waybill_count->outgoing_waybill,
                'total_incoming_waybill_count' => $total_waybill_count->incoming_waybill,
                'total_delisheet_voucher_count' => $total_delisheet_voucher_count,
                'total_waybill_voucher_count' => $total_waybill_voucher_count,
                'total_return_voucher_count' => $total_return_voucher_count,

                'delivery_statuses' => [
                    "to_assign_delisheet" => $voucher->to_assign_delisheet,
                    "no_attempt" => $voucher->no_attempt,
                    "first_attempt" => $voucher->first_attempt,
                    "second_attempt" => $voucher->second_attempt,
                    "third_attempt" => $voucher->third_attempt,
                    "to_try_attemp_voucher" => $to_try_voucher,
                    'to_try_waybill_voucher'=> $to_try_waybill_voucher,
                    "return_voucher" => $return_voucher,
                    "over_third_attempt" => $voucher->over_third_attempt
                ],
                'call_statuses' => [
                    "have_not_call" => $voucher->first,
                    "cannot_call_wrong_number" => $voucher->second,
                    "cannot_call_power_off" => $voucher->third,
                    "can_call_do_not_pickup" => $voucher->four,
                    "can_call_out_of_service" => $voucher->five,
                    "can_call_busy" => $voucher->six
                ],
                'store_status' => [
                    "waiting" => $voucher->store_status
                ],
                'delisheet' => [
                    "open_delisheet" => $delisheet->open,
                    "unpaid_delisheet" => $delisheet->unpaid,
                    "unassigned_delisheet" => $delisheet->unassigned
                ],
                'waybill' => [
                    "open_waybill" => $waybill->open,
                    "unpaid_waybill" => $waybill->unpaid
                ],
                'pickup' => [
                    "open_pickup" => $pickup->open,
                    "unpaid_pickup" => $pickup->unpaid
                ]
            ], Response::HTTP_OK);
    }

    /**
     * Display return and postpone voucher count.
     *
     * @return Count
     */
    public function return_and_postpone_vouchers()
    {
        $merchant_ids = [];
        if (auth()->user()->department->department === 'CustomerService') {
            $merchant_ids = Merchant::where('staff_id', auth()->user()->id)->get()->pluck('id');
        }

        $pendingReturnVouchers = Voucher::where('is_return', false)
                                    ->where('delivery_status_id', 9)
                                    ->when($merchant_ids, function ($query, $merchant_ids) {
                                        return $query->whereHas('pickup', function ($qr) use ($merchant_ids) {
                                            $qr->where('sender_type', 'Merchant')
                                                ->whereIn('sender_id', $merchant_ids);
                                        });
                                    })
                                    ->where(function ($query) {
                                        $query->where('origin_city_id', auth()->user()->city_id)
                                            ->orWhere('sender_city_id', auth()->user()->city_id);
                                    })
                                    ->whereDoesntHave('return_sheets')
                                    ->count();

        $postponeVouchers = Voucher::whereNotNull('postpone_date')
                                    ->when($merchant_ids, function ($query, $merchant_ids) {
                                        return $query->whereHas('pickup', function ($qr) use ($merchant_ids) {
                                            $qr->where('sender_type', 'Merchant')
                                                ->whereIn('sender_id', $merchant_ids);
                                        });
                                    })
                                    ->where(function ($query) {
                                        $query->where('origin_city_id', auth()->user()->city_id)
                                            ->orWhere('sender_city_id', auth()->user()->city_id);
                                    })
                                    ->whereNull('outgoing_status')
                                    ->count();
        return response()->json([
            'status' => 1,
            'pending_return_vouchers' => $pendingReturnVouchers,
            'postpone_vouchers' => $postponeVouchers

        ], Response::HTTP_OK);
    }
    public function pinRequest(Request $request)
    {
        $data = $this->smsService->verifyRequest($request->get('phone'));
        return $data;
    }

    public function pinVerify(Request $request)
    {
        $data = $this->smsService->verify($request->get('request_id'),$request->get('code'));
        return $data;

    }
}
