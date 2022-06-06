<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\City;
use App\Models\Voucher;
use App\Jobs\StoreStock;
use \Carbon\Carbon;

class EndofdayCron extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'endofday:cron';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        //
        // \Log::info("Instock Items are Saved by EndofDayCronJob!");
        // City::chunk(50, function ($cities) {
        //     foreach ($cities as $city) {
        // $city->name_mm = $city->name_mm . '_edited';
        // $city->name_mm = str_replace('_edited', '', $city->name_mm);
        // $city->save();
        // dispatch(new StoreStock($city));
        // StoreStock::dispatchNow($city);
        //     }
        // });
        // Voucher::whereNotNull('pickup_id')->where('deleted_at', NULL)->whereDate('created_at', Carbon::today())->Chunk(100, function ($vouchers) {
        //     foreach ($vouchers as $voucher) {
        //         \Log::info($voucher->voucher_invoice);
        //     }
        // });
    }
}
