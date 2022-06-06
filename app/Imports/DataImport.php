<?php

namespace App\Imports;

use Maatwebsite\Excel\Concerns\SkipsUnknownSheets;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class DataImport implements WithMultipleSheets, SkipsUnknownSheets
{
    public function sheets(): array
    {
        // $request = request()->all();
        
        return [
            'Agent' => new Sheets\AgentSheetImport(),
            'Staff' => new Sheets\StaffSheetImport(),
            // 'GlobalScale' => new Sheets\GlobalScaleSheetImport(),
            'City' => new Sheets\CitySheetImport(),
            'Zone' => new Sheets\ZoneSheetImport(),
            'Route' => new Sheets\RouteSheetImport(),
            'DoorToDoor' => new Sheets\DoorToDoorSheetImport(),
            'Bus' => new Sheets\BusSheetImport(),
            'BusStation' => new Sheets\BusStationSheetImport(),
            'Gate' => new Sheets\GateSheetImport(),
            
            // 'Merchant' => new Sheets\MerchantSheetImport(),
            // 'MerchantAssociate' => new Sheets\MerchantAssociateSheetImport(),
            // 'ContactAssociate' => new Sheets\ContactAssociateSheetImport(),
            'MerchantDiscount' => new Sheets\MerchantDiscountSheetImport(),
            'MerchantRateCard' => new Sheets\MerchantRateCardSheetImport(),

            'Product' => new Sheets\ProductSheetImport(),
            
            // 'Customer' => new Sheets\CustomerSheetImport(),
            // 'AccountInformation' => new Sheets\AccountInformationSheetImport(),
            //'CityName' => new Sheets\CityNameSheetImport(),
        ];
    }

    public function onUnknownSheet($sheetName)
    {
        // E.g. you can log that a sheet was not found.
        info("Sheet {$sheetName} was skipped");
    }
}
