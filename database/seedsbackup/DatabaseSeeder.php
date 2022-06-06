<?php

use Illuminate\Database\Seeder;
use App\Models\PaymentType;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run()
    {
        $this->call([
            // PermissionTableSeeder::class,
            UsersTableSeeder::class,
            DepartmentsTableSeeder::class,
            CitiesTableSeeder::class,
            ZonesTableSeeder::class,
            CourierTypesTableSeeder::class,
            RolesTableSeeder::class,
            StaffRolesTableSeeder::class,
            CallStatusesTableSeeder::class,
            DeliveryStatusesTableSeeder::class,
            StoreStatusesTableSeeder::class,
            MetasTableSeeder::class,
            BusStationsTableSeeder::class,
            MerchantsTableSeeder::class,
            GatesTableSeeder::class,
            FlagsTableSeeder::class,
            BadgesTableSeeder::class,
            CustomersTableSeeder::class,
            FlaggedCustomersTableSeeder::class,
            LogStatusesTableSeeder::class,
            MerchantAssociatesTableSeeder::class,
            ContactAssociatesTableSeeder::class,
            StaffsTableSeeder::class,
            // PickupsTableSeeder::class,
            DiscountTypesTableSeeder::class,
            MerchantDiscountsTableSeeder::class,
            GlobalScalesTableSeeder::class,
            PaymentTypesTableSeeder::class,
            StoresTableSeeder::class,
            CouponsTableSeeder::class,
            CouponAssociatesTableSeeder::class,
            PaymentStatusesTableSeeder::class,
            RoutesTableSeeder::class,
            AccountsTableSeeder::class,
            DoorToDoorsTableSeeder::class,
            BusesTableSeeder::class,
            DelegateDurationsTableSeeder::class,
            AgentsTableSeeder::class,
            TrackingStatusesTableSeeder::class,

            BusDropOffsTableSeeder::class
        ]);
    }
}
