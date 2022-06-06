<?php

use Carbon\Carbon;
use App\Models\Meta;
use Illuminate\Database\Seeder;
use App\Traits\TruncateTableSeeder;

class MetasTableSeeder extends Seeder
{
    use TruncateTableSeeder;

    protected $metas = [
        'branch' => '1',
        'okkar' => '(p#D5&nhNTr5$`Fb',
        'lang' => 'en',
        'currency' => 'Ks',
        'male' => 'U',
        'female' => 'Daw',
        'decimal' => '2',
        'login' => '3',
        'date' => 'dd/mm/yyyy hh:mm a',
        'city' => '1',
        'zone' => '1',
        'pickup_price' => '1000',
        'pickup_min_qty' => '3',
        'scale_unit' => 'in',
        'weight_unit' => 'kg',
        'dropoff_price' => '1000',
        'target_sale' =>	'100',
        'target_coupon' =>	'5',
        'transaction_amount' =>	'100000',
        'transaction_fee' =>	'1000',
        'insurance_fee' =>	'1',
        'agent_fee_base_rate' =>	'1000',
        'warehousing_fee' =>	'200',
        'return_percentage' => '50',
        'delivery_commission' => '200',
        'pickup_commission' => '25',
        'lunch' => '1300'
    ];

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->truncate('metas');

        // DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        // Meta::truncate();
        // DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $this->metas['target_start_date'] = Carbon::now();
        $this->metas['target_end_date']   = Carbon::now()->addMonth();

        foreach ($this->metas as $key => $value) {
            factory(Meta::class)->create([
               'key'   => $key,
               'value' => $value
            ]);
        }
    }
}
