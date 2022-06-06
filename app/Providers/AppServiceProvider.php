<?php

namespace App\Providers;

use App\Models\Gate;
use App\Models\Zone;
use App\Models\Agent;
use App\Models\Staff;
use App\Models\Branch;
use App\Models\Pickup;
use App\Models\Account;
use App\Models\Invoice;
use App\Models\Journal;
use App\Models\Product;
use App\Models\Voucher;
use App\Models\Waybill;
use App\Models\Customer;
use App\Models\Delivery;
use App\Models\Merchant;
use App\Models\PointLog;
use App\Models\Deduction;
use App\Models\DeliSheet;
use App\Models\Operation;
use App\Models\Attachment;
use App\Models\ReturnSheet;
use App\Models\TempJournal;
use App\Models\Transaction;
use App\Models\ProductReview;
use App\Models\FinanceAccount;
use App\Models\FinanceAdvance;
use App\Models\FinanceExpense;
use App\Models\FinancePosting;
use App\Models\InvoiceJournal;
use App\Models\MerchantAssociate;
use App\Models\FinanceExpenseItem;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use Laravel\Telescope\TelescopeServiceProvider;
use Illuminate\Database\Eloquent\Relations\Relation;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Schema::defaultStringLength(191);

        /**
         * Laravel Telescope
         */
        if ($this->app->isLocal()  || $this->app->environment() == 'staging') {
            $this->app->register(TelescopeServiceProvider::class);
        }

        /**
         * Store custom sender type in pickup tables
         */
        Relation::morphMap([
            'Merchant' => Merchant::class,
            'Customer' => Customer::class,
            'Staff' => Staff::class,
            'Zone' => Zone::class,
            'Gate' => Gate::class,
            'Voucher' => Voucher::class,
            'Pickup' => Pickup::class,
            'Attachment' => Attachment::class,
            'Waybill' => Waybill::class,
            'ReturnSheet' => ReturnSheet::class,
            'Agent' => Agent::class,
            'MerchantAssociate' => MerchantAssociate::class,
            'Branch' => Branch::class,
            'Account' => Account::class,
            'Transaction' => Transaction::class,
            'Operation' => Operation::class,
            'Delivery' => Delivery::class,
            'DeliSheet' => DeliSheet::class,
            'Journal' => Journal::class,
            'Deduction' => Deduction::class,
            'PointLog'  => PointLog::class,
            'Product'  => Product::class,
            'ProductReview'  => ProductReview::class,
            'FinanceExpense' => FinanceExpense::class,
            'FinanceExpenseItem' => FinanceExpenseItem::class,
            'FinanceAdvance' => FinanceAdvance::class,
            'FinancePosting' => FinancePosting::class,
            'FinanceAccount' => FinanceAccount::class,
            'TempJournal' => TempJournal::class,
            'InvoiceJournal' => InvoiceJournal::class,
            'Invoice' => Invoice::class
        ]);
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
