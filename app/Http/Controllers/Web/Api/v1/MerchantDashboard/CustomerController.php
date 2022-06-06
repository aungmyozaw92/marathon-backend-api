<?php

namespace App\Http\Controllers\Web\Api\v1\MerchantDashboard;

use App\Models\Voucher;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\MerchantCustomer;
use App\Http\Controllers\Controller;
use App\Http\Resources\Mobile\Voucher\VoucherCollection;
use App\Http\Resources\MerchantDashboard\Customer\CustomerResource;
use App\Http\Resources\MerchantDashboard\Customer\CustomerCollection;
use App\Repositories\Web\Api\v1\MerchantDashboard\CustomerRepository;
use App\Http\Requests\MerchantDashboard\Customer\CreateCustomerRequest;
use App\Http\Requests\MerchantDashboard\Customer\UpdateCustomerRequest;

class CustomerController extends Controller
{
    /**
     * @var CustomerRepository
     */
    protected $customerRepository;

    /**
     * CustomerController constructor.
     *
     * @param CustomerRepository $customerRepository
     */
    public function __construct(CustomerRepository $customerRepository)
    {
        $this->middleware('can:view,customer')->only('show');
        $this->middleware('can:view_voucher,customer')->only('get_vouchers');
        $this->middleware('can:delete,customer')->only('destroy');
        $this->customerRepository = $customerRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $merchant = auth()->user();
        
        $customers = Customer::with(['city', 'zone'])
                            ->whereHas('merchants', function ($query) use($merchant) {
                                    $query->where('merchant_id',$merchant->id);
                            })
                            ->filterMerchant(request()->only(['search','name','phone','address','city_id','zone_id']));
        if (request()->has('paginate')) {
            $customers = $customers->paginate((request()->get('paginate') ? request()->get('paginate') : 25));
        } else {
            $customers = $customers->get();
        }

        return new CustomerCollection($customers);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CreateCustomerRequest $request)
    {
        $customer = $this->customerRepository->create($request->all());

        return new CustomerResource($customer->load(['city', 'zone']));
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Customer  $customer
     * @return \Illuminate\Http\Response
     */
    public function show(Customer $customer)
    {
        return new CustomerResource($customer->load(['city', 'zone']));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Customer  $customer
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateCustomerRequest $request, Customer $customer)
    {
        $customer = $this->customerRepository->update($customer, $request->all());

        return new CustomerResource($customer->load(['city', 'zone']));
    }

     /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Customer  $customer
     * @return \Illuminate\Http\Response
     */
    public function destroy(Customer $customer)
    {
        $this->customerRepository->destroy($customer);

        return response()->json(['status' => 1], Response::HTTP_OK);
    }

    public function get_vouchers(Customer $customer)
    {
        $vouchers = Voucher::with(['sender_city','receiver_city','sender_zone','receiver_zone',
                                    'delivery_status','payment_type'])
                            ->where('receiver_id',$customer->id);
        if (request()->has('paginate')) {
            $vouchers = $vouchers->paginate((request()->get('paginate') ? request()->get('paginate') : 25));
        } else {
            $vouchers = $vouchers->get();
        }
        return new VoucherCollection($vouchers);
    }

    
}
