<?php

namespace App\Http\Controllers\Web\Api\v1;

use App\Models\Branch;
use App\Models\Journal;
use App\Models\Voucher;
use App\Http\Controllers\Controller;
use App\Http\Resources\BranchSheetVoucher\BranchSheetVoucherCollection;

class BranchSheetVoucherController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $branch = Branch::findOrFail(request()->get('branch_id'));
        $vouchers = Voucher::branchSheet($branch)->get();

        return new BranchSheetVoucherCollection($vouchers->load([
             'customer', 'call_status','delivery_status', 'store_status',  'payment_type'
        ]));
    }
}
