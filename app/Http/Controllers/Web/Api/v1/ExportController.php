<?php

namespace App\Http\Controllers\Web\Api\v1;

use App\Models\Bus;

use Illuminate\Http\Response;
use App\Exports\Api\VouchersExport;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\File;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Storage;

class ExportController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function exportVouchers()
    {
      //  return \Storage::disk('public')->delete('voucher.xlsx');

        $filename = 'voucher.xlsx';
        Excel::store(new VouchersExport, $filename, 'public', null, [
                'visibility' => 'public',
        ]);
        //$file = File::get(storage_path('/app/public/voucher.xlsx'));
        // $response = Response::make($file, 200);
        // $response->header('Content-Type', 'application/xls');
        // $response = Storage::download(storage_path('/app/public/voucher.xlsx'));
        $response =  \Storage::disk('public')->response('voucher.xlsx');
        return $response;


      // return Excel::download(new VouchersExport, $filename);
    }
}
