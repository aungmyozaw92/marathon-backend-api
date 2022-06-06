<?php

namespace App\Exports;

use App\Exports\BusSheet;
use App\Exports\CitySheet;
use App\Exports\GateSheet;
use App\Exports\ZoneSheet;
use App\Exports\RouteSheet;
use App\Exports\StaffSheet;
use App\Exports\AccountSheet;
use App\Exports\VoucherSheet;
use App\Exports\CustomerSheet;
use App\Exports\MerchantSheet;
use App\Exports\BusDropOffSheet;
use App\Exports\BusStationSheet;
use App\Exports\DoorToDoorSheet;
use App\Exports\GlobalScaleSheet;
use App\Exports\ContactAssociateSheet;
use App\Exports\MerchantDiscountSheet;
use App\Exports\MerchantAssociateSheet;
use App\Exports\AccountInformationSheet;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class MainDataExport implements WithMultipleSheets
{
    use Exportable;
    /**
     * @return array
     */
    public function sheets(): array
    {
        $sheets = [];

        // $sheets[] = new AccountSheet();
        // $sheets[] = new CitySheet();
        // $sheets[] = new ZoneSheet();
        // $sheets[] = new RouteSheet();
        // $sheets[] = new GlobalScaleSheet();
        // $sheets[] = new BusSheet();
        // $sheets[] = new BusStationSheet();
        // $sheets[] = new GateSheet();
        // $sheets[] = new DoorToDoorSheet();
        // $sheets[] = new BusDropOffSheet();
        //  $sheets[] = new StaffSheet();
        //  $sheets[] = new MerchantSheet();
        // $sheets[] = new MerchantAssociateSheet();
        // $sheets[] = new ContactAssociateSheet();
        //$sheets[] = new MerchantDiscountSheet();
        //$sheets[] = new CustomerSheet();
        // $sheets[] = new AccountInformationSheet();
        // $sheets[] = new VoucherSheet();

        
        return $sheets;
    }
}
