<?php

use App\Models\PaymentType;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run()
    {
        $this->call([
            // FailureStatusesTableSeeder::class,
            // PermissionTableSeeder::class,
            // MetasTableSeeder::class,
            // UsersTableSeeder::class,
            // DepartmentsTableSeeder::class,
            // CitiesTableSeeder::class,
            // ZonesTableSeeder::class,
            // RoutesTableSeeder::class,
            // BusesTableSeeder::class,
            // BusStationsTableSeeder::class,
            // GatesTableSeeder::class,
            // CourierTypesTableSeeder::class,
            // RolesTableSeeder::class,

            // CallStatusesTableSeeder::class,
            // DeliveryStatusesTableSeeder::class,
            // StoreStatusesTableSeeder::class,

            // MerchantsTableSeeder::class,
            // MerchantAssociatesTableSeeder::class,
            // ContactAssociatesTableSeeder::class,

            // FlagsTableSeeder::class,
            // BadgesTableSeeder::class,
            // CustomersTableSeeder::class,
            // FlaggedCustomersTableSeeder::class,
            // LogStatusesTableSeeder::class,

            //StaffsTableSeeder::class,
            //StaffRolesTableSeeder::class,
            // PickupsTableSeeder::class,
            // DiscountTypesTableSeeder::class,
            //MerchantDiscountsTableSeeder::class,
            //GlobalScalesTableSeeder::class,
            // PaymentTypesTableSeeder::class,
            //StoresTableSeeder::class,
            // CouponsTableSeeder::class,
            // CouponAssociatesTableSeeder::class,
            // PaymentStatusesTableSeeder::class,
            // AccountsTableSeeder::class,
            // DelegateDurationsTableSeeder::class,
            //AgentsTableSeeder::class,
            // TrackingStatusesTableSeeder::class,
            //DoorToDoorsTableSeeder::class,
            //BusDropOffsTableSeeder::class,
            // AgentBadgesTableSeeder::class,
            HeroBadgesTableSeeder::class,
            HeroPointsTableSeeder::class,
            DeductionsTableSeeder::class
        ]);
    }
}
