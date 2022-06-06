<?php

namespace App\Providers;

use App\Models\Tag;
use App\Models\Order;
use App\Models\Store;
use App\Models\Pickup;
use App\Models\Product;
use App\Models\Voucher;
use App\Models\Customer;
use App\Models\Merchant;
use App\Models\Inventory;
use App\Models\ProductTag;
use App\Models\ProductType;
use App\Models\Transaction;
use App\Models\ProductReview;
use App\Models\VariationMeta;
use App\Models\ProductDiscount;
use App\Models\ProductVariation;
use App\Models\MerchantAssociate;
use App\Models\AccountInformation;
use App\Policies\Mobile\TagPolicy;
use App\Policies\Mobile\StorePolicy;
use Illuminate\Support\Facades\Gate;
use App\Policies\Mobile\PickupPolicy;
use App\Policies\Mobile\InventoryPolicy;
use App\Policies\Mobile\ProductTagPolicy;
use App\Policies\ThirdParty\VoucherPolicy;
use App\Policies\Mobile\ProductReviewPolicy;
use App\Policies\Mobile\VariationMetaPolicy;
use App\Policies\Mobile\ProductDiscountPolicy;
use App\Policies\SuperMerchant\MerchantPolicy;
use App\Policies\MerchantDashboard\OrderPolicy;
use App\Policies\Mobile\ProductVariationPolicy;
use App\Policies\Mobile\MerchantAssociatePolicy;
use App\Policies\MerchantDashboard\ProductPolicy;
use App\Policies\Mobile\AccountInformationPolicy;
use App\Policies\MerchantDashboard\CustomerPolicy;
use App\Policies\MerchantDashboard\ProductTypePolicy;
use App\Policies\Mobile\v2\Merchant\TransactionPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        Store::class => StorePolicy::class,
        Pickup::class => PickupPolicy::class,
        AccountInformation::class => AccountInformationPolicy::class,
        MerchantAssociate::class => MerchantAssociatePolicy::class,
       
        Voucher::class => VoucherPolicy::class,
        Merchant::class => MerchantPolicy::class,
        Product::class => ProductPolicy::class,
        ProductType::class => ProductTypePolicy::class,
        Tag::class => TagPolicy::class,
        ProductTag::class => ProductTagPolicy::class,
        Inventory::class => InventoryPolicy::class,
        VariationMeta::class => VariationMetaPolicy::class,
        ProductVariation::class => ProductVariationPolicy::class,
        ProductReview::class => ProductReviewPolicy::class,
        ProductDiscount::class => ProductDiscountPolicy::class,

        Transaction::class => TransactionPolicy::class,
        Customer::class => CustomerPolicy::class,
        Order::class => OrderPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        //
    }
}
