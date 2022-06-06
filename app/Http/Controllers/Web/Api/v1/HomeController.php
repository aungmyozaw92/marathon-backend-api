<?php

namespace App\Http\Controllers\Web\Api\v1;

use App\Models\Bank;
use App\Models\City;
use App\Models\Gate;
use App\Models\Meta;
use App\Models\Zone;
use App\Models\Staff;
use App\Models\Branch;
use App\Models\Merchant;
use App\Models\Deduction;
use App\Models\HeroBadge;
use App\Models\AgentBadge;
use App\Models\BusStation;
use App\Models\CallStatus;
use App\Models\FinanceTax;
use App\Models\CourierType;
use App\Models\FinanceCode;
use App\Models\FinanceMeta;
use App\Models\GlobalScale;
use App\Models\StoreStatus;
use App\Models\DiscountType;
use App\Models\FinanceGroup;
use Illuminate\Http\Request;
use App\Models\FailureStatus;
use App\Models\FinanceConfig;
use App\Models\FinanceNature;
use App\Models\DeliveryStatus;
use App\Models\FinanceAccount;
use App\Models\FinanceMasterType;
use App\Models\FinanceAccountType;
use App\Http\Controllers\Controller;
use App\Models\FinancePaymentOption;
use Illuminate\Support\Facades\Redis;
use App\Http\Resources\Bank\BankCollection;
use App\Http\Resources\City\CityCollection;
use App\Http\Resources\Gate\GateCollection;
use App\Http\Resources\Meta\MetaCollection;
use App\Http\Resources\Zone\ZoneCollection;
use App\Http\Resources\Staff\StaffCollection;
use App\Http\Resources\Branch\BranchCollection;
use App\Http\Resources\Merchant\MerchantCollection;
use App\Repositories\Web\Api\v1\LogStatusRepository;
use App\Http\Resources\Deduction\DeductionCollection;
use App\Http\Resources\HeroBadge\HeroBadgeCollection;
use App\Http\Resources\LogStatus\LogStatusCollection;
use App\Repositories\Web\Api\v1\PaymentTypeRepository;
use App\Http\Resources\AgentBadge\AgentBadgeCollection;
use App\Http\Resources\BusStation\BusStationCollection;
use App\Http\Resources\CallStatus\CallStatusCollection;
use App\Http\Resources\FinanceTax\FinanceTaxCollection;
use App\Http\Resources\CourierType\CourierTypeCollection;
use App\Http\Resources\FinanceCode\FinanceCodeCollection;
use App\Http\Resources\FinanceMeta\FinanceMetaCollection;
use App\Http\Resources\GlobalScale\GlobalScaleCollection;
use App\Http\Resources\PaymentType\PaymentTypeCollection;
use App\Http\Resources\StoreStatus\StoreStatusCollection;
use App\Http\Resources\DiscountType\DiscountTypeCollection;
use App\Http\Resources\FinanceGroup\FinanceGroupCollection;
use App\Repositories\Web\Api\v1\DelegateDurationRepository;
use App\Http\Resources\FailureStatus\FailureStatusCollection;
use App\Http\Resources\FinanceConfig\FinanceConfigCollection;
use App\Http\Resources\FinanceNature\FinanceNatureCollection;
use App\Http\Resources\DeliveryStatus\DeliveryStatusCollection;
use App\Http\Resources\FinanceAccount\FinanceAccountCollection;
use App\Http\Resources\DelegateDuration\DelegateDurationCollection;
use App\Http\Resources\FinanceMasterType\FinanceMasterTypeCollection;
use App\Http\Resources\FinanceAccountType\FinanceAccountTypeCollection;
use App\Http\Resources\FinancePaymentOption\FinancePaymentOptionCollection;

class HomeController extends Controller
{
    public function __construct(
        PaymentTypeRepository  $paymentTypeRepository,
        LogStatusRepository  $logStatusRepository,
        DelegateDurationRepository  $delegateDurationRepository
    ) {
        $this->paymentTypeRepository = $paymentTypeRepository;
        $this->logStatusRepository = $logStatusRepository;
        $this->delegateDurationRepository = $delegateDurationRepository;
    }

    public function GetAllMasterRecords()
    {
        $staffs = Staff::with('role', 'department', 'zone', 'courier_type', 'city', 'hero_badge')
            ->where(function ($query) {
                (auth()->user()->hasRole('Admin') || auth()->user()->hasRole('HQ')) ? $query : $query->where('city_id', auth()->user()->city_id);
            })
            // ->where('city_id', auth()->user()->city_id)
            ->get();


        $cities = City::with('zones', 'branch')->get();
        $zones = Zone::with('city', 'city.branch')->get();
        $agent_badges = AgentBadge::all();
        $payment_types = $this->paymentTypeRepository->all();

        $bus_stations = BusStation::with(['city', 'zone', 'gates', 'gates.bus'])->get();
        $delivery_status = DeliveryStatus::all();
        $meta = Meta::all();
        $global_status = GlobalScale::orderBy('id','asc')->get();
        $discount_types = DiscountType::orderBy('id','asc')->get();
        $gates = Gate::with('bus_station', 'bus')->get();
        $call_status = CallStatus::all();
        $store_status = StoreStatus::all();
        $log_status = $this->logStatusRepository->all();
        $delegate_durations = $this->delegateDurationRepository->all();
        $failure_statuses = FailureStatus::all();
        $courier_types = CourierType::all();
        $banks = Bank::all();
        $deductions = Deduction::all();
        $hero_badges = HeroBadge::all();
        $finance_account_types = FinanceAccountType::all();
        $finance_codes = FinanceCode::all();
        $finance_groups = FinanceGroup::all();
        $finance_master_types = FinanceMasterType::all();
        $finance_natures = FinanceNature::all();
        $finance_taxes = FinanceTax::all();
        $finance_metas = FinanceMeta::all();
        $corporate_merchants = Merchant::where('is_corporate_merchant', true)->get();
        $finance_payment_options = FinancePaymentOption::all();
        $finance_accounts = FinanceAccount::with('finance_code', 'finance_account_type', 'finance_tax', 'finance_group', 'finance_nature', 'branch')
            ->where('branch_id', auth()->user()->city->branch->id)->get();
        $branches = Branch::with(['city'])->get();
        $finance_configs = FinanceConfig::where('branch_id', auth()->user()->city->branch->id)->with(['branch', 'finance_account'])->get();
        return response()->json([
            'staffs' => new StaffCollection($staffs),
            'cities' => new CityCollection($cities->load(['zones', 'branch'])),
            'agent_badges' => new AgentBadgeCollection($agent_badges),
            // 'all_cities' => new CityCollection($all_cities),
            'zones' => new ZoneCollection($zones->load(['city', 'city.branch'])),
            'payment_types' => new PaymentTypeCollection($payment_types),
            'bus_stations' => new BusStationCollection($bus_stations->load(['city', 'city.branch', 'zone', 'gates'])),
            'delivery_status' => new DeliveryStatusCollection($delivery_status),
            'meta' => new MetaCollection($meta),
            'global_status' => new GlobalScaleCollection($global_status),
            'discount_types' => new DiscountTypeCollection($discount_types),
            'gates' => new GateCollection($gates),
            'call_status' => new CallStatusCollection($call_status),
            'store_status' => new StoreStatusCollection($store_status),
            'log_status' => new LogStatusCollection($log_status),
            'delegate_durations' => new DelegateDurationCollection($delegate_durations),
            'failure_status' => new FailureStatusCollection($failure_statuses),
            'banks' => new BankCollection($banks),
            'courier_types' => new CourierTypeCollection($courier_types),
            'hero_badges' => new HeroBadgeCollection($hero_badges),
            'deductions' => new DeductionCollection($deductions),
            'finance_account_types' => new FinanceAccountTypeCollection($finance_account_types),
            'finance_codes' => new FinanceCodeCollection($finance_codes),
            'finance_groups' => new FinanceGroupCollection($finance_groups),
            'finance_master_types' => new FinanceMasterTypeCollection($finance_master_types),
            'finance_natures' => new FinanceNatureCollection($finance_natures),
            'finance_taxes' => new FinanceTaxCollection($finance_taxes),
            'finance_accounts' => new FinanceAccountCollection($finance_accounts),
            'branches' => new BranchCollection($branches),
            'finance_config' => new FinanceConfigCollection($finance_configs->load('finance_account')),
            'finance_meta' => new FinanceMetaCollection($finance_metas),
            'finance_payment_options' => new FinancePaymentOptionCollection($finance_payment_options),
            'corporate_merchants' => new MerchantCollection($corporate_merchants)
        ]);
    }
}
