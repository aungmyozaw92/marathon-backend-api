<?php

namespace App\Http\Controllers\Web\Api\v1;

use App\Models\Voucher;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use App\Http\Resources\BankApi\BankApiResource;

class BankApiController extends Controller
{
    // /**
    //  * @var AccountRepository
    //  */
    // protected $accountRepository;

    // /**
    //  * AccountController constructor.
    //  *
    //  * @param AccountRepository $accountRepository
    //  */
    // public function __construct(AccountRepository $accountRepository)
    // {
    //     $this->accountRepository = $accountRepository;
    // }

    

    public function ResponsePaymentAPI(Request $request)
    {
        $voucher = Voucher::orderBy('id','DESC')->first();
        $InvoiceNo = $voucher->voucher_invoice;
        
        $QRUniqueId = $request['QRUniqueId'];
        $DataType = 'Data';
        $ConfrimationUrl = 'https://www.marathonmyanmar.com';
  
        $items = $voucher->parcels[0]->parcel_items;
        $ItemListJsonStr = [];
        foreach ($items as $item) {
            $i['ItemId'] = $item->id;
            $i['Quantity'] = $item->item_qty;
            $i['EachPrice'] = $item->item_price;
            array_push($ItemListJsonStr, $i);
        }

        $ItemListJsonStr = json_encode($ItemListJsonStr);
        $RespCode = '000';
        $RespDescription = 'Success';
        // dd($RespCode.$RespDescription.
        // $QRUniqueId . 
        // $InvoiceNo. 
        // $ItemListJsonStr . $DataType . $ConfrimationUrl);
        $HashValue = sha1($RespCode.$RespDescription.
                    $QRUniqueId . 
                    $InvoiceNo. 
                    $ItemListJsonStr . $DataType . $ConfrimationUrl);

        return [
            'QRUniqueId'    => $QRUniqueId,
            'InvoiceNo'  => $InvoiceNo,
            'DataType'  => $DataType,
            'ConfrimationUrl'  => $ConfrimationUrl,
            'ItemListJsonStr'  => $ItemListJsonStr,
            'RespDescription'  => $RespDescription,
            'RespCode'  => $RespCode,
            'HashValue'  => strtoupper($HashValue),
        ];
        return new BankApiResource($request->all());
        return response()->json([
            'status' => 1,
            'data' => $request->all(),
        ], Response::HTTP_OK);

    }
}
