<?php

namespace App\Http\Controllers\Web\Api\v1;

use App\Models\Voucher;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use App\Http\Resources\Customer\CustomerResource;
use App\Http\Resources\Customer\CustomerCollection;
use App\Repositories\Web\Api\v1\CustomerRepository;
use App\Http\Requests\Customer\CreateCustomerRequest;
use App\Http\Requests\Customer\UpdateCustomerRequest;

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
        $this->customerRepository = $customerRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $customers = Customer::filter(request()->only('search'))->get();

        return new CustomerCollection($customers->load(['city', 'zone', 'badge']));
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

        return new CustomerResource($customer->load(['city', 'zone', 'badge']));
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Customer  $customer
     * @return \Illuminate\Http\Response
     */
    public function show(Customer $customer)
    {
        return new CustomerResource($customer->load(['city', 'zone', 'badge']));
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

        return new CustomerResource($customer->load(['city', 'zone', 'badge']));
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

    // public function update_customer(Request $request)
    // {
    //     $phone = $request['phone'];
    //     $customers = Customer::where('phone', 'like', "%{$phone}%")->orderBy('id','asc')->get();
    //     $i = '10';
    //     $customer_id = $customers[0]->id;
    //     foreach ($customers as $key => $c) {
    //         if ($key > 0) {
    //            $c->phone = $c->phone.$i++;
    //            $c->save();
    //            Voucher::where('receiver_id', $c->id)->update(['receiver_id' => $customer_id]);
    //         }
    //         echo $key;
    //         echo '<br>';
    //         echo $i+1;
    //         echo '<br>';
    //     }
    // }
}
