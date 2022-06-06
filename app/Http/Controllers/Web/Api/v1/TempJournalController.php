<?php

namespace App\Http\Controllers\Web\Api\v1;

use App\Models\Voucher;
use App\Models\TempJournal;
use App\Http\Controllers\Controller;
use App\Http\Resources\TempJournal\TempJournalCollection;

class TempJournalController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (request()->has('paginate')) {
            $temp_journals = TempJournal::filter(request()->only([
                    'start_date', 'end_date'
                ]))
                ->where('merchant_id', request()->get('merchant_id'))
                ->where('city_id', auth()->user()->id)
                ->paginate(25);

            return new TempJournalCollection($temp_journals);
        }

        $vouchers = TempJournal::merchantSheet(request()->get('merchant_id'))->get();

        return new TempJournalCollection($vouchers);
    }
}
