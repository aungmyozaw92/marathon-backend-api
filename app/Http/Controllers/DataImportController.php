<?php

namespace App\Http\Controllers;

use App\Imports\BDImport;
use App\Imports\DataImport;
use Illuminate\Http\Request;
use App\Exports\MainDataExport;
use App\Http\Controllers\Controller;
use Maatwebsite\Excel\Facades\Excel;

class DataImportController extends Controller
{
    


    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function import()
    {
        ini_set('max_execution_time', 600);

        Excel::import(new DataImport, request()->file('file'));
           
        return redirect('home')->with('message', 'Successful');
    }

    public function importBusDropOff()
    {
        ini_set('max_execution_time', 600);

        Excel::import(new BDImport, request()->file('file'));
           
        return redirect('home')->with('message', 'Yay Bus Drop off data!');
    }

    // public function importDoorToDoor()
    // {
    //     set_time_limit(0);

    //     Excel::import(new DoorToDoorImport, request()->file('file'));
           
    //     dd('Yay  DTD!!');
    // }

    public function export()
    {
        return Excel::download(new MainDataExport, 'MainData.xlsx');
    }
}
