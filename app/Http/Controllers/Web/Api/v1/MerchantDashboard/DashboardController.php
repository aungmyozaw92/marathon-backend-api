<?php

namespace App\Http\Controllers\Web\Api\v1\MerchantDashboard;

use App\Models\Pickup;
use App\Models\Voucher;
use App\Models\ReturnSheet;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;

class DashboardController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $date = request()->get('date');

        // if ($date) {
        // $totalVouchers = [];
        // $doorToDoorVouchers = [];
        // $waybillVouchers = [];

        $pickups = Pickup::with('vouchers')
            // ->where('sender_type', 'Merchant')
            ->where('sender_type', 'Merchant')
            ->where('sender_id', auth()->user()->id)
            ->where(function ($qr) use ($date) {
                // if ($date) {
                //     $qr->whereDate('created_at', $date);
                // }
                $date ? $qr->whereDate('created_at', $date): $qr;
            })
            ->count();

        $pendingPickups = Pickup::with('vouchers')
            // ->where('sender_type', 'Merchant')
            ->where('sender_type', 'Merchant')
            ->where('sender_id', auth()->user()->id)
            ->where('is_pickuped', false)
            ->where(function ($qr) use ($date) {
                $date ? $qr->whereDate('created_at', $date): $qr;
            })
            ->count();

        $totalVouchers = Voucher::where(function ($qr) use ($date) {
            $date ? $qr->whereDate('created_at', $date): $qr;
        })
            ->whereHas('pickup', function ($qr) {
                // ->where('sender_type', 'Merchant')
                $qr->where('sender_type', 'Merchant')
                    ->where('sender_id', auth()->user()->id);
            })->count();
        
        $draftVouchers = Voucher::where('created_by_id', auth()->user()->id)
                                    ->where('created_by_type', 'Merchant')
                                    ->where('pickup_id', null)
                                    ->where(function ($qr) use ($date) {
                                        $date ? $qr->whereDate('created_at', $date): $qr;
                                    })
                                    ->count();

        $doorToDoorVouchers = Voucher::where('receiver_city_id', getBranchCityId())
            ->where(function ($qr) use ($date) {
                $date ? $qr->whereDate('created_at', $date): $qr;
            })
            ->whereHas('pickup', function ($qr) {
                // ->where('sender_type', 'Merchant')
                $qr->where('sender_type', 'Merchant')
                    ->where('sender_id', auth()->user()->id);
            })
            ->count();

        $waybillVouchers = Voucher::where('receiver_city_id', '!=', getBranchCityId())
            ->where(function ($qr) use ($date) {
                $date ? $qr->whereDate('created_at', $date): $qr;
            })
            ->whereHas('pickup', function ($qr) {
                // ->where('sender_type', 'Merchant')
                $qr->where('sender_type', 'Merchant')
                    ->where('sender_id', auth()->user()->id);
            })
            ->count();

        $deliveringVouchers = Voucher::whereIn('delivery_status_id', [1, 2])
            ->where(function ($qr) use ($date) {
                $date ? $qr->whereDate('created_at', $date): $qr;
            })
            ->whereHas('pickup', function ($qr) {
                // ->where('sender_type', 'Merchant')
                $qr->where('sender_type', 'Merchant')
                    ->where('sender_id', auth()->user()->id);
            })
            ->count();

        $deliveredVouchers = Voucher::where('delivery_status_id', 8)
            ->where(function ($qr) use ($date) {
                $date ? $qr->whereDate('created_at', $date): $qr;
            })
            ->whereHas('pickup', function ($qr) {
                // ->where('sender_type', 'Merchant')
                $qr->where('sender_type', 'Merchant')
                    ->where('sender_id', auth()->user()->id);
            })
            ->count();

        $cannotDeliveredVouchers = Voucher::whereNotIn('delivery_status_id', [1, 2, 8, 9])
            ->where(function ($qr) use ($date) {
                $date ? $qr->whereDate('created_at', $date): $qr;
            })
            ->whereHas('pickup', function ($qr) {
                // ->where('sender_type', 'Merchant')
                $qr->where('sender_type', 'Merchant')
                    ->where('sender_id', auth()->user()->id);
            })
            ->count();

        $returnedVouchers = Voucher::where('is_return', true)
            ->where(function ($qr) use ($date) {
                $date ? $qr->whereDate('created_at', $date): $qr;
            })
            ->whereHas('pickup', function ($qr) {
                // ->where('sender_type', 'Merchant')
                $qr->where('sender_type', 'Merchant')
                    ->where('sender_id', auth()->user()->id);
            })
            ->whereHas('return_sheets', function ($qr) {
                $qr->where('is_returned', true);
            })
            ->count();

        $returningVouchers = Voucher::where('is_return', false)
            ->where(function ($qr) use ($date) {
                $date ? $qr->whereDate('created_at', $date): $qr;
            })
            ->whereHas('pickup', function ($qr) {
                // ->where('sender_type', 'Merchant')
                $qr->where('sender_type', 'Merchant')
                    ->where('sender_id', auth()->user()->id);
            })
            ->whereHas('return_sheets', function ($qr) {
                $qr->where('is_returned', false);
            })
            ->count();

        $pendingReturnVouchers = Voucher::where('is_return', false)
            ->where(function ($qr) use ($date) {
                $date ? $qr->whereDate('created_at', $date): $qr;
            })
            ->where('delivery_status_id', 9)
            ->whereHas('pickup', function ($qr) {
                // ->where('sender_type', 'Merchant')
                $qr->where('sender_type', 'Merchant')
                    ->where('sender_id', auth()->user()->id);
            })
            ->whereDoesntHave('return_sheets')
            ->count();

        $merchantSheetVouchers = Voucher::where(function ($qr) use ($date) {
            $date ? $qr->whereDate('created_at', $date): $qr;
        })
            ->whereHas('pickup', function ($qr) {
                // ->where('sender_type', 'Merchant')
                $qr->where('sender_type', 'Merchant')
                    ->where('sender_id', auth()->user()->id);
            })
            ->whereHas('merchant_sheets', function ($qr) {
                $qr->where('is_paid', false);
            })
            ->count();

        $merchantSheetPaidVouchers = Voucher::where(function ($qr) use ($date) {
            $date ? $qr->whereDate('created_at', $date): $qr;
        })
            ->whereHas('pickup', function ($qr) {
                // ->where('sender_type', 'Merchant')
                $qr->where('sender_type', 'Merchant')
                    ->where('sender_id', auth()->user()->id);
            })
            ->whereHas('merchant_sheets', function ($qr) {
                $qr->where('is_paid', true);
            })
            ->count();

        $returnSheets = ReturnSheet::where(function ($qr) use ($date) {
            $date ? $qr->whereDate('created_at', $date) : $qr;
        })
            ->where('merchant_id', auth()->user()->id)
            ->count();


        // $vouchers = Voucher::with('pickup')->whereDate('created_at', $date)->get();
        // foreach ($vouchers as $voucher) {
        //     if ($voucher->pickup->sender_type == 'Merchant' && $voucher->pickup->sender_id == auth()->user()->id) {
        //         $totalVouchers[] = $voucher;
        //         if ($voucher->receiver_city_id == getBranchCityId()) {
        //             $doorToDoorVouchers[] = $voucher;
        //         } else {
        //             $waybillVouchers[] = $voucher;
        //         }
        //     }
        // }

        return response()->json([
            'status' => 1,
            "total_pickups_count" => $pickups,
            "total_pending_pickups" => $pendingPickups,
            "total_vouchers_count" => $totalVouchers,
            "total_draft_vouchers_count" => $draftVouchers,
            "total_dtd_vouchers_count" => $doorToDoorVouchers,
            "total_waybill_vouchers_count" => $waybillVouchers,
            "total_delivering_vouchers" => $deliveringVouchers,
            "total_delivered_vouchers" => $deliveredVouchers,
            "total_cannot_delivered_vouchers" => $cannotDeliveredVouchers,
            "total_returned_vouchers" => $returnedVouchers,
            "total_returning_vouchers" => $returningVouchers,
            "total_pending_return_vouchers"  => $pendingReturnVouchers,
            "total_merchant_sheetVouchers" => $merchantSheetVouchers,
            "total_merchant_sheet_paid_vouchers" => $merchantSheetPaidVouchers,
            'total_return_sheets_count' => $returnSheets
        ], Response::HTTP_OK);
    }
}
