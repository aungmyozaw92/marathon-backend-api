<?php

use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductsTableSeeder extends Seeder
{

     protected $products = [
        ['A3 Poster(A3-P)x100', '1.9'],
        ['1000 ks top up 10000 ပါ ဂျပ်ဖာသေးx1', '3.8'],
        ['1000 ks top up 50000 ပါ ဂျပ်ဖာကြီးx1', '20'],
        ['Bag Pack(B-P)x1', '0.15'],
        ['Mugx1', '0.32'],
        ['Polo Shirt S+M+L+XL(PSH)x1', '0.3'],
        ['X-Stand (X-SD)x1', '0.74'],
        ['Vinyl  (5x2)(Vy-5x2) Wowx1', '0.25'],
        ['Parasol(PRS)x1', '4.3'],
        ['Umbrella Long(UM-L)x1', '0.5'],
        ['Rain Coad (Yellow) (RC-H)x1', '0.7'],
        ['Rain Coad (White) (RC-W)x1', '0.7'],
        ['Huawei WCDMA(L&D)x500', '1'],
        ['Window Standx1', '2.7'],
        ['Top Up 1000 Ksx50', '0.2'],
        ['Top Up 3000 Ksx50', '0.2'],
        ['Top Up 5000 Ksx50', '0.25'],
        ['Top Up 10000 Ksx50', '0.25'],
        ['Flyer A5 size (Ftth)x100', '0.39'],
        ['Window Standx1', '2.7'],
        ['Note Bookx1', '0.13'],
        ['Normal Beautiful Simx50', '0.25'],
        ['CDMA Similarx50', '0.25'],
        ['CDMA 450 YGN Simx50', '0.1'],
        ['4G Simx500', '1'],
        ['Wave Simx2000', '5.2'],
        ['Wave Simx1000', '2.6'],
        ['Sim Swapx50', '0.1'],
        ['Ball Penx50', '0.4'],
        ['Maskx50', '0.7'],
        ['Black Sim 500 ပါအချောင်း 1 ချောင်းx500', '1'],
        ['Top Up 2000 Per 1 Pcsx2000', '0.65'],
    ];
   
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Schema::disableForeignKeyConstraints();
        foreach ($this->products as $product) {
            factory(Product::class)->create([
                'item_name'    => $product[0],
                'weight' => $product[1],
                'item_price' => 0,

            ]);
        }
        Schema::enableForeignKeyConstraints();
        // Schema::disableForeignKeyConstraints();
        // factory(Product::class, 20)->create();
        // Schema::enableForeignKeyConstraints();
    }
}
