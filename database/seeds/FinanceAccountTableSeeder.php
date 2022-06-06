<?php

use App\Models\Branch;
use App\Models\FinanceCode;
use App\Models\FinanceGroup;
use App\Models\FinanceNature;
use App\Models\FinanceAccount;
use Illuminate\Database\Seeder;
use App\Models\FinanceMasterType;
use App\Models\FinanceAccountType;
use Illuminate\Support\Facades\Schema;

class FinanceAccountTableSeeder extends Seeder
{
    protected $data = [
        
        ['code' => '100000', 'name' => 'Non Current Assets','description' => '-', 'nature' => 'Assets', 'group' => 'Non Current Assets', 'type' => 'Balance Sheet', 'master_type' => 'General Ledger', 'tax_id' => 1,],
        ['code' => '110100', 'name' => 'Land','description' => 'Land', 'nature' => 'Assets', 'group' => 'Non Current Assets', 'type' => 'Balance Sheet', 'master_type' => 'General Ledger', 'tax_id' => 1,],
        ['code' => '110200', 'name' => 'Acc; Depreciation of Land','description' => 'Acc; Depreciation of Land', 'nature' => 'Assets', 'group' => 'Non Current Assets', 'type' => 'Balance Sheet', 'master_type' => 'General Ledger', 'tax_id' => 1,],
        ['code' => '110300', 'name' => 'Plant & Machinery','description' => 'Plant & Machinery', 'nature' => 'Assets', 'group' => 'Non Current Assets', 'type' => 'Balance Sheet', 'master_type' => 'General Ledger', 'tax_id' => 1,],
        ['code' => '110400', 'name' => 'Acc; Depreciation of Plant and Machinery','description' => 'Acc; Depreciation of Plant and Machinery', 'nature' => 'Assets', 'group' => 'Non Current Assets', 'type' => 'Balance Sheet', 'master_type' => 'General Ledger', 'tax_id' => 1,],
        ['code' => '110500', 'name' => 'Building','description' => 'Building', 'nature' => 'Assets', 'group' => 'Non Current Assets', 'type' => 'Balance Sheet', 'master_type' => 'General Ledger', 'tax_id' => 1,],
        ['code' => '110600', 'name' => 'Acc; Depreciation of Building','description' => 'Acc; Depreciation of Building', 'nature' => 'Assets', 'group' => 'Non Current Assets', 'type' => 'Balance Sheet', 'master_type' => 'General Ledger', 'tax_id' => 1,],
        ['code' => '110700', 'name' => 'Lab Equipment','description' => 'Lab Equipment', 'nature' => 'Assets', 'group' => 'Non Current Assets', 'type' => 'Balance Sheet', 'master_type' => 'General Ledger', 'tax_id' => 1,],
        ['code' => '110800', 'name' => 'Acc; Depreciation of Office Equipment','description' => 'Acc; Depreciation of Office Equipment', 'nature' => 'Assets', 'group' => 'Non Current Assets', 'type' => 'Balance Sheet', 'master_type' => 'General Ledger', 'tax_id' => 1,],
        ['code' => '110900', 'name' => 'IT Equipment','description' => 'IT Equipment', 'nature' => 'Assets', 'group' => 'Non Current Assets', 'type' => 'Balance Sheet', 'master_type' => 'General Ledger', 'tax_id' => 1,],
        ['code' => '111000', 'name' => 'Acc; Depreciation of IT Equipment','description' => 'Acc; Depreciation of IT Equipment', 'nature' => 'Assets', 'group' => 'Non Current Assets', 'type' => 'Balance Sheet', 'master_type' => 'General Ledger', 'tax_id' => 1,],
        ['code' => '111100', 'name' => 'Fixture and Fitting','description' => 'Fixture and Fitting', 'nature' => 'Assets', 'group' => 'Non Current Assets', 'type' => 'Balance Sheet', 'master_type' => 'General Ledger', 'tax_id' => 1,],
        ['code' => '111200', 'name' => 'Acc; Depreciation of Fixture and Fitting','description' => 'Acc; Depreciation of Fixture and Fitting', 'nature' => 'Assets', 'group' => 'Non Current Assets', 'type' => 'Balance Sheet', 'master_type' => 'General Ledger', 'tax_id' => 1,],
        ['code' => '111300', 'name' => 'Vehicle','description' => 'Vehicle', 'nature' => 'Assets', 'group' => 'Non Current Assets', 'type' => 'Balance Sheet', 'master_type' => 'General Ledger', 'tax_id' => 1,],
        ['code' => '111400', 'name' => 'Acc; Depreciation of Vehicle','description' => 'Acc; Depreciation of Vehicle', 'nature' => 'Assets', 'group' => 'Non Current Assets', 'type' => 'Balance Sheet', 'master_type' => 'General Ledger', 'tax_id' => 1,],
        ['code' => '111500', 'name' => 'Low value assets','description' => 'Low value assets', 'nature' => 'Assets', 'group' => 'Non Current Assets', 'type' => 'Balance Sheet', 'master_type' => 'General Ledger', 'tax_id' => 1,],
        ['code' => '111600', 'name' => 'Acc; Depreciation of Low value assets','description' => 'Acc; Depreciation of Low value assets', 'nature' => 'Assets', 'group' => 'Non Current Assets', 'type' => 'Balance Sheet', 'master_type' => 'General Ledger', 'tax_id' => 1,],
        ['code' => '111700', 'name' => 'Other Fixed Assets','description' => 'Other Fixed Assets', 'nature' => 'Assets', 'group' => 'Non Current Assets', 'type' => 'Balance Sheet', 'master_type' => 'General Ledger', 'tax_id' => 1,],
        ['code' => '111800', 'name' => 'Acc; Depreciation of Other Fixed Assets','description' => 'Acc; Depreciation of Other Fixed Assets', 'nature' => 'Assets', 'group' => 'Non Current Assets', 'type' => 'Balance Sheet', 'master_type' => 'General Ledger', 'tax_id' => 1,],
        ['code' => '200000', 'name' => 'Current Assets','description' => '-', 'nature' => 'Assets', 'group' => 'Current Assets', 'type' => 'Balance Sheet', 'master_type' => 'General Ledger', 'tax_id' => 1,],
        ['code' => '210000', 'name' => 'Inventory ','description' => 'Inventory', 'nature' => 'Assets', 'group' => 'Current Assets', 'type' => 'Balance Sheet', 'master_type' => 'General Ledger', 'tax_id' => 1,],
        ['code' => '220000', 'name' => 'Trade Receivables from Agent','description' => 'Trade Receivable', 'nature' => 'Assets', 'group' => 'Current Assets', 'type' => 'Balance Sheet', 'master_type' => 'General Ledger', 'tax_id' => 1,],
        ['code' => '220001', 'name' => 'Trade Receivables - Pyin Oo Lwin Agent','description' => 'Trade Receivable', 'nature' => 'Assets', 'group' => 'Current Assets', 'type' => 'Balance Sheet', 'master_type' => 'General Ledger', 'tax_id' => 1,],
        ['code' => '220002', 'name' => 'Trade Receivables - Taunggyi Agent','description' => 'Trade Receivable', 'nature' => 'Assets', 'group' => 'Current Assets', 'type' => 'Balance Sheet', 'master_type' => 'General Ledger', 'tax_id' => 1,],
        ['code' => '220003', 'name' => 'Trade Receivables - Mawlamyaing','description' => 'Trade Receivable', 'nature' => 'Assets', 'group' => 'Current Assets', 'type' => 'Balance Sheet', 'master_type' => 'General Ledger', 'tax_id' => 1,],
        ['code' => '220004', 'name' => 'Trade Receivables - Bago','description' => 'Trade Receivable', 'nature' => 'Assets', 'group' => 'Current Assets', 'type' => 'Balance Sheet', 'master_type' => 'General Ledger', 'tax_id' => 1,],
        ['code' => '220005', 'name' => 'Trade Receivables - Monywa','description' => 'Trade Receivable', 'nature' => 'Assets', 'group' => 'Current Assets', 'type' => 'Balance Sheet', 'master_type' => 'General Ledger', 'tax_id' => 1,],

        ['code' => '229000','name' => 'Sundry Customer','description' => 'Accounts Receivable', 'nature' => 'Assets', 'group' => 'Current Assets', 'type' => 'Balance Sheet', 'master_type' => 'General Ledger', 'tax_id' => 1,],
        ['code' => '229900','name' => 'Allowance for Doubtful Debt','description' => 'Trade Receivable', 'nature' => 'Assets', 'group' => 'Current Assets', 'type' => 'Balance Sheet', 'master_type' => 'General Ledger', 'tax_id' => 1,],
        ['code' => '229990','name' => 'Other Receivables','description' => 'Receivable', 'nature' => 'Assets', 'group' => 'Current Assets', 'type' => 'Balance Sheet', 'master_type' => 'General Ledger', 'tax_id' => 1,],
        ['code' => '230000','name' => 'Cash And Cash Equivalent','description' => 'Cash in Hand', 'nature' => 'Assets', 'group' => 'Current Assets', 'type' => 'Balance Sheet', 'master_type' => 'Cash & Bank', 'tax_id' => 1,],
        ['code' => '230010','name' => 'AYA Bank (MMK) Saving - HO','description' => 'Cash at bank', 'nature' => 'Assets', 'group' => 'Current Assets', 'type' => 'Balance Sheet', 'master_type' => 'Cash & Bank', 'tax_id' => 1,],
        ['code' => '230020','name' => 'KBZ Bank (MMK) saving - HO','description' => 'Cash at bank', 'nature' => 'Assets', 'group' => 'Current Assets', 'type' => 'Balance Sheet', 'master_type' => 'Cash & Bank', 'tax_id' => 1,],
        ['code' => '230030','name' => 'CB Bank (MMK) Saving - HO','description' => 'Cash at bank', 'nature' => 'Assets', 'group' => 'Current Assets', 'type' => 'Balance Sheet', 'master_type' => 'Cash & Bank', 'tax_id' => 1,],
        ['code' => '230040','name' => 'MOB Bank (MMK) Saving - HO','description' => 'Cash at bank', 'nature' => 'Assets', 'group' => 'Current Assets', 'type' => 'Balance Sheet', 'master_type' => 'Cash & Bank', 'tax_id' => 1,],
        ['code' => '230090','name' => 'Cash in Hand - HO','description' => 'Cash at bank', 'nature' => 'Assets', 'group' => 'Current Assets', 'type' => 'Balance Sheet', 'master_type' => 'Cash & Bank', 'tax_id' => 1,],
        ['code' => '240000','name' => 'Inter Company Cash','description' => 'Cash at bank', 'nature' => 'Assets', 'group' => 'Current Assets', 'type' => 'Balance Sheet', 'master_type' => 'Cash & Bank', 'tax_id' => 1,],
        ['code' => '240010','name' => 'AYA Bank (MMK) Current - Branch','description' => 'Cash at bank', 'nature' => 'Assets', 'group' => 'Current Assets', 'type' => 'Balance Sheet', 'master_type' => 'Cash & Bank', 'tax_id' => 1,],
        ['code' => '240020','name' => 'KBZ Bank (MMK) Current - Branch','description' => 'Cash at bank', 'nature' => 'Assets', 'group' => 'Current Assets', 'type' => 'Balance Sheet', 'master_type' => 'Cash & Bank', 'tax_id' => 1,],
        ['code' => '240030','name' => 'CB Bank (MMK) Current - Branch','description' => 'Cash at bank', 'nature' => 'Assets', 'group' => 'Current Assets', 'type' => 'Balance Sheet', 'master_type' => 'Cash & Bank', 'tax_id' => 1,],
        ['code' => '240040','name' => 'MOB Bank (MMK) Current - Branch','description' => 'Cash at bank', 'nature' => 'Assets', 'group' => 'Current Assets', 'type' => 'Balance Sheet', 'master_type' => 'Cash & Bank', 'tax_id' => 1,],
        ['code' => '240090','name' => 'Cash in Hand - Branch','description' => 'Cash at bank', 'nature' => 'Assets', 'group' => 'Current Assets', 'type' => 'Balance Sheet', 'master_type' => 'Cash & Bank', 'tax_id' => 1,],
        ['code' => '250000','name' => 'Prepaid and Advances','description' => 'Advance & Prepayment', 'nature' => 'Assets', 'group' => 'Current Assets', 'type' => 'Balance Sheet', 'master_type' => 'General Ledger', 'tax_id' => 1,],
        ['code' => '251000','name' => 'Prepaid and Advances - Staff','description' => 'Advance & Prepayment', 'nature' => 'Assets', 'group' => 'Current Assets', 'type' => 'Balance Sheet', 'master_type' => 'General Ledger', 'tax_id' => 1,],
        ['code' => '252000','name' => 'Prepaid and Advances - 3rd Party','description' => 'Advance & Prepayment', 'nature' => 'Assets', 'group' => 'Current Assets', 'type' => 'Balance Sheet', 'master_type' => 'General Ledger', 'tax_id' => 1,],
        ['code' => '259000','name' => 'Other Advances','description' => 'Advance & Prepayment', 'nature' => 'Assets', 'group' => 'Current Assets', 'type' => 'Balance Sheet', 'master_type' => 'General Ledger', 'tax_id' => 1,],
           
        ['code' => '260000','name' => 'R & D','description' => 'Deferred Expenses', 'nature' => 'Assets', 'group' => 'Current Assets', 'type' => 'Balance Sheet', 'master_type' => 'General Ledger', 'tax_id' => 1,],
        ['code' => '261000','name' => 'Salaries','description' => 'Deferred Expenses', 'nature' => 'Assets', 'group' => 'Current Assets', 'type' => 'Balance Sheet', 'master_type' => 'General Ledger', 'tax_id' => 1,],
        ['code' => '261010','name' => 'Social Security Board (SSB)','description' => 'Deferred Expenses', 'nature' => 'Assets', 'group' => 'Current Assets', 'type' => 'Balance Sheet', 'master_type' => 'General Ledger', 'tax_id' => 1,],
        ['code' => '261020','name' => 'Uniform Expense','description' => 'Deferred Expenses', 'nature' => 'Assets', 'group' => 'Current Assets', 'type' => 'Balance Sheet', 'master_type' => 'General Ledger', 'tax_id' => 1,],
        ['code' => '261030','name' => 'Overtime','description' => 'Deferred Expenses', 'nature' => 'Assets', 'group' => 'Current Assets', 'type' => 'Balance Sheet', 'master_type' => 'General Ledger', 'tax_id' => 1,],
        ['code' => '261040','name' => 'Welfare','description' => 'Deferred Expenses', 'nature' => 'Assets', 'group' => 'Current Assets', 'type' => 'Balance Sheet', 'master_type' => 'General Ledger', 'tax_id' => 1,],
        ['code' => '261050','name' => 'Training Expenses','description' => 'Deferred Expenses', 'nature' => 'Assets', 'group' => 'Current Assets', 'type' => 'Balance Sheet', 'master_type' => 'General Ledger', 'tax_id' => 1,],
        ['code' => '261060','name' => 'Meal Expenses','description' => 'Deferred Expenses', 'nature' => 'Assets', 'group' => 'Current Assets', 'type' => 'Balance Sheet', 'master_type' => 'General Ledger', 'tax_id' => 1,],
        ['code' => '261070','name' => 'Staff experience Allowance','description' => 'Deferred Expenses', 'nature' => 'Assets', 'group' => 'Current Assets', 'type' => 'Balance Sheet', 'master_type' => 'General Ledger', 'tax_id' => 1,],
        ['code' => '261080','name' => 'Travelling Allowance','description' => 'Deferred Expenses', 'nature' => 'Assets', 'group' => 'Current Assets', 'type' => 'Balance Sheet', 'master_type' => 'General Ledger', 'tax_id' => 1,],
        
        
        ['code' => '261090','name' => 'Insurance','description' => 'Deferred Expenses', 'nature' => 'Assets', 'group' => 'Current Assets', 'type' => 'Balance Sheet', 'master_type' => 'General Ledger', 'tax_id' => 1,],
        ['code' => '261100','name' => 'Leave Refund','description' => 'Deferred Expenses', 'nature' => 'Assets', 'group' => 'Current Assets', 'type' => 'Balance Sheet', 'master_type' => 'General Ledger', 'tax_id' => 1,],
        ['code' => '261110','name' => 'Staff Bonus (Yearly Bonus )','description' => 'Deferred Expenses', 'nature' => 'Assets', 'group' => 'Current Assets', 'type' => 'Balance Sheet', 'master_type' => 'General Ledger', 'tax_id' => 1,],
        ['code' => '261120','name' => 'Other Benefit in Kind','description' => 'Deferred Expenses', 'nature' => 'Assets', 'group' => 'Current Assets', 'type' => 'Balance Sheet', 'master_type' => 'General Ledger', 'tax_id' => 1,],
        ['code' => '261130','name' => 'Utilities and Cleaning','description' => 'Deferred Expenses', 'nature' => 'Assets', 'group' => 'Current Assets', 'type' => 'Balance Sheet', 'master_type' => 'General Ledger', 'tax_id' => 1,],
        ['code' => '261140','name' => 'Communications','description' => 'Deferred Expenses', 'nature' => 'Assets', 'group' => 'Current Assets', 'type' => 'Balance Sheet', 'master_type' => 'General Ledger', 'tax_id' => 1,],
        ['code' => '261150','name' => 'Other Rent and Lease','description' => 'Deferred Expenses', 'nature' => 'Assets', 'group' => 'Current Assets', 'type' => 'Balance Sheet', 'master_type' => 'General Ledger', 'tax_id' => 1,],
        ['code' => '261160','name' => 'Repair and Maintenance - Building','description' => 'Deferred Expenses', 'nature' => 'Assets', 'group' => 'Current Assets', 'type' => 'Balance Sheet', 'master_type' => 'General Ledger', 'tax_id' => 1,],
        ['code' => '261170','name' => 'Repair and Maintenance - Machinery','description' => 'Deferred Expenses', 'nature' => 'Assets', 'group' => 'Current Assets', 'type' => 'Balance Sheet', 'master_type' => 'General Ledger', 'tax_id' => 1,],
        ['code' => '261180','name' => 'Repair and Maintenance - IT Equipment','description' => 'Deferred Expenses', 'nature' => 'Assets', 'group' => 'Current Assets', 'type' => 'Balance Sheet', 'master_type' => 'General Ledger', 'tax_id' => 1,],
        
        
        ['code' => '261190','name' => 'Repair and Maintenance - Vehicles','description' => 'Deferred Expenses', 'nature' => 'Assets', 'group' => 'Current Assets', 'type' => 'Balance Sheet', 'master_type' => 'General Ledger', 'tax_id' => 1,],
        ['code' => '261200','name' => 'Repair and Maintenance - Other Equipment','description' => 'Deferred Expenses', 'nature' => 'Assets', 'group' => 'Current Assets', 'type' => 'Balance Sheet', 'master_type' => 'General Ledger', 'tax_id' => 1,],
        ['code' => '261210','name' => 'Fuel','description' => 'Deferred Expenses', 'nature' => 'Assets', 'group' => 'Current Assets', 'type' => 'Balance Sheet', 'master_type' => 'General Ledger', 'tax_id' => 1,],
        ['code' => '261220','name' => 'Printing and Stationery','description' => 'Deferred Expenses', 'nature' => 'Assets', 'group' => 'Current Assets', 'type' => 'Balance Sheet', 'master_type' => 'General Ledger', 'tax_id' => 1,],
        ['code' => '261230','name' => 'Other Licences','description' => 'Deferred Expenses', 'nature' => 'Assets', 'group' => 'Current Assets', 'type' => 'Balance Sheet', 'master_type' => 'General Ledger', 'tax_id' => 1,],
        ['code' => '261240','name' => 'Other General Expenses','description' => 'Deferred Expenses', 'nature' => 'Assets', 'group' => 'Current Assets', 'type' => 'Balance Sheet', 'master_type' => 'General Ledger', 'tax_id' => 1,],
        ['code' => '261250','name' => 'Travelling Expenses','description' => 'Deferred Expenses', 'nature' => 'Assets', 'group' => 'Current Assets', 'type' => 'Balance Sheet', 'master_type' => 'General Ledger', 'tax_id' => 1,],
        ['code' => '261260','name' => 'Accommodation Expenses','description' => 'Deferred Expenses', 'nature' => 'Assets', 'group' => 'Current Assets', 'type' => 'Balance Sheet', 'master_type' => 'General Ledger', 'tax_id' => 1,],
        ['code' => '261270','name' => 'Travelling Meal Expenses','description' => 'Deferred Expenses', 'nature' => 'Assets', 'group' => 'Current Assets', 'type' => 'Balance Sheet', 'master_type' => 'General Ledger', 'tax_id' => 1,],
        ['code' => '261280','name' => 'Entertainment','description' => 'Deferred Expenses', 'nature' => 'Assets', 'group' => 'Current Assets', 'type' => 'Balance Sheet', 'master_type' => 'General Ledger', 'tax_id' => 1,],
        
        
        ['code' => '261290','name' => 'Bank Charges','description' => 'Deferred Expenses', 'nature' => 'Assets', 'group' => 'Current Assets', 'type' => 'Balance Sheet', 'master_type' => 'General Ledger', 'tax_id' => 1,],
        ['code' => '261300','name' => 'Bank Loan Interest Chg.;','description' => 'Deferred Expenses', 'nature' => 'Assets', 'group' => 'Current Assets', 'type' => 'Balance Sheet', 'master_type' => 'General Ledger', 'tax_id' => 1,],
        ['code' => '261310','name' => 'Bank Loan Commission Chg.;','description' => 'Deferred Expenses', 'nature' => 'Assets', 'group' => 'Current Assets', 'type' => 'Balance Sheet', 'master_type' => 'General Ledger', 'tax_id' => 1,],
        ['code' => '261320','name' => 'Bank Cheque Book, Stamp Duty & Others Chg.;','description' => 'Deferred Expenses', 'nature' => 'Assets', 'group' => 'Current Assets', 'type' => 'Balance Sheet', 'master_type' => 'General Ledger', 'tax_id' => 1,],
        ['code' => '261330','name' => 'Insurance','description' => 'Deferred Expenses', 'nature' => 'Assets', 'group' => 'Current Assets', 'type' => 'Balance Sheet', 'master_type' => 'General Ledger', 'tax_id' => 1,],
        ['code' => '261340','name' => 'Fire Insurance Chg.','description' => 'Deferred Expenses', 'nature' => 'Assets', 'group' => 'Current Assets', 'type' => 'Balance Sheet', 'master_type' => 'General Ledger', 'tax_id' => 1,],
        ['code' => '261350','name' => 'Consultancy fees (Audit/Accounting & Finance/HR/IT/Sales & Marketing)','description' => 'Deferred Expenses', 'nature' => 'Assets', 'group' => 'Current Assets', 'type' => 'Balance Sheet', 'master_type' => 'General Ledger', 'tax_id' => 1,],
        ['code' => '261360','name' => 'Tech/Email Server Fee','description' => 'Deferred Expenses', 'nature' => 'Assets', 'group' => 'Current Assets', 'type' => 'Balance Sheet', 'master_type' => 'General Ledger', 'tax_id' => 1,],
        ['code' => '261370','name' => 'Website & Software','description' => 'Deferred Expenses', 'nature' => 'Assets', 'group' => 'Current Assets', 'type' => 'Balance Sheet', 'master_type' => 'General Ledger', 'tax_id' => 1,],
        ['code' => '261380','name' => 'Computers & peripherals','description' => 'Deferred Expenses', 'nature' => 'Assets', 'group' => 'Current Assets', 'type' => 'Balance Sheet', 'master_type' => 'General Ledger', 'tax_id' => 1,],
        
        
        ['code' => '261390','name' => 'Depreciation','description' => 'Deferred Expenses', 'nature' => 'Assets', 'group' => 'Current Assets', 'type' => 'Balance Sheet', 'master_type' => 'General Ledger', 'tax_id' => 1,],
        ['code' => '261400','name' => 'Disposal Account (Fixed Assets)','description' => 'Deferred Expenses', 'nature' => 'Assets', 'group' => 'Current Assets', 'type' => 'Balance Sheet', 'master_type' => 'General Ledger', 'tax_id' => 1,],
        ['code' => '261410','name' => 'Write off (Fixed Assets)','description' => 'Deferred Expenses', 'nature' => 'Assets', 'group' => 'Current Assets', 'type' => 'Balance Sheet', 'master_type' => 'General Ledger', 'tax_id' => 1,],
        ['code' => '290000','name' => 'Other Current Assets','description' => 'Input CT @ 0%', 'nature' => 'Assets', 'group' => 'Other Current Assets', 'type' => 'Balance Sheet', 'master_type' => 'Tax', 'tax_id' => 1,],
        ['code' => '290010','name' => 'Input CT @ 0%','description' => 'Input CT @ 0%', 'nature' => 'Assets', 'group' => 'Other Current Assets', 'type' => 'Balance Sheet', 'master_type' => 'Tax', 'tax_id' => 1,],
        ['code' => '290020','name' => 'Input CT @ 1%','description' => 'Input CT @ 1%', 'nature' => 'Assets', 'group' => 'Other Current Assets', 'type' => 'Balance Sheet', 'master_type' => 'Tax', 'tax_id' => 1,],
        ['code' => '290030','name' => 'Input CT @ 3%','description' => 'Input CT @ 3%', 'nature' => 'Assets', 'group' => 'Other Current Assets', 'type' => 'Balance Sheet', 'master_type' => 'Tax', 'tax_id' => 1,],
        ['code' => '290040','name' => 'Input CT @ 5%','description' => 'Input CT @ 5%', 'nature' => 'Assets', 'group' => 'Other Current Assets', 'type' => 'Balance Sheet', 'master_type' => 'Tax', 'tax_id' => 1,],
        ['code' => '290050','name' => 'Input CT @ 8%','description' => 'Input CT @ 8%', 'nature' => 'Assets', 'group' => 'Other Current Assets', 'type' => 'Balance Sheet', 'master_type' => 'Tax', 'tax_id' => 1,],
        ['code' => '290060','name' => 'Withholding tax','description' => 'WHT', 'nature' => 'Assets', 'group' => 'Other Current Assets', 'type' => 'Balance Sheet', 'master_type' => 'Tax', 'tax_id' => 1,],
        
        ['code' => '290070','name' => 'Advance Taxes','description' => 'Tax', 'nature' => 'Assets', 'group' => 'Other Current Assets', 'type' => 'Balance Sheet', 'master_type' => 'Tax', 'tax_id' => 1,],
        
        ['code' => '300000','name' => 'Non Current Liabilities','description' => 'Non Current Liabilities', 'nature' => 'Liabilities', 'group' => 'Non Current Liabilities', 'type' => 'Balance Sheet', 'master_type' => 'General Ledger', 'tax_id' => 1,],
        ['code' => '311000','name' => 'Long Term Loan','description' => 'Long Term Loan', 'nature' => 'Liabilities', 'group' => 'Non Current Liabilities', 'type' => 'Balance Sheet', 'master_type' => 'General Ledger', 'tax_id' => 1,],
        ['code' => '312000','name' => 'Other Loan','description' => 'Other Loan', 'nature' => 'Liabilities', 'group' => 'Non Current Liabilities', 'type' => 'Balance Sheet', 'master_type' => 'General Ledger', 'tax_id' => 1,],
        ['code' => '323000','name' => 'Debenture','description' => 'Debenture', 'nature' => 'Liabilities', 'group' => 'Non Current Liabilities', 'type' => 'Balance Sheet', 'master_type' => 'General Ledger', 'tax_id' => 1,],
        
        ['code' => '330000','name' => 'Current Liabilities','description' => 'Current Liabilities', 'nature' => 'Liabilities', 'group' => 'Current Liabilities', 'type' => 'Balance Sheet', 'master_type' => 'General Ledger', 'tax_id' => 1,],
        ['code' => '330001','name' => 'Trade Payables - Shop.com','description' => 'Trade Payable', 'nature' => 'Liabilities', 'group' => 'Current Liabilities', 'type' => 'Balance Sheet', 'master_type' => 'General Ledger', 'tax_id' => 1,],
        ['code' => '330002','name' => 'Trade Payables - Barlolo','description' => 'Trade Payable', 'nature' => 'Liabilities', 'group' => 'Current Liabilities', 'type' => 'Balance Sheet', 'master_type' => 'General Ledger', 'tax_id' => 1,],
        ['code' => '340000','name' => 'Other Payables','description' => 'Payable', 'nature' => 'Liabilities', 'group' => 'Current Liabilities', 'type' => 'Balance Sheet', 'master_type' => 'General Ledger', 'tax_id' => 1,],
        ['code' => '349000','name' => 'Sundry Vendor','description' => 'Accounts Payable', 'nature' => 'Liabilities', 'group' => 'Current Liabilities', 'type' => 'Balance Sheet', 'master_type' => 'General Ledger', 'tax_id' => 1,],
        ['code' => '350000','name' => 'Bank Overdraft','description' => 'Overdraft', 'nature' => 'Liabilities', 'group' => 'Current Liabilities', 'type' => 'Balance Sheet', 'master_type' => 'General Ledger', 'tax_id' => 1,],
        ['code' => '360000','name' => 'Expense Payable (Accrued)','description' => 'Accrued & Other', 'nature' => 'Liabilities', 'group' => 'Current Liabilities', 'type' => 'Balance Sheet', 'master_type' => 'General Ledger', 'tax_id' => 1,],
        ['code' => '370000','name' => 'Salary Control Account','description' => 'Control Account', 'nature' => 'Liabilities', 'group' => 'Current Liabilities', 'type' => 'Balance Sheet', 'master_type' => 'General Ledger', 'tax_id' => 1,],
        ['code' => '371000','name' => 'Personal Income tax payable','description' => 'Control Account', 'nature' => 'Liabilities', 'group' => 'Current Liabilities', 'type' => 'Balance Sheet', 'master_type' => 'General Ledger', 'tax_id' => 1,],
        ['code' => '372000','name' => 'Social Security Board (SSB) payable','description' => 'Control Account', 'nature' => 'Liabilities', 'group' => 'Current Liabilities', 'type' => 'Balance Sheet', 'master_type' => 'General Ledger', 'tax_id' => 1,],
        ['code' => '380000','name' => 'Temporary Loan','description' => 'Temporary Loan', 'nature' => 'Liabilities', 'group' => 'Current Liabilities', 'type' => 'Balance Sheet', 'master_type' => 'General Ledger', 'tax_id' => 1,],
        
        ['code' => '390000','name' => 'Other Current Liabilities','description' => 'Other Current Liabilities', 'nature' => 'Liabilities', 'group' => 'Other Current Liabilities', 'type' => 'Balance Sheet', 'master_type' => 'Tax', 'tax_id' => 1,],
        ['code' => '390010','name' => 'Output CT @ 0%','description' => 'Output CT @ 0%', 'nature' => 'Liabilities', 'group' => 'Other Current Liabilities', 'type' => 'Balance Sheet', 'master_type' => 'Tax', 'tax_id' => 1,],
        ['code' => '390020','name' => 'Output CT @ 1%','description' => 'Output CT @ 1%', 'nature' => 'Liabilities', 'group' => 'Other Current Liabilities', 'type' => 'Balance Sheet', 'master_type' => 'Tax', 'tax_id' => 1,],
        ['code' => '390030','name' => 'Output CT @ 3%','description' => 'Output CT @ 3%', 'nature' => 'Liabilities', 'group' => 'Other Current Liabilities', 'type' => 'Balance Sheet', 'master_type' => 'Tax', 'tax_id' => 1,],
        ['code' => '390040','name' => 'Output CT @ 5%','description' => 'Output CT @ 5%', 'nature' => 'Liabilities', 'group' => 'Other Current Liabilities', 'type' => 'Balance Sheet', 'master_type' => 'Tax', 'tax_id' => 1,],
        ['code' => '390050','name' => 'Output CT @ 8%','description' => 'Output CT @ 8%', 'nature' => 'Liabilities', 'group' => 'Other Current Liabilities', 'type' => 'Balance Sheet', 'master_type' => 'Tax', 'tax_id' => 1,],
        ['code' => '390060','name' => 'Withholding tax','description' => 'WHT', 'nature' => 'Liabilities', 'group' => 'Other Current Liabilities', 'type' => 'Balance Sheet', 'master_type' => 'Tax', 'tax_id' => 1,],
        ['code' => '390070','name' => 'Sales Tax Payable','description' => 'Sales Tax', 'nature' => 'Liabilities', 'group' => 'Other Current Liabilities', 'type' => 'Balance Sheet', 'master_type' => 'Tax', 'tax_id' => 1,],
        ['code' => '399000','name' => 'Tax Payable','description' => 'Tax Payable', 'nature' => 'Liabilities', 'group' => 'Other Current Liabilities', 'type' => 'Balance Sheet', 'master_type' => 'Tax', 'tax_id' => 1,],
        
        ['code' => '400000','name' => 'Equity and Capital Employed','description' => 'Equity and Capital Employed', 'nature' => 'Liabilities', 'group' => 'Equity and Capital Employed', 'type' => 'Balance Sheet', 'master_type' => 'General Ledger', 'tax_id' => 1,],
        ['code' => '410000','name' => 'Shareholder capital','description' => 'Shareholder Capital', 'nature' => 'Liabilities', 'group' => 'Equity and Capital Employed', 'type' => 'Balance Sheet', 'master_type' => 'General Ledger', 'tax_id' => 1,],
        ['code' => '420000','name' => 'Retained Earning','description' => 'Retained Earnings', 'nature' => 'Liabilities', 'group' => 'Equity and Capital Employed', 'type' => 'Balance Sheet', 'master_type' => 'General Ledger', 'tax_id' => 1,],
        
        ['code' => '500000','name' => 'Income/Revenue','description' => 'Income', 'nature' => 'Income', 'group' => 'Income/Revenue', 'type' => 'Profit & Loss', 'master_type' => 'General Ledger', 'tax_id' => 1,],
        ['code' => '510000','name' => 'Delivery Income - HO','description' => 'Income', 'nature' => 'Income', 'group' => 'Income/Revenue', 'type' => 'Profit & Loss', 'master_type' => 'General Ledger', 'tax_id' => 1,],
        ['code' => '510500','name' => 'Delivery Income - Yangon Branch','description' => 'Income', 'nature' => 'Income', 'group' => 'Income/Revenue', 'type' => 'Profit & Loss', 'master_type' => 'General Ledger', 'tax_id' => 1,],
        ['code' => '511000','name' => 'Delivery Income - Mandalay','description' => 'Income', 'nature' => 'Income', 'group' => 'Income/Revenue', 'type' => 'Profit & Loss', 'master_type' => 'General Ledger', 'tax_id' => 1,],
        ['code' => '511500','name' => 'Delivery Income - Nay Pyi Daw','description' => 'Income', 'nature' => 'Income', 'group' => 'Income/Revenue', 'type' => 'Profit & Loss', 'master_type' => 'General Ledger', 'tax_id' => 1,],
        ['code' => '520000','name' => 'Cash Collection fees','description' => 'Income', 'nature' => 'Income', 'group' => 'Income/Revenue', 'type' => 'Profit & Loss', 'master_type' => 'General Ledger', 'tax_id' => 1,],
        ['code' => '530000','name' => 'Insurance','description' => 'Income', 'nature' => 'Income', 'group' => 'Income/Revenue', 'type' => 'Profit & Loss', 'master_type' => 'General Ledger', 'tax_id' => 1,],
        ['code' => '590000','name' => 'Sales Return','description' => 'Income', 'nature' => 'Income', 'group' => 'Income/Revenue', 'type' => 'Profit & Loss', 'master_type' => 'General Ledger', 'tax_id' => 1,],
        
        ['code' => '600000','name' => 'Direct Cost','description' => 'Direct Cost', 'nature' => 'Expenses', 'group' => 'Cost of Service', 'type' => 'Profit & Loss', 'master_type' => 'General Ledger', 'tax_id' => 1,],
        
        ['code' => '610000','name' => 'Delivery Income Allocation (To Branch)','description' => 'Direct Cost', 'nature' => 'Expenses', 'group' => 'Cost of Service', 'type' => 'Profit & Loss', 'master_type' => 'General Ledger', 'tax_id' => 1,],
        ['code' => '610001','name' => 'Deli Hero Commission','description' => 'Direct Cost', 'nature' => 'Expenses', 'group' => 'Cost of Service', 'type' => 'Profit & Loss', 'master_type' => 'General Ledger', 'tax_id' => 1,],
        ['code' => '610002','name' => 'Bus Charges','description' => 'Direct Cost', 'nature' => 'Expenses', 'group' => 'Cost of Service', 'type' => 'Profit & Loss', 'master_type' => 'General Ledger', 'tax_id' => 1,],
        ['code' => '610003','name' => 'Gate Fees','description' => 'Direct Cost', 'nature' => 'Expenses', 'group' => 'Cost of Service', 'type' => 'Profit & Loss', 'master_type' => 'General Ledger', 'tax_id' => 1,],
        ['code' => '610004','name' => 'Transportation Charges','description' => 'Direct Cost', 'nature' => 'Expenses', 'group' => 'Cost of Service', 'type' => 'Profit & Loss', 'master_type' => 'General Ledger', 'tax_id' => 1,],
        ['code' => '610005','name' => 'Delivery Staff Cost','description' => 'Direct Cost', 'nature' => 'Expenses', 'group' => 'Cost of Service', 'type' => 'Profit & Loss', 'master_type' => 'General Ledger', 'tax_id' => 1,],
        ['code' => '620000','name' => 'Purchasing Packaging materials','description' => 'Direct Cost', 'nature' => 'Expenses', 'group' => 'Cost of Service', 'type' => 'Profit & Loss', 'master_type' => 'General Ledger', 'tax_id' => 1,],
        ['code' => '630000','name' => 'Other Cost of Service','description' => 'Direct Cost', 'nature' => 'Expenses', 'group' => 'Cost of Service', 'type' => 'Profit & Loss', 'master_type' => 'General Ledger', 'tax_id' => 1,],
        ['code' => '640000','name' => 'Accrued Cost of Sales','description' => 'Accrued Expenses', 'nature' => 'Expenses', 'group' => 'Cost of Service', 'type' => 'Profit & Loss', 'master_type' => 'General Ledger', 'tax_id' => 1,],
        
        
        ['code' => '650000','name' => 'Other Direct Cost','description' => 'Other Direct Cost', 'nature' => 'Expenses', 'group' => 'Cost of Service', 'type' => 'Profit & Loss', 'master_type' => 'General Ledger', 'tax_id' => 1,],
        ['code' => '651000','name' => 'Salaries','description' => 'Other Direct Cost', 'nature' => 'Expenses', 'group' => 'Cost of Service', 'type' => 'Profit & Loss', 'master_type' => 'General Ledger', 'tax_id' => 1,],
        ['code' => '652000','name' => 'Social Security Board (SSB)','description' => 'Other Direct Cost', 'nature' => 'Expenses', 'group' => 'Cost of Service', 'type' => 'Profit & Loss', 'master_type' => 'General Ledger', 'tax_id' => 1,],
        ['code' => '653000','name' => 'Uniform Expense','description' => 'Other Direct Cost', 'nature' => 'Expenses', 'group' => 'Cost of Service', 'type' => 'Profit & Loss', 'master_type' => 'General Ledger', 'tax_id' => 1,],
        ['code' => '654000','name' => 'Overtime','description' => 'Other Direct Cost', 'nature' => 'Expenses', 'group' => 'Cost of Service', 'type' => 'Profit & Loss', 'master_type' => 'General Ledger', 'tax_id' => 1,],
        ['code' => '655000','name' => 'Welfare','description' => 'Other Direct Cost', 'nature' => 'Expenses', 'group' => 'Cost of Service', 'type' => 'Profit & Loss', 'master_type' => 'General Ledger', 'tax_id' => 1,],
        ['code' => '656000','name' => 'Training Expenses','description' => 'Other Direct Cost', 'nature' => 'Expenses', 'group' => 'Cost of Service', 'type' => 'Profit & Loss', 'master_type' => 'General Ledger', 'tax_id' => 1,],
        ['code' => '657000','name' => 'Meal Expenses','description' => 'Other Direct Cost', 'nature' => 'Expenses', 'group' => 'Cost of Service', 'type' => 'Profit & Loss', 'master_type' => 'General Ledger', 'tax_id' => 1,],
        ['code' => '658000','name' => 'Staff experience Allowance','description' => 'Other Direct Cost', 'nature' => 'Expenses', 'group' => 'Cost of Service', 'type' => 'Profit & Loss', 'master_type' => 'General Ledger', 'tax_id' => 1,],
        ['code' => '659000','name' => 'Travelling Allowance','description' => 'Other Direct Cost', 'nature' => 'Expenses', 'group' => 'Cost of Service', 'type' => 'Profit & Loss', 'master_type' => 'General Ledger', 'tax_id' => 1,],
        
        ['code' => '660000','name' => 'Insurance','description' => 'Other Direct Cost', 'nature' => 'Expenses', 'group' => 'Cost of Service', 'type' => 'Profit & Loss', 'master_type' => 'General Ledger', 'tax_id' => 1,],
        ['code' => '661000','name' => 'Leave Refund','description' => 'Other Direct Cost', 'nature' => 'Expenses', 'group' => 'Cost of Service', 'type' => 'Profit & Loss', 'master_type' => 'General Ledger', 'tax_id' => 1,],
        ['code' => '662000','name' => 'Staff Bonus (Yearly Bonus )','description' => 'Other Direct Cost', 'nature' => 'Expenses', 'group' => 'Cost of Service', 'type' => 'Profit & Loss', 'master_type' => 'General Ledger', 'tax_id' => 1,],
        ['code' => '663000','name' => 'Other Benefit in Kind','description' => 'Other Direct Cost', 'nature' => 'Expenses', 'group' => 'Cost of Service', 'type' => 'Profit & Loss', 'master_type' => 'General Ledger', 'tax_id' => 1,],
        ['code' => '664000','name' => 'Utilities and Cleaning','description' => 'Other Direct Cost', 'nature' => 'Expenses', 'group' => 'Cost of Service', 'type' => 'Profit & Loss', 'master_type' => 'General Ledger', 'tax_id' => 1,],
        ['code' => '665000','name' => 'Communications','description' => 'Other Direct Cost', 'nature' => 'Expenses', 'group' => 'Cost of Service', 'type' => 'Profit & Loss', 'master_type' => 'General Ledger', 'tax_id' => 1,],
        ['code' => '666000','name' => 'Other Rent and Lease','description' => 'Other Direct Cost', 'nature' => 'Expenses', 'group' => 'Cost of Service', 'type' => 'Profit & Loss', 'master_type' => 'General Ledger', 'tax_id' => 1,],
        ['code' => '667000','name' => 'Repair and Maintenance - Building','description' => 'Other Direct Cost', 'nature' => 'Expenses', 'group' => 'Cost of Service', 'type' => 'Profit & Loss', 'master_type' => 'General Ledger', 'tax_id' => 1,],
        ['code' => '668000','name' => 'Repair and Maintenance - Machinery','description' => 'Other Direct Cost', 'nature' => 'Expenses', 'group' => 'Cost of Service', 'type' => 'Profit & Loss', 'master_type' => 'General Ledger', 'tax_id' => 1,],
        ['code' => '669000','name' => 'Repair and Maintenance - IT Equipment','description' => 'Other Direct Cost', 'nature' => 'Expenses', 'group' => 'Cost of Service', 'type' => 'Profit & Loss', 'master_type' => 'General Ledger', 'tax_id' => 1,],
        
        ['code' => '670000','name' => 'Repair and Maintenance - Vehicles','description' => 'Other Direct Cost', 'nature' => 'Expenses', 'group' => 'Cost of Service', 'type' => 'Profit & Loss', 'master_type' => 'General Ledger', 'tax_id' => 1,],
        ['code' => '671000','name' => 'Repair and Maintenance - Other Equipment','description' => 'Other Direct Cost', 'nature' => 'Expenses', 'group' => 'Cost of Service', 'type' => 'Profit & Loss', 'master_type' => 'General Ledger', 'tax_id' => 1,],
        ['code' => '672000','name' => 'Fuel','description' => 'Other Direct Cost', 'nature' => 'Expenses', 'group' => 'Cost of Service', 'type' => 'Profit & Loss', 'master_type' => 'General Ledger', 'tax_id' => 1,],
        ['code' => '673000','name' => 'Printing and Stationery','description' => 'Other Direct Cost', 'nature' => 'Expenses', 'group' => 'Cost of Service', 'type' => 'Profit & Loss', 'master_type' => 'General Ledger', 'tax_id' => 1,],
        ['code' => '674000','name' => 'Travelling Expenses','description' => 'Other Direct Cost', 'nature' => 'Expenses', 'group' => 'Cost of Service', 'type' => 'Profit & Loss', 'master_type' => 'General Ledger', 'tax_id' => 1,],
        ['code' => '675000','name' => 'Accommodation Expenses','description' => 'Other Direct Cost', 'nature' => 'Expenses', 'group' => 'Cost of Service', 'type' => 'Profit & Loss', 'master_type' => 'General Ledger', 'tax_id' => 1,],
        ['code' => '676000','name' => 'Travelling Meal Expenses','description' => 'Other Direct Cost', 'nature' => 'Expenses', 'group' => 'Cost of Service', 'type' => 'Profit & Loss', 'master_type' => 'General Ledger', 'tax_id' => 1,],
        ['code' => '677000','name' => 'Other Production Overhead','description' => 'Other Direct Cost', 'nature' => 'Expenses', 'group' => 'Cost of Service', 'type' => 'Profit & Loss', 'master_type' => 'General Ledger', 'tax_id' => 1,],
        ['code' => '678000','name' => 'Security Expenses','description' => 'Other Direct Cost', 'nature' => 'Expenses', 'group' => 'Cost of Service', 'type' => 'Profit & Loss', 'master_type' => 'General Ledger', 'tax_id' => 1,],
        ['code' => '679000','name' => 'Consumable expenses','description' => 'Other Direct Cost', 'nature' => 'Expenses', 'group' => 'Cost of Service', 'type' => 'Profit & Loss', 'master_type' => 'General Ledger', 'tax_id' => 1,],
        
        ['code' => '680000','name' => 'Consultancy fees','description' => 'Other Direct Cost', 'nature' => 'Expenses', 'group' => 'Cost of Service', 'type' => 'Profit & Loss', 'master_type' => 'General Ledger', 'tax_id' => 1,],
        ['code' => '681000','name' => 'Accrued Direct Cost','description' => 'Other Direct Cost', 'nature' => 'Expenses', 'group' => 'Cost of Service', 'type' => 'Profit & Loss', 'master_type' => 'General Ledger', 'tax_id' => 1,],
        ['code' => '682000','name' => 'Depreciation','description' => 'Other Direct Cost', 'nature' => 'Expenses', 'group' => 'Cost of Service', 'type' => 'Profit & Loss', 'master_type' => 'General Ledger', 'tax_id' => 1,],
        
        ['code' => '700000','name' => 'Other Operating and Admin Expenses','description' => 'Other Operating and Admin Expenses', 'nature' => 'Expenses', 'group' => 'Overhead', 'type' => 'Profit & Loss', 'master_type' => 'General Ledger', 'tax_id' => 1,],
        ['code' => '701000','name' => 'Salaries','description' => 'Other Operating and Admin Expenses', 'nature' => 'Expenses', 'group' => 'Overhead', 'type' => 'Profit & Loss', 'master_type' => 'General Ledger', 'tax_id' => 1,],
        ['code' => '702000','name' => 'Social Security Board (SSB)','description' => 'Other Operating and Admin Expenses', 'nature' => 'Expenses', 'group' => 'Overhead', 'type' => 'Profit & Loss', 'master_type' => 'General Ledger', 'tax_id' => 1,],
        ['code' => '703000','name' => 'Uniform Expense','description' => 'Other Operating and Admin Expenses', 'nature' => 'Expenses', 'group' => 'Overhead', 'type' => 'Profit & Loss', 'master_type' => 'General Ledger', 'tax_id' => 1,],
        ['code' => '704000','name' => 'Overtime','description' => 'Other Operating and Admin Expenses', 'nature' => 'Expenses', 'group' => 'Overhead', 'type' => 'Profit & Loss', 'master_type' => 'General Ledger', 'tax_id' => 1,],
        ['code' => '705000','name' => 'Welfare','description' => 'Other Operating and Admin Expenses', 'nature' => 'Expenses', 'group' => 'Overhead', 'type' => 'Profit & Loss', 'master_type' => 'General Ledger', 'tax_id' => 1,],
        
        ['code' => '706000','name' => 'Training Expenses','description' => 'Other Operating and Admin Expenses', 'nature' => 'Expenses', 'group' => 'Overhead', 'type' => 'Profit & Loss', 'master_type' => 'General Ledger', 'tax_id' => 1,],
        ['code' => '707000','name' => 'Meal Expenses','description' => 'Other Operating and Admin Expenses', 'nature' => 'Expenses', 'group' => 'Overhead', 'type' => 'Profit & Loss', 'master_type' => 'General Ledger', 'tax_id' => 1,],
        ['code' => '708000','name' => 'Staff experience Allowance','description' => 'Other Operating and Admin Expenses', 'nature' => 'Expenses', 'group' => 'Overhead', 'type' => 'Profit & Loss', 'master_type' => 'General Ledger', 'tax_id' => 1,],
        ['code' => '709000','name' => 'Travelling Allowance','description' => 'Other Operating and Admin Expenses', 'nature' => 'Expenses', 'group' => 'Overhead', 'type' => 'Profit & Loss', 'master_type' => 'General Ledger', 'tax_id' => 1,],
        ['code' => '710000','name' => 'Insurance','description' => 'Other Operating and Admin Expenses', 'nature' => 'Expenses', 'group' => 'Overhead', 'type' => 'Profit & Loss', 'master_type' => 'General Ledger', 'tax_id' => 1,],
        ['code' => '711000','name' => 'Leave Refund','description' => 'Other Operating and Admin Expenses', 'nature' => 'Expenses', 'group' => 'Overhead', 'type' => 'Profit & Loss', 'master_type' => 'General Ledger', 'tax_id' => 1,],
        ['code' => '712000','name' => 'Staff Bonus (Yearly Bonus )','description' => 'Other Operating and Admin Expenses', 'nature' => 'Expenses', 'group' => 'Overhead', 'type' => 'Profit & Loss', 'master_type' => 'General Ledger', 'tax_id' => 1,],
        ['code' => '713000','name' => 'Other Benefit in Kind','description' => 'Other Operating and Admin Expenses', 'nature' => 'Expenses', 'group' => 'Overhead', 'type' => 'Profit & Loss', 'master_type' => 'General Ledger', 'tax_id' => 1,],
        ['code' => '714000','name' => 'Utilities and Cleaning','description' => 'Other Operating and Admin Expenses', 'nature' => 'Expenses', 'group' => 'Overhead', 'type' => 'Profit & Loss', 'master_type' => 'General Ledger', 'tax_id' => 1,],
        ['code' => '715000','name' => 'Communications','description' => 'Other Operating and Admin Expenses', 'nature' => 'Expenses', 'group' => 'Overhead', 'type' => 'Profit & Loss', 'master_type' => 'General Ledger', 'tax_id' => 1,],
        
        ['code' => '716000','name' => 'Other Rent and Lease','description' => 'Other Operating and Admin Expenses', 'nature' => 'Expenses', 'group' => 'Overhead', 'type' => 'Profit & Loss', 'master_type' => 'General Ledger', 'tax_id' => 1,],
        ['code' => '717000','name' => 'Repair and Maintenance - Building','description' => 'Other Operating and Admin Expenses', 'nature' => 'Expenses', 'group' => 'Overhead', 'type' => 'Profit & Loss', 'master_type' => 'General Ledger', 'tax_id' => 1,],
        ['code' => '718000','name' => 'Repair and Maintenance - Machinery','description' => 'Other Operating and Admin Expenses', 'nature' => 'Expenses', 'group' => 'Overhead', 'type' => 'Profit & Loss', 'master_type' => 'General Ledger', 'tax_id' => 1,],
        ['code' => '719000','name' => 'Repair and Maintenance - IT Equipment','description' => 'Other Operating and Admin Expenses', 'nature' => 'Expenses', 'group' => 'Overhead', 'type' => 'Profit & Loss', 'master_type' => 'General Ledger', 'tax_id' => 1,],
        ['code' => '720000','name' => 'Repair and Maintenance - Vehicles','description' => 'Other Operating and Admin Expenses', 'nature' => 'Expenses', 'group' => 'Overhead', 'type' => 'Profit & Loss', 'master_type' => 'General Ledger', 'tax_id' => 1,],
        ['code' => '721000','name' => 'Repair and Maintenance - Other Equipment','description' => 'Other Operating and Admin Expenses', 'nature' => 'Expenses', 'group' => 'Overhead', 'type' => 'Profit & Loss', 'master_type' => 'General Ledger', 'tax_id' => 1,],
        ['code' => '722000','name' => 'Fuel','description' => 'Other Operating and Admin Expenses', 'nature' => 'Expenses', 'group' => 'Overhead', 'type' => 'Profit & Loss', 'master_type' => 'General Ledger', 'tax_id' => 1,],
        ['code' => '723000','name' => 'Printing and Stationery','description' => 'Other Operating and Admin Expenses', 'nature' => 'Expenses', 'group' => 'Overhead', 'type' => 'Profit & Loss', 'master_type' => 'General Ledger', 'tax_id' => 1,],
        ['code' => '724000','name' => 'Other Licences','description' => 'Other Operating and Admin Expenses', 'nature' => 'Expenses', 'group' => 'Overhead', 'type' => 'Profit & Loss', 'master_type' => 'General Ledger', 'tax_id' => 1,],
        ['code' => '725000','name' => 'Tender Form & Documents Chg.;','description' => 'Other Operating and Admin Expenses', 'nature' => 'Expenses', 'group' => 'Overhead', 'type' => 'Profit & Loss', 'master_type' => 'General Ledger', 'tax_id' => 1,],
        
        ['code' => '726000','name' => 'Other General Expenses','description' => 'Other Operating and Admin Expenses', 'nature' => 'Expenses', 'group' => 'Overhead', 'type' => 'Profit & Loss', 'master_type' => 'General Ledger', 'tax_id' => 1,],
        ['code' => '727000','name' => 'Travelling Expenses','description' => 'Other Operating and Admin Expenses', 'nature' => 'Expenses', 'group' => 'Overhead', 'type' => 'Profit & Loss', 'master_type' => 'General Ledger', 'tax_id' => 1,],
        ['code' => '728000','name' => 'Accommodation Expenses','description' => 'Other Operating and Admin Expenses', 'nature' => 'Expenses', 'group' => 'Overhead', 'type' => 'Profit & Loss', 'master_type' => 'General Ledger', 'tax_id' => 1,],
        ['code' => '729000','name' => 'Travelling Meal Expenses','description' => 'Other Operating and Admin Expenses', 'nature' => 'Expenses', 'group' => 'Overhead', 'type' => 'Profit & Loss', 'master_type' => 'General Ledger', 'tax_id' => 1,],
        ['code' => '730000','name' => 'Entertainment','description' => 'Other Operating and Admin Expenses', 'nature' => 'Expenses', 'group' => 'Overhead', 'type' => 'Profit & Loss', 'master_type' => 'General Ledger', 'tax_id' => 1,],
        ['code' => '731000','name' => 'Bank Charges','description' => 'Other Operating and Admin Expenses', 'nature' => 'Expenses', 'group' => 'Overhead', 'type' => 'Profit & Loss', 'master_type' => 'General Ledger', 'tax_id' => 1,],
        ['code' => '732000','name' => 'Bank Loan Interest Chg.;','description' => 'Other Operating and Admin Expenses', 'nature' => 'Expenses', 'group' => 'Overhead', 'type' => 'Profit & Loss', 'master_type' => 'General Ledger', 'tax_id' => 1,],
        ['code' => '733000','name' => 'Bank Loan Commission Chg.;','description' => 'Other Operating and Admin Expenses', 'nature' => 'Expenses', 'group' => 'Overhead', 'type' => 'Profit & Loss', 'master_type' => 'General Ledger', 'tax_id' => 1,],
        ['code' => '734000','name' => 'Bank Cheque Book, Stamp Duty & Others Chg.;','description' => 'Other Operating and Admin Expenses', 'nature' => 'Expenses', 'group' => 'Overhead', 'type' => 'Profit & Loss', 'master_type' => 'General Ledger', 'tax_id' => 1,],
        ['code' => '735000','name' => 'Insurance','description' => 'Other Operating and Admin Expenses', 'nature' => 'Expenses', 'group' => 'Overhead', 'type' => 'Profit & Loss', 'master_type' => 'General Ledger', 'tax_id' => 1,],
        
        ['code' => '736000','name' => 'Fire Insurance Chg.','description' => 'Other Operating and Admin Expenses', 'nature' => 'Expenses', 'group' => 'Overhead', 'type' => 'Profit & Loss', 'master_type' => 'General Ledger', 'tax_id' => 1,],
        ['code' => '737000','name' => 'Consultancy fees (Audit/Accounting & Finance/HR/IT/Sales & Marketing)','description' => 'Other Operating and Admin Expenses', 'nature' => 'Expenses', 'group' => 'Overhead', 'type' => 'Profit & Loss', 'master_type' => 'General Ledger', 'tax_id' => 1,],
        ['code' => '738000','name' => 'Dealers Commission ','description' => 'Other Operating and Admin Expenses', 'nature' => 'Expenses', 'group' => 'Overhead', 'type' => 'Profit & Loss', 'master_type' => 'General Ledger', 'tax_id' => 1,],
        ['code' => '739000','name' => 'Other Commission','description' => 'Other Operating and Admin Expenses', 'nature' => 'Expenses', 'group' => 'Overhead', 'type' => 'Profit & Loss', 'master_type' => 'General Ledger', 'tax_id' => 1,],
        ['code' => '740000','name' => 'Promotion Expenses','description' => 'Other Operating and Admin Expenses', 'nature' => 'Expenses', 'group' => 'Overhead', 'type' => 'Profit & Loss', 'master_type' => 'General Ledger', 'tax_id' => 1,],
        ['code' => '741000','name' => 'Advertising Expenses','description' => 'Other Operating and Admin Expenses', 'nature' => 'Expenses', 'group' => 'Overhead', 'type' => 'Profit & Loss', 'master_type' => 'General Ledger', 'tax_id' => 1,],
        ['code' => '742000','name' => 'Branding & Marketing','description' => 'Other Operating and Admin Expenses', 'nature' => 'Expenses', 'group' => 'Overhead', 'type' => 'Profit & Loss', 'master_type' => 'General Ledger', 'tax_id' => 1,],
        ['code' => '743000','name' => 'Gift and Donation','description' => 'Other Operating and Admin Expenses', 'nature' => 'Expenses', 'group' => 'Overhead', 'type' => 'Profit & Loss', 'master_type' => 'General Ledger', 'tax_id' => 1,],
        ['code' => '744000','name' => 'Depreciation','description' => 'Other Operating and Admin Expenses', 'nature' => 'Expenses', 'group' => 'Overhead', 'type' => 'Profit & Loss', 'master_type' => 'General Ledger', 'tax_id' => 1,],
        ['code' => '745000','name' => 'Disposal Account (Fixed Assets)','description' => 'Other Operating and Admin Expenses', 'nature' => 'Expenses', 'group' => 'Overhead', 'type' => 'Profit & Loss', 'master_type' => 'General Ledger', 'tax_id' => 1,],
        
        ['code' => '746000','name' => 'Write off (Fixed Assets)','description' => 'Other Operating and Admin Expenses', 'nature' => 'Expenses', 'group' => 'Overhead', 'type' => 'Profit & Loss', 'master_type' => 'General Ledger', 'tax_id' => 1,],
        
        ['code' => '800000','name' => 'Other Income ','description' => 'Other Income', 'nature' => 'Income', 'group' => 'Others Income', 'type' => 'Profit & Loss', 'master_type' => 'General Ledger', 'tax_id' => 1,],
        ['code' => '801000','name' => 'Interest Income','description' => 'Other Income', 'nature' => 'Income', 'group' => 'Others Income', 'type' => 'Profit & Loss', 'master_type' => 'General Ledger', 'tax_id' => 1,],
        ['code' => '802000','name' => 'Commission received','description' => 'Other Income', 'nature' => 'Income', 'group' => 'Others Income', 'type' => 'Profit & Loss', 'master_type' => 'General Ledger', 'tax_id' => 1,],
        ['code' => '805000','name' => 'Disposal Account (Fixed Assets)','description' => 'Other Income', 'nature' => 'Income', 'group' => 'Others Income', 'type' => 'Profit & Loss', 'master_type' => 'General Ledger', 'tax_id' => 1,],
        
        ['code' => '900000','name' => 'Other Allocations','description' => 'Others', 'nature' => 'Expenses', 'group' => 'Others', 'type' => 'Profit & Loss', 'master_type' => 'General Ledger', 'tax_id' => 1,],
        ['code' => '950000','name' => 'Exchange Gain & Loss','description' => 'Others', 'nature' => 'Expenses', 'group' => 'Others', 'type' => 'Profit & Loss', 'master_type' => 'General Ledger', 'tax_id' => 1,],
        ['code' => '980000','name' => 'Corporation Tax','description' => 'Others', 'nature' => 'Expenses', 'group' => 'Others', 'type' => 'Profit & Loss', 'master_type' => 'General Ledger', 'tax_id' => 1,],
        
        ['code' => '999999','name' => 'Suspense Account','description' => 'Others', 'nature' => 'Expenses', 'group' => 'Others', 'type' => 'Profit & Loss', 'master_type' => 'General Ledger', 'tax_id' => 1,],
        
    ];
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Schema::disableForeignKeyConstraints();
        foreach (Branch::all() as $key => $branch) {
            foreach ($this->data as $row) {
                FinanceAccount::create([
                    'name'    => $row['name'],
                    'description' => $row['description'],
                    'code' => $row['code'],
                    'finance_nature_id' => FinanceNature::where('name',$row['nature'])->first()->id,
                    'finance_group_id' => FinanceGroup::where('name',$row['group'])->first()->id,
                    'finance_master_type_id' => FinanceMasterType::where('name',$row['master_type'])->first()->id,
                    'finance_account_type_id' => FinanceAccountType::where('name',$row['type'])->first()->id,
                    'finance_code_id' => FinanceCode::where('code',$row['code'])->first()->id,
                    'finance_tax_id' => $row['tax_id'],
                    'branch_id' => $branch->id,
                    
                ]);
            }
        }
        Schema::enableForeignKeyConstraints();
    }
}
