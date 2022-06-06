<?php

namespace App\Providers;

use App\Models\Order;
use App\Models\Parcel;
use App\Models\Pickup;
use App\Models\Account;
use App\Models\Invoice;
use App\Models\Journal;
use App\Models\Voucher;
use App\Models\Waybill;
use App\Models\BusSheet;
use App\Models\Customer;
use App\Models\Merchant;
use App\Models\DeliSheet;
use App\Models\Inventory;
use App\Models\ParcelItem;
use App\Models\ReturnSheet;
use App\Models\TempJournal;
use App\Models\Transaction;
use App\Models\FinanceAsset;
use App\Models\MerchantSheet;
use App\Models\FinanceAdvance;
use App\Models\FinanceExpense;
use App\Models\FinancePosting;
use App\Models\FinancePettyCash;
use App\Observers\OrderObserver;
use App\Observers\ParcelObserver;
use App\Observers\PickupObserver;
use App\Models\FinanceExpenseItem;
use App\Observers\AccountObserver;
use App\Observers\InvoiceObserver;
use App\Observers\JournalObserver;
use App\Observers\VoucherObserver;
use App\Observers\WaybillObserver;
use App\Observers\BusSheetObserver;
use App\Observers\CustomerObserver;
use App\Observers\MerchantObserver;
use App\Models\FinancePettyCashItem;
use App\Observers\DeliSheetObserver;
use App\Observers\InventoryObserver;
use App\Observers\ParcelItemObserver;
use App\Observers\ReturnSheetObserver;
use App\Observers\TempJournalObserver;
use App\Observers\TransactionObserver;
use App\Observers\FinanceAssetObserver;
use Illuminate\Support\ServiceProvider;
use App\Observers\MerchantSheetObserver;
use App\Observers\FinanceAdvanceObserver;
use App\Observers\FinanceExpenseObserver;
use App\Observers\FinancePostingObserver;
use App\Observers\FinancePettyCashObserver;
use App\Observers\FinanceExpenseItemObserver;
use App\Observers\FinancePettyCashItemObserver;

class ObserverServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Pickup::observe(PickupObserver::class);
        Customer::observe(CustomerObserver::class);
        Voucher::observe(VoucherObserver::class);
        Parcel::observe(ParcelObserver::class);
        ParcelItem::observe(ParcelItemObserver::class);
        DeliSheet::observe(DeliSheetObserver::class);
        Waybill::observe(WaybillObserver::class);
        BusSheet::observe(BusSheetObserver::class);
        Account::observe(AccountObserver::class);
        Journal::observe(JournalObserver::class);
        MerchantSheet::observe(MerchantSheetObserver::class);
        ReturnSheet::observe(ReturnSheetObserver::class);
        Merchant::observe(MerchantObserver::class);
        Inventory::observe(InventoryObserver::class);
        FinanceAsset::observe(FinanceAssetObserver::class);
        FinanceExpense::observe(FinanceExpenseObserver::class);
        FinanceExpenseItem::observe(FinanceExpenseItemObserver::class);
        FinancePettyCash::observe(FinancePettyCashObserver::class);
        FinancePettyCashItem::observe(FinancePettyCashItemObserver::class);
        FinanceAdvance::observe(FinanceAdvanceObserver::class);
        FinancePosting::observe(FinancePostingObserver::class);
        TempJournal::observe(TempJournalObserver::class);
        Invoice::observe(InvoiceObserver::class);
		Transaction::observe(TransactionObserver::class);
		Order::observe(OrderObserver::class);
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
