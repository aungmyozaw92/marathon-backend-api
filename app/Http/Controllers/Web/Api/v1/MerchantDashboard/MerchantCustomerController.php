<?php

namespace App\Http\Controllers\Web\Api\v1\MerchantDashboard;

use Illuminate\Http\Request;
use App\Models\MerchantCustomer;
use App\Http\Controllers\Controller;
use App\Http\Resources\MerchantDashboard\MerchantCustomer\MerchantCustomerCollection;

class MerchantCustomerController extends Controller
{
    // protected $merchantRepository;

    // public function __construct(MerchantRepository $merchantRepository)
    // {
    //     $this->merchantRepository = $merchantRepository;
    // }

    public function index()
    {
        $merchants = MerchantCustomer::where('merchant_id',auth()->user()->id)
                                    ->with('customer')
                                    ->filter(request()->only(['search']))
                                    ->get();

        // dd($merchants);
         return new MerchantCustomerCollection($merchants);
    }

    public function store(Request $request)
    {
        
    }

    public function show(MerchantCustomer $merchant)
    {
        
    }

    public function update(Request $request, MerchantCustomer $merchant)
    {
        
    }

    public function destroy(MerchantCustomer $merchant)
    {
       
    }

   
}
