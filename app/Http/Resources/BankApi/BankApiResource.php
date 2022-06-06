<?php

namespace App\Http\Resources\BankApi;

use App\Models\Voucher;
use Illuminate\Http\Resources\Json\JsonResource;

class BankApiResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $voucher = Voucher::orderBy('id','DESC')->first();
        $InvoiceNo = $voucher->voucher_invoice;
        
        $QRUniqueId = $this['QRUniqueId'];
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

        // $HashValue = sha1('MARATHON MYANMARUABMM202028160428256841209saisaipay1delivery1DEF11DB87433EB9F33394AE98E3E8Z1');
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
    }

    /**
     * Get additional data that should be returned with the resource array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    // public function with($request)
    // {
    //     return [
    //         'status' => 1,
    //     ];
    // }
}
