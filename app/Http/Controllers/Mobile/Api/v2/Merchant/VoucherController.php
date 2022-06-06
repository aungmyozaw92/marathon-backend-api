<?php

namespace App\Http\Controllers\Mobile\Api\v2\Merchant;

use App\Models\City;
use App\Models\Voucher;
use App\Models\QrAssociate;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use App\Repositories\Mobile\Api\v1\QrRepository;
use App\Repositories\Mobile\Api\v2\Merchant\VoucherRepository;
use App\Http\Resources\Mobile\v2\Merchant\Voucher\VoucherCollection;
use App\Http\Requests\Mobile\v2\Voucher\CreateVoucherRequest;
use App\Http\Requests\Mobile\v2\Voucher\UpdateVoucherRequest;
use App\Http\Resources\Mobile\v2\Merchant\Voucher\VoucherResource;
use Validator;

//use App\Http\Requests\Voucher\UpdateVoucherRequest;

class VoucherController extends Controller
{
    protected $voucherRepository;

    public function __construct(
        VoucherRepository $voucherRepository,
        QrRepository $qrRepository
    ) {
        $this->voucherRepository = $voucherRepository;
        $this->qrRepository = $qrRepository;
    }

	public function draftVouchers()
	{
		$vouchers = Voucher::where('created_by_id', auth()->user()->id)
			->where('created_by_type', 'Merchant')
			->where('is_complete',true)
			->where('pickup_id', null)
			->orderBy('id', 'desc')
			->with(merchantAppCompleteVoucherList())
			->paginate(25);
		return new VoucherCollection($vouchers);
	}

	public function deliveringVouchers()
	{
		$requested_status = \Request::segments()[4];
		$vouchers = Voucher::whereHas('pickup', function ($q) {
			$q->where('sender_type', 'Merchant')
				->where('sender_id', auth()->user()->id);
			})
			->where(function($query) use($requested_status) {
				if ($requested_status == 'delivering_vouchers') {
					$query->whereIn('store_status_id', [1,4,5])
						->orWhere(function($q) {
							$q->where([['store_status_id', 2],['delivery_counter', 0]]);
						});
				}else{
					$query->where('delivery_status_id', 8)
						->where('is_closed',1)
						->whereNotNull('end_date');
				}
			})
			->with(merchantAppCompleteVoucherList())
			->orderBy('id', 'desc')
			->paginate(25);
		return new VoucherCollection($vouchers);
	}

	public function failedAttemptVouchers()
	{
		$requested_status = \Request::segments()[4];
		$vouchers = Voucher::whereHas('pickup', function ($q) {
			$q->where('sender_type', 'Merchant')
				->where('sender_id', auth()->user()->id);
			})
			->where(function ($query) use ($requested_status) {
				if ($requested_status == 'failed_attempt_vouchers') {
					$query->whereNotIn('delivery_status_id', [8, 9])
						->where('delivery_counter','>',0)
						->whereNull('outgoing_status');
				} else {
					$query->where('delivery_status_id','!=', 8)
						->where('outgoing_status', 0)
						->where(function($query) {
							$query->where('is_manual_return',1)
								->orWhere('postpone_date','!=',null);
						});
				}
			})
			->with(merchantAppCompleteVoucherList())
			->orderBy('id', 'desc')
			->paginate(25);
		return new VoucherCollection($vouchers);
	}

    public function store(CreateVoucherRequest $request)
    {
        if ($request->get('qr_code')) {
            $response = $this->qrRepository->checkQrCode($request->get('qr_code'));
            if ($response['status'] === 2) {
                return response()->json(['status' => $response['status'], 'message' => $response['message']]);
            }
        }
        $city = City::findOrFail($request->input('receiver_city_id'));
        if (!$city->is_available_d2d) {
            return response()->json(['status' => 2, 'message' => $city->name . ' city is not available']);
        }
        $request['platform'] = 'Merchant App';
        $voucher = $this->voucherRepository->create($request->all());
        if ($voucher) {
            // create and bind
            if ($request->get('qr_code')) {
                $qr_associate = QrAssociate::where('qr_code', $request->get('qr_code'))->first();
                $qr_bind = $this->qrRepository->bindQR($voucher, $qr_associate);
            }
			// return response()->json(['status' => 1, 'message' => 'Successfully Created!', 'data' => $voucher->id], Response::HTTP_OK);
			return new VoucherResource($voucher->load($voucher->merchant_app_voucher_detail));
        } else {
            return response()->json(['status' => 5, 'message' => 'Data integraty check again!']);
        }
    }

	public function show(Voucher $voucher) {
		// return new VoucherResource($voucher->load([
		// 	'customer:id,name,phone,other_phone,address',
		// 	'receiver_city:id,name,name_mm',
		// 	'receiver_zone:id,name,name_mm,is_deliver',
		// 	'delivery_status:id,status,status_mm',
		// 	'payment_type:id,name,name_mm',
		// 	'parcels','parcels.parcel_items','parcels.parcel_items.product','parcels.parcel_items.product.attachment'
		// ]));
		return new VoucherResource($voucher->load($voucher->merchant_app_voucher_detail));
	}

    public function update(UpdateVoucherRequest $request, Voucher $voucher)
    {
        $city = City::findOrFail($request->input('receiver_city_id'));
        if (!$city->is_available_d2d) {
            return response()->json(['status' => 2, 'message' => $city->name . ' city is not available']);
        }
        if ($voucher->is_closed) {
            $remark = request()->only(['remark']);
            if (isset($remark['remark']) && $remark['remark']) {
                $voucher = $this->voucherRepository->update_note($voucher, request()->only(['remark']));
                return response()->json(['status' => 1, "message" => "Successful updated note"]);
            } else {
                return response()->json(['status' => 2, "message" => "Cannot update voucher is already closed or return"]);
            }
        } else {
            $voucher = $this->voucherRepository->update($voucher, $request->all());
            if ($voucher) {
				// return response()->json(['status' => 1, 'message' => 'Successfully Updated!'], Response::HTTP_OK);
				return new VoucherResource($voucher->load($voucher->merchant_app_voucher_detail));
            } else {
                return response()->json(['status' => 5, 'message' => 'Data integraty check again!']);
            }
        }
    }

    public function return_it_back(Voucher $voucher)
    {
        $user = auth()->user();

        if ($voucher->delivery_status_id == 9) {
            return response()->json(['status' => 2, "message" => "Voucher is already requested to return"]);
        }

        if ($voucher->is_closed || $voucher->is_return) {
            return response()->json(['status' => 2, "message" => "Voucher is already closed or returned"]);
        }

        // if ($voucher->origin_city_id != $user->city_id) {
        //     return response()->json(['status' => 2, "message" => "Cannot request to return"]);
        // }

        $delisheet_voucher = $voucher->delisheets()->latest()->first();
        if ($delisheet_voucher) {
            if (!$delisheet_voucher->is_closed) {
                return response()->json(['status' => 2, "message" => "Cannot request to return coz voucher is already assigned in delisheet"]);
            } else {
                if (!$delisheet_voucher->deli_sheet_vouchers->cant_deliver) {
                    return response()->json(['status' => 2, "message" => "Cannot request to return coz voucher is not can't deliver in delisheet."]);
                }
            }
        }

        $waybill_voucher = $voucher->waybills()->latest()->first();
        if ($waybill_voucher) {
            if (!$waybill_voucher->is_closed || !$waybill_voucher->is_received) {
                return response()->json(['status' => 2, "message" => "Cannot return coz voucher is already assigned in Waybill."]);
            }
        }
        $voucher = $this->voucherRepository->return($voucher);
        return response()->json(['status' => 1, 'message' => 'Successfully requested to return it!'], Response::HTTP_OK);
    }

    public function postpone_it(Request $request, Voucher $voucher)
    {
        $validator = Validator::make($request->all(), ['postpone_date' => 'required|after_or_equal:today|date_format:Y-m-d']);
        if ($validator->fails()) {
            return response()->json(['status' => 2, 'message' => $validator->messages()]);
        }
        $user = auth()->user();
        if ($voucher->delivery_status_id == 9) {
            return response()->json(['status' => 2, "message" => "Voucher is already requested to return"]);
        }
        if ($voucher->is_closed || $voucher->is_return) {
            return response()->json(['status' => 2, "message" => "Voucher is already closed or returned"]);
        }
        $delisheet_voucher = $voucher->delisheets()->latest()->first();
        if ($delisheet_voucher) {
            if (!$delisheet_voucher->is_closed) {
                return response()->json(['status' => 2, "message" => "Cannot postpone coz voucher is already assigned in delisheet"]);
            } else {
                if (!$delisheet_voucher->deli_sheet_vouchers->cant_deliver) {
                    return response()->json(['status' => 2, "message" => "Cannot postpone coz voucher is not can't deliver in delisheet."]);
                }
            }
        }
        $waybill_voucher = $voucher->waybills()->latest()->first();
        if ($waybill_voucher) {
            if (!$waybill_voucher->is_closed || !$waybill_voucher->is_received) {
                return response()->json(['status' => 2, "message" => "Cannot postpone coz voucher is already assigned in Waybill."]);
            }
        }
        $voucher = $this->voucherRepository->postpone($request->all(), $voucher);
        return response()->json(['status' => 1, 'message' => 'Successfully requested to postpone to ' . $request->postpone_date . '!'], Response::HTTP_OK);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Voucher  $voucher
     * @return \Illuminate\Http\Response
     */
    public function destroy(Voucher $voucher)
    {
        $this->voucherRepository->destroy($voucher);

        return response()->json(['status' => 1, 'message' => 'Successfully Deleted!'], Response::HTTP_OK);
    }
}
