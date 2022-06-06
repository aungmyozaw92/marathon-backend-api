<?php

use App\Models\Qr;
use App\Models\Flag;
use App\Models\Agent;
use App\Models\Staff;
use App\Models\Coupon;
use App\Models\Parcel;
use App\Models\Pickup;
use App\Models\Invoice;
use App\Models\Journal;
use App\Models\Message;
use App\Models\Voucher;
use App\Models\Waybill;
use App\Models\BusSheet;
use App\Models\Customer;
use App\Models\Merchant;
use App\Models\PointLog;
use App\Models\DeliSheet;
use App\Models\HeroPoint;
use App\Models\Inventory;
use App\Models\StaffRole;
use App\Models\AgentSheet;
use App\Models\Attachment;
use App\Models\Attendance;
use App\Models\FinanceTax;
use App\Models\ParcelItem;
use App\Models\BranchSheet;
use App\Models\FinanceCode;
use App\Models\QrAssociate;
use App\Models\ReturnSheet;
use App\Models\TempJournal;
use App\Models\Transaction;
use App\Models\FinanceAsset;
use App\Models\InventoryLog;
use App\Models\CommissionLog;
use App\Models\MerchantSheet;
use App\Models\PickupHistory;
use App\Models\FinanceAccount;
use App\Models\FinanceExpense;
use App\Models\InvoiceHistory;
use App\Models\InvoiceJournal;
use App\Models\VoucherHistory;
use App\Models\WaybillHistory;
use App\Models\WaybillVoucher;
use App\Models\BusSheetVoucher;
use App\Models\CouponAssociate;
use App\Models\TrackingVoucher;
use Illuminate\Database\Seeder;
use App\Models\ContactAssociate;
use App\Models\DeliSheetHistory;
use App\Models\DeliSheetVoucher;
use App\Models\FinanceAssetType;
use App\Models\FinancePettyCash;
use App\Models\MerchantDiscount;
use App\Models\MerchantRateCard;
use App\Models\VoucherAssociate;
use App\Models\FinanceMasterType;
use App\Models\MerchantAssociate;
use App\Models\AccountInformation;
use App\Models\BranchSheetVoucher;
use App\Models\DoorToDoor;
use App\Models\FinanceAccountType;
use App\Models\FinanceAdvance;
use App\Models\FinanceConfig;
use App\Models\FinanceExpenseItem;
use App\Models\FinanceGroup;
use App\Models\FinanceMeta;
use App\Models\FinanceNature;
use App\Models\FinancePaymentOption;
use App\Models\FinancePettyCashItem;
use App\Models\FinancePosting;
use App\Models\FinanceTableOfAuthority;
use App\Models\ReturnSheetHistory;
use App\Models\ReturnSheetVoucher;
use App\Models\MerchantSheetHistory;
use App\Models\MerchantSheetVoucher;
use App\Models\Product;
use App\Models\ProductDiscount;
use App\Models\ProductReview;
use App\Models\ProductTag;
use App\Models\ProductType;
use App\Models\ProductVariation;
use App\Models\Route;
use Illuminate\Support\Facades\Schema;

class TruncateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Schema::disableForeignKeyConstraints();
        // // AccountInformation::truncate();

        // // StaffRole::truncate();
        // // Staff::truncate();
        // // Agent::truncate();

        // BusSheet::truncate();
        // AgentSheet::truncate();
        // Attachment::truncate();
        // Attendance::truncate();

        // BranchSheet::truncate();
        // BranchSheetVoucher::truncate();
        // BusSheet::truncate();
        // BusSheetVoucher::truncate();
        // CommissionLog::truncate();
        
        // CouponAssociate::truncate();
        // Coupon::truncate();
        // Customer::truncate();

        // DeliSheetHistory::truncate();
        // DeliSheetVoucher::truncate();
        // DeliSheet::truncate();
        // HeroPoint::truncate();
        // Flag::truncate();
        // Inventory::truncate();
        // InventoryLog::truncate();
        // InvoiceHistory::truncate();
        // InvoiceJournal::truncate();
        // Invoice::truncate();
        // Journal::truncate();
        // // ContactAssociate::truncate();
        // // MerchantDiscount::truncate();
        // // MerchantRateCard::truncate();
        // // MerchantAssociate::truncate();
        // // Merchant::truncate();

        // MerchantSheetHistory::truncate();
        // MerchantSheetVoucher::truncate();
        // MerchantSheet::truncate();
        // Message::truncate();
        
        // ParcelItem::truncate();
        // Parcel::truncate();
        // PickupHistory::truncate();
        // Pickup::truncate();
        // PointLog::truncate();

        // QrAssociate::truncate();
        // Qr::truncate();
        // ReturnSheetHistory::truncate();
        // ReturnSheetVoucher::truncate();
        // ReturnSheet::truncate();

        
        // TempJournal::truncate();
        // TrackingVoucher::truncate();
        // Transaction::truncate();
        // VoucherAssociate::truncate();
        // VoucherHistory::truncate();
        // Voucher::truncate();
        
        // WaybillHistory::truncate();
        // WaybillVoucher::truncate();
        // Waybill::truncate();
        
        // FinanceAccountType::truncate();
        // FinanceAccount::truncate();
        // FinanceTax::truncate();
        // FinanceCode::truncate();
        // FinanceAssetType::truncate();
        // FinanceExpense::truncate();
        // FinancePettyCashItem::truncate();
        // FinanceMasterType::truncate();
        // FinanceExpenseItem::truncate();
        // FinanceExpense::truncate();
        // FinanceNature::truncate();
        // FinanceMeta::truncate();
        // FinancePaymentOption::truncate();
        // FinancePettyCash::truncate();
        // FinancePosting::truncate();
        // FinanceTableOfAuthority::truncate();
        // FinanceConfig::truncate();
        // FinanceAdvance::truncate();
        // FinanceGroup::truncate();

        // // ProductType::truncate();
        // // ProductDiscount::truncate();
        // // ProductTag::truncate();
        // // ProductVariation::truncate();
        // // ProductReview::truncate();
        // // Product::truncate();

        // // Route::truncate();
        // // DoorToDoor::truncate();

        // Schema::enableForeignKeyConstraints();
    }
}
