<?php

use App\Models\Meta;
use App\Models\Account;
use App\Models\LogStatus;
use App\Models\TrackingStatus;
use App\Models\Staff;
use App\Models\City;
use App\Models\Zone;
use App\Models\GlobalScale;
use App\Models\PaymentType;
use App\Models\CallStatus;
use App\Models\StoreStatus;
use App\Models\DeliveryStatus;
use App\Models\BusStation;
use App\Models\DeliSheet;
use App\Models\Gate;
use Illuminate\Support\Str;
use Googlei18n\MyanmarTools\ZawgyiDetector;
use Illuminate\Support\Facades\Storage;

/*
 * Global helpers file with misc functions.
 */

if (!function_exists('include_route_files')) {

    /**
     * Loops through a folder and requires all PHP files
     * Searches sub-directories as well.
     *
     * @param $folder
     */
    function include_route_files($folder)
    {
        try {
            $rdi = new recursiveDirectoryIterator($folder);
            $it = new recursiveIteratorIterator($rdi);

            while ($it->valid()) {
                if (!$it->isDot() && $it->isFile() && $it->isReadable() && $it->current()->getExtension() === 'php') {
                    require $it->key();
                }

                $it->next();
            }
        } catch (Exception $e) {
            echo $e->getMessage();
        }
    }
}

if (!function_exists('getTransactionFee')) {
    function getTransactionFee()
    {
        return Meta::where('key', 'transaction_fee')->first()->value;
    }
}


if (!function_exists('getTransactionAmount')) {
    function getTransactionAmount()
    {
        return Meta::where('key', 'transaction_amount')->first()->value;
    }
}
if (!function_exists('getInsuranceFee')) {
    function getInsuranceFee()
    {
        return Meta::where('key', 'insurance_fee')->first()->value;
    }
}
if (!function_exists('getWarehousingFee')) {
    function getWarehousingFee()
    {
        return Meta::where('key', 'warehousing_fee')->first()->value;
    }
}
if (!function_exists('getTargetSaleCount')) {
    function getTargetSaleCount()
    {
        return Meta::where('key', 'target_sale')->first()->value;
    }
}
if (!function_exists('getReturnPercentage')) {
    function getReturnPercentage()
    {
        return Meta::where('key', 'return_percentage')->first()->value;
    }
}
if (!function_exists('getTargetCoupon')) {
    function getTargetCoupon()
    {
        return Meta::where('key', 'target_coupon')->first()->value;
    }
}
if (!function_exists('getVolumnTargetDateBetween')) {
    function getVolumnTargetDateBetween()
    {
        $start_date = Meta::where('key', 'target_start_date')->first()->value;
        $end_date = Meta::where('key', 'target_end_date')->first()->value;

        if ($end_date >= $start_date) {
            return true;
        }
        return false;
    }
}

if (!function_exists('getStatusId')) {
    function getStatusId($status)
    {
        return LogStatus::statusValue($status)->first()->id;
    }
}
if (!function_exists('getTrackingStatusId')) {
    function getTrackingStatusId($status)
    {
        return TrackingStatus::Status($status)->first()->id;
    }
}

if (!function_exists('voucherTracker')) {
    function voucherTracker($voucher)
    {

        if (array_key_exists('store_status_id', $voucher->getChanges()) && $voucher->store_status_id === 5) {
            $status =  getTrackingStatusId('prepare_for_delivery');
            return $status;
        }
        if (array_key_exists('store_status_id', $voucher->getChanges()) && $voucher->store_status_id === 9) {
            $status =  getTrackingStatusId('prepare_to_return');
            return $status;
        }
        if (array_key_exists('is_return', $voucher->getChanges()) && ($voucher->getOriginal('is_return') == false && $voucher->is_return == true)) {
            $status =  getTrackingStatusId('return');
            return $status;
        }
        if ((array_key_exists('delivery_status_id', $voucher->getChanges()) && $voucher->delivery_status_id != 1)) {
            if ($voucher->delivery_status_id == 8 && !$voucher->is_closed) {
                $status =  getTrackingStatusId('delivered');
                return $status;
            }
            if ($voucher->delivery_status_id == 9) {
                $status =  getTrackingStatusId('welling_to_return');
                return $status;
            }
            if ($voucher->delivery_status_id == 10) {
                $status =  getTrackingStatusId('failed_attempt');
                return $status;
            }
        }
        $tracking_statuses = [];
        if ($voucher->getOriginal('delivery_status_id') == 9 && $voucher->delivery_status_id == 2) {
            $status = getTrackingStatusId('request_to_re-deliver');
            array_push($tracking_statuses, $status);
        }
        if ($voucher->getOriginal('delivery_status_id') == 10 && $voucher->delivery_status_id == 8) {
            $status = getTrackingStatusId('change_to_success');
            array_push($tracking_statuses, $status);
        }
        if ($voucher->getOriginal('delivery_status_id') == 8 && (int) $voucher->delivery_status_id <= 4) {
            $status = getTrackingStatusId('change_to_fail');
            array_push($tracking_statuses, $status);
        }
        if ($voucher->delivery_status_id == 8 && $voucher->is_closed) {
            $status = getTrackingStatusId('delivery_success');
            array_push($tracking_statuses, $status);
        }
        if (((int) $voucher->delivery_status_id <= 4 || (int) $voucher->delivery_status_id == 10) && ($voucher->delivery_counter >= 1 && $voucher->getOriginal('outgoing_status') == null)) {
            $status = getTrackingStatusId('delivery_fail');
            array_push($tracking_statuses, $status);
        }
        return $tracking_statuses;
    }
}

if (!function_exists('pickupTracker')) {
    function pickupTracker($pickup)
    {
        $tracking_statuses = [];
        if (array_key_exists('assigned_by_id', $pickup->getChanges()) && ($pickup->getOriginal('assigned_by_id') == null && $pickup->assigned_by_id != null)) {
            $status = getTrackingStatusId('assign_for_pickup');
            array_push($tracking_statuses, $status);
        }
        if (array_key_exists('is_pickuped', $pickup->getChanges()) && ($pickup->getOriginal('is_pickuped') == false && $pickup->is_pickuped == true)) {
            $status = getTrackingStatusId('picked-up_the_parcel');
            array_push($tracking_statuses, $status);
        }
        if (array_key_exists('is_closed', $pickup->getChanges()) && ($pickup->getOriginal('is_closed') == false && $pickup->is_closed == true)) {
            $status = getTrackingStatusId('marathon_received_parcel');
            array_push($tracking_statuses, $status);
        }
        // if (empty($tracking_statuses)) {
        //     $status = getTrackingStatusId('exception');
        //     array_push($tracking_statuses, $status);
        // }
        return $tracking_statuses;
    }
}

if (!function_exists('waybillTracker')) {
    function waybillTracker($waybill)
    {
        $tracking_statuses = [];
        if (array_key_exists('is_confirm', $waybill->getChanges()) && ($waybill->getOriginal('is_confirm') == false && $waybill->is_confirm == true)) {
            $status = getTrackingStatusId('prepare_for_freight');
            array_push($tracking_statuses, $status);
        }
        if (array_key_exists('is_delivered', $waybill->getChanges()) && ($waybill->getOriginal('is_delivered') == false && $waybill->is_delivered == true)) {
            $status = getTrackingStatusId('on_the_freight');
            array_push($tracking_statuses, $status);
        }
        if (array_key_exists('is_received', $waybill->getChanges()) && ($waybill->getOriginal('is_received') == false && $waybill->is_received == true)) {
            $status = getTrackingStatusId('receive_waybill');
            array_push($tracking_statuses, $status);
        }
        return $tracking_statuses;
    }
}

if (!function_exists('setPickupId')) {
    function setPickupId($id)
    {
        session(['pickupId' => $id]);
    }
}

if (!function_exists('getPickupId')) {
    function getPickupId()
    {
        return $value = session('pickupId');
    }
}

if (!function_exists('clearPickupId')) {
    function clearPickupId()
    {
        session()->forget('pickupId');
    }
}

if (!function_exists('setVoucherId')) {
    function setVoucherId($id)
    {
        session(['voucherId' => $id]);
    }
}

if (!function_exists('getVoucherId')) {
    function getVoucherId()
    {
        return $value = session('voucherId');
    }
}

if (!function_exists('clearVoucherId')) {
    function clearVoucherId()
    {
        session()->forget('voucherId');
    }
}

if (!function_exists('arrayKeyChange')) {
    function arrayKeyChange($array, $old_key, $new_key)
    {
        $array[$new_key] = $array[$old_key];
        unset($array[$old_key]);
        return $array;
    }
}

if (!function_exists('transformedPickupsAttribute')) {
    function transformedPickupsAttribute($index, $previous, $next)
    {
        $attributes = [
            'sender_type' => ['status' => 'change_sender_type', 'previous' => $previous, 'next' => $next],
            'sender_id' => ['status' => 'change_sender_id', 'previous' => $previous, 'next' => $next],
            'opened_by'     =>  ['status' => 'assign_pickup', 'previous' => $previous, 'next' => $next],
            'note'     =>  ['status' => 'change_note', 'previous' => $previous, 'next' => $next],
            'pickup_fee'     =>  ['status' => 'change_pickup_fee', 'previous' => $previous, 'next' => $next],
            'deleted_at'   =>  ['status' => 'delete_pickup', 'previous' => $previous, 'next' => $next],
            'is_closed'     =>  ['status' => 'close', 'previous' => $previous, 'next' => $next],

        ];
        $attributes['name'] = ['status' => 'change_sender_name', 'previous' => $previous, 'next' => $next];
        $attributes['address'] = ['status' => 'change_sender_address', 'previous' => $previous, 'next' => $next];
        $attributes['phone'] = ['status' => 'change_sender_phone', 'previous' => $previous, 'next' => $next];
        $attributes['other_phone'] = ['status' => 'change_receiver_other_phone', 'previous' => $previous, 'next' => $next];
        $attributes['is_paid'] = ['status' => 'receive_payment', 'previous' => $previous, 'next' => $next];
        return isset($attributes[$index]) ? $attributes[$index] : null;
    }
}

if (!function_exists('transformedVouchersAttribute')) {
    function transformedVouchersAttribute($index, $previous, $next)
    {
        $attributes = [
            'receiver_id' =>  ['status' => 'change_receiver_id', 'previous' => $previous, 'next' => $next],
            'phone' =>  ['status' => 'change_receiver_phone', 'previous' => $previous, 'next' => $next],
            'address' =>  ['status' => 'change_receiver_address', 'previous' => $previous, 'next' => $next],
            'payment_type_id' => ['status' => 'change_payment_type', 'previous' => $previous, 'next' => $next],
            'call_status_id' => ['status' => 'change_call_status', 'previous' => $previous, 'next' => $next],
            'delivery_status_id' => ['status' => 'change_delivery_status', 'previous' => $previous, 'next' => $next],
            'store_status_id' => ['status' => 'change_store_status', 'previous' => $previous, 'next' => $next],
            'sender_city_id' =>  ['status' => 'change_from_city', 'previous' => $previous, 'next' => $next],
            'receiver_city_id' =>  ['status' => 'change_to_city', 'previous' => $previous, 'next' => $next],
            'sender_zone_id'  =>  ['status' => 'change_from_zone', 'previous' => $previous, 'next' => $next],
            'receiver_zone_id' => ['status' => 'change_to_zone', 'previous' => $previous, 'next' => $next],
            'sender_bus_station_id' => ['status' => 'change_from_bus_station', 'previous' => $previous, 'next' => $next],
            'receiver_bus_station_id' => ['status' => 'change_to_bus_station', 'previous' => $previous, 'next' => $next],
            'remark' => ['status' => 'change_note', 'previous' => $previous, 'next' => $next],
            'is_closed'     =>  ['status' => 'close', 'previous' => $previous, 'next' => $next],
            'pickup_id'     =>  ['status' => 'remove_pickup_voucher', 'previous' => $previous, 'next' => $next]
        ];
        // customer changes
        $attributes['name'] = ['status' => 'change_receiver_name', 'previous' => $previous, 'next' => $next];
        $attributes['address'] = ['status' => 'change_receiver_address', 'previous' => $previous, 'next' => $next];
        $attributes['phone'] = ['status' => 'change_receiver_phone', 'previous' => $previous, 'next' => $next];
        $attributes['other_phone'] = ['status' => 'change_receiver_other_phone', 'previous' => $previous, 'next' => $next];
        // parcel changes
        $attributes['weight'] = ['status' => 'change_item_weight', 'previous' => $previous, 'next' => $next];
        $attributes['global_scale_id'] = ['status' => 'change_global_scale', 'previous' => $previous, 'next' => $next];
        // change parcel item
        $attributes['item_name'] = ['status' => 'change_item_name', 'previous' => $previous, 'next' => $next];
        $attributes['item_qty'] = ['status' => 'change_item_qty', 'previous' => $previous, 'next' => $next];
        $attributes['item_price'] = ['status' => 'change_item_price', 'previous' => $previous, 'next' => $next];
        return isset($attributes[$index]) ? $attributes[$index] : null;
    }
}



if (!function_exists('transformedDeliSheetsAttribute')) {
    function transformedDeliSheetsAttribute($index, $previous, $next)
    {
        $attributes = [
            'delivery_id' => ['status' => 'change_delivery_man', 'previous' => $previous, 'next' => $next],
            'note'     =>  ['status' => 'change_note', 'previous' => $previous, 'next' => $next],
            'date'     =>  ['status' => 'change_deliver_date', 'previous' => $previous, 'next' => $next],
            'is_closed'     =>  ['status' => 'close', 'previous' => $previous, 'next' => $next],
            'is_paid' => ['status' => 'receive_payment', 'previous' => $previous, 'next' => $next],
            'deleted_at' => ['status' => 'delet_delisheet', 'previous' => $previous, 'next' => $next]
        ];
        return isset($attributes[$index]) ? $attributes[$index] : null;
    }
}

if (!function_exists('transformedWaybillsAttribute')) {
    function transformedWaybillsAttribute($index, $previous, $next)
    {
        $attributes = [
            'delivery_id' => ['status' => 'change_delivery_man', 'previous' => $previous, 'next' => $next],
            'from_bus_station_id' => ['status' => 'change_from_bus_station', 'previous' => $previous, 'next' => $next],
            'to_bus_station_id' => ['status' => 'change_to_bus_station', 'previous' => $previous, 'next' => $next],
            'gate_id' => ['status' => 'change_waybill_gate', 'previous' => $previous, 'next' => $next],
            'from_city_id' => ['status' => 'change_from_city', 'previous' => $previous, 'next' => $next],
            'to_city_id' => ['status' => 'change_to_city', 'previous' => $previous, 'next' => $next],
            'actual_bus_fee' => ['status' => 'change_actual_bus_fee', 'previous' => $previous, 'next' => $next],
            'is_received' => ['status' => 'receive_waybill', 'previous' => $previous, 'next' => $next],
            'is_closed' => ['status' => 'close', 'previous' => $previous, 'next' => $next],
            'is_paid' => ['status' => 'receive_payment', 'previous' => $previous, 'next' => $next],
            'note'     =>  ['status' => 'change_note', 'previous' => $previous, 'next' => $next],
            'is_confirm'     =>  ['status' => 'confirmed_waybill', 'previous' => $previous, 'next' => $next],
            'is_delivered'     =>  ['status' => 'delivered_waybill', 'previous' => $previous, 'next' => $next],
            'deleted_at' => ['status' => 'delet_delisheet', 'previous' => $previous, 'next' => $next]
        ];
        return isset($attributes[$index]) ? $attributes[$index] : null;
    }
}

if (!function_exists('transformedMerchantSheetsAttribute')) {
    function transformedMerchantSheetsAttribute($index, $previous, $next)
    {
        $attributes = [
            'is_paid'     =>  ['status' => 'receive_payment', 'previous' => $previous, 'next' => $next],
            'deleted_at' => ['status' => 'delete_msf', 'previous' => $previous, 'next' => $next],
            'note'     =>  ['status' => 'change_note', 'previous' => $previous, 'next' => $next],
        ];
        return isset($attributes[$index]) ? $attributes[$index] : null;
    }
}

if (!function_exists('transformedReturnSheetsAttribute')) {
    function transformedReturnSheetsAttribute($index, $previous, $next)
    {
        $attributes = [
            'is_paid'     =>  ['status' => 'receive_payment', 'previous' => $previous, 'next' => $next],
            'deleted_at' => ['status' => 'delete_msf', 'previous' => $previous, 'next' => $next]
        ];
        return isset($attributes[$index]) ? $attributes[$index] : null;
    }
}
if (!function_exists('computeRoundingAmount')) {
    function computeRoundingAmount($total_amount)
    {
        ## 0 -- 25 -- 50 -- 75 -- 100
        # if get_rounded_amt == true
        $value = 0;

        $num = $total_amount;
        $get_last_no = substr($num, -2);
        if ($get_last_no > 0) {
            $rouding = 100 - $get_last_no;
            $num += $rouding;
        }
        return $num;
    }
}

if (!function_exists('getLunchAmount')) {
    function getLunchAmount()
    {
        return Meta::where('key', 'lunch')->first()->value;
    }
}
if (!function_exists('getDeliveryCommission')) {
    function getDeliveryCommission()
    {
        return Meta::where('key', 'delivery_commission')->first()->value;
    }
}
if (!function_exists('getPickupCommission')) {
    function getPickupCommission()
    {
        return Meta::where('key', 'pickup_commission')->first()->value;
    }
}
if (!function_exists('getBranchAccountId')) {
    function getBranchAccountId()
    {
        return 1;
    }
}
if (!function_exists('getAgentBaseRate')) {
    function getAgentBaseRate()
    {
        return Meta::where('key', 'agent_fee_base_rate')->first()->value;
    }
}
if (!function_exists('getConvertedString')) {
    function getConvertedString($note)
    {
        if ($note) {
            $detector = new ZawgyiDetector();
            $score = $detector->getZawgyiProbability($note);

            $score = number_format($score, 6);

            if ($score > 0.001) {
                $note = Rabbit::zg2uni($note);
            }
        }
        return $note;
    }
}
if (!function_exists('getConvertedUni2Zg')) {
    function getConvertedUni2Zg($note)
    {

        return Rabbit::uni2zg($note);
    }
}

if (!function_exists('getImmediatelyRreturnFee')) {
    function getImmediatelyRreturnFee()
    {
        return Meta::where('key', 'immediately_return_fee')->first()->value;
    }
}

if (!function_exists('getAttendanceQrCode')) {
    function getAttendanceQrCode()
    {
        return Meta::where('key', 'attendance')->first()->value;
    }
}

if (!function_exists('generateAttendanceQrCode')) {
    function generateAttendanceQrCode()
    {
        $meta_attendance = Meta::where('key', 'attendance')->first();
        $meta_attendance->value = (string) Str::uuid();
        $meta_attendance->save();
        return $meta_attendance->value;
    }
}

if (!function_exists('getBranchCityId')) {
    function getBranchCityId()
    {
        return auth()->user()->city_id;
    }
}

if (!function_exists('getHqAccount')) {
    function getHqAccount()
    {
        return Account::where('accountable_type', 'HQ')->first();
    }
}
if (!function_exists('getMerchantRewardPercentage')) {
    function getMerchantRewardPercentage()
    {
        return Meta::where('key', 'merchant_reward')->first()->value;
    }
}
if (!function_exists('loggedValue')) {
    function loggedValue($status, $value)
    {
        switch ($status) {
            case 3:
            case 45:
                return Staff::find($value)->username;
                break;
            case 4:
                return $value != null ? intval($value) . ' Ks' : null;
                break;
            case 10:
            case 17:
                return City::find($value)->name;
                break;
            case 11:
            case 18:
                return Zone::find($value)->name;
                break;
            case 22:
                return GlobalScale::find($value)->description;
                break;
            case 23:
                return $value !== null ? $value . ' Kg' : null;
                break;
            case 24:
                return PaymentType::find($value)->name_mm;
                break;
            case 25:
                return CallStatus::find($value)->status_mm;
                break;
            case 26:
                return StoreStatus::find($value)->status_mm;
                break;
            case 27:
                return DeliveryStatus::find($value)->status_mm;
                break;
            case 48:
            case 49:
                return BusStation::find($value)->name;
                break;
            case 50:
                return Gate::find($value)->name;
                break;
            case 52:
            case 58:
            case 78:
                return null;
                break;
            default:
                return (int) $value;
        }
    }
}
if (!function_exists('isHero')) {
    function isHero($sheet)
    {
        return ($sheet->is_came_from_mobile && $sheet->acted_hero()->exists()
            && $sheet->acted_hero->department_id == 5
            && $sheet->acted_hero->role_id == 5);
    }
}
if (!function_exists('isFreelancer')) {
    function isFreelancer($acted_hero)
    {
        return ($acted_hero->staff_type === 'Freelance'
            && $acted_hero->department_id == 5
            && $acted_hero->role_id == 5);
    }
}
if (!function_exists('isFreelancerCar')) {
    function isFreelancerCar($acted_hero)
    {
        return ($acted_hero->staff_type === 'Freelance Car'
            && $acted_hero->department_id == 5
            && $acted_hero->role_id == 5);
    }
}
// spare function to replace !isFreelancer() condition to earn freelancer point
if (!function_exists('isBlackList')) {
    function isBlackList($acted_hero)
    {
        return ($acted_hero->staff_type === 'BlackList'
            && $acted_hero->department_id == 5
            && $acted_hero->role_id == 5);
    }
}
// if (!function_exists('getPlatform')) {
//     function getPlatform($url)
//     {
//         if ($url[0] == 'api') {
//             if ($url[2] == 'vouchers') {
//                 return 'Marathon Dashboard';
//             } else if ($url[2] == 'merchant_dashboard') {
//                 return 'Merchant Dashboard';
//             } else {
//                 return implode(',', $url);
//             }
//         } else if ($url[0] == 'mobile') {
//             return 'Merchant App';
//         } else if ($url[0] == 'thirdparty') {
//             return 'Third-party Platform';
//         } else if ($url[0] == 'supermerchant') {
//             return 'Supermerchant Platform';
//         } else {
//             return implode('/', $url);
//         }
//     }
// }
// if (!function_exists('formatForFirebase')) {
//     function formatForFirebase($value, $new_keys)
//     {
//         $formatted_array = [];
//         foreach ($new_keys as $key => $new) {
//             $result = is_array($new) ? $new[0] : $new;
//             $formatted_array[$key] = is_array($new) ? $value->$result[$new[1]] : $value->$new;
//         }
//         return  $formatted_array;
//     }
// }
if (!function_exists('formatForFirebase')) {
    function formatForFirebase($relationships, $new_keys, array  $additionals = [])
    {
        $assoc = [];
        foreach ($relationships as $parent_key => $value) {
            foreach ($new_keys as $key => $new) {
                $result = is_array($new) ? $new[0] : $new;
                $formatted_array[$key] = is_array($new) ? $value->$result[$new[1]] : $value->$new;
                // if (!empty($additionals)) {
                //     \Log::info('not empty');
                //     foreach ($additionals as $add_key => $add_val) {
                //         $formatted_array[$add_key] = $add_val;
                //     }
                // }
                if (is_array($new) && !empty($additionals)) {
                    if ($result === 'tracking_status') {
                        foreach ($additionals as $add_key => $add_val) {
                            if (is_array($add_val)) {
                                $city = City::findOrFail(auth()->user()->city_id);
                                $to_city = $value->$result['id'] === 3 ?
                                    $add_val[0] : ($value->$result['id'] === 6 ? $add_val[1] : (($city) ?
                                        $city->name : null));
                                $formatted_array['to_city_en'] = $to_city;
                                $to_city_mm = $value->$result['id'] === 3 ?
                                    $add_val[0] : ($value->$result['id'] === 6 ? $add_val[1] : (($city) ?
                                        $city->name_mm : null));
                                $formatted_array['to_city_mm'] = $to_city_mm;
                            } else {
                                $formatted_array[$add_key] = $add_val;
                            }
                        }
                    }
                }
            }
            $assoc[$parent_key] = $formatted_array;
        }
        return $assoc;
    }
}
if (!function_exists('AttachmentFormatFirebase')) {
    function AttachmentFormatFirebase($attachments, $path)
    {
        $attach_results = [];
        if ($attachments == null) {
            return;
        }
        if (!is_countable($attachments)) {
            $date_path = $attachments->created_at->format('F-Y');
            $exists = Storage::disk('dospace')->exists($path . '/' . $date_path . '/' . $attachments->image);
            $large = Storage::disk('dospace')->exists($path . '/large/' . $date_path . '/' . $attachments->image);
            $medium = Storage::disk('dospace')->exists($path . '/medium/' . $date_path . '/' . $attachments->image);
            $small = Storage::disk('dospace')->exists($path . '/small/' . $date_path . '/' . $attachments->image);
            if ($exists) {
                $url = Storage::url($path . '/' . $date_path . '/' . $attachments->image);
            } else if ($large) {
                $url = Storage::url($path . '/large/' . $date_path . '/' . $attachments->image);
            } else if ($medium) {
                $url = Storage::url($path . '/medium/' . $date_path . '/' . $attachments->image);
            } else if ($small) {
                $url = Storage::url($path . '/small/' . $date_path . '/' . $attachments->image);
            } else {
                $url = null;
            }
            $attachments = array_only($attachments->toArray(), ['id']);
            $attachments['image'] = $url;
            return $attachments;
        }
        foreach ($attachments as $result) {
            $date_path = $result->created_at->format('F-Y');
            $exists = Storage::disk('dospace')->exists($path . '/' . $date_path . '/' . $result->image);
            $large = Storage::disk('dospace')->exists($path . '/large/' . $date_path . '/' . $result->image);
            $medium = Storage::disk('dospace')->exists($path . '/medium/' . $date_path . '/' . $result->image);
            $small = Storage::disk('dospace')->exists($path . '/small/' . $date_path . '/' . $result->image);
            if ($exists) {
                $url = Storage::url($path . '/' . $date_path . '/' . $result->image);
            } else if ($large) {
                $url = Storage::url($path . '/large/' . $date_path . '/' . $result->image);
            } else if ($medium) {
                $url = Storage::url($path . '/medium/' . $date_path . '/' . $result->image);
            } else if ($small) {
                $url = Storage::url($path . '/small/' . $date_path . '/' . $result->image);
            } else {
                $url = null;
            }
            if ($url != null) {
                array_push($attach_results, ['id' => $result->id, 'image' => $url]);
            }
            if ($path == 'signature') {
                array_push($attach_results, ['is_sign' => $result->is_sign]);
            }
        }
        return $attach_results;
    }
}

if (!function_exists('formatVoucherParcelsForFirebase')) {
    function formatVoucherParcelsForFirebase($parcel, $new_keys, array  $additionals = [])
    {
        foreach ($new_keys as $key => $new) {
            // $formatted_array[$key] =  $parcel->$new;
            $result = is_array($new) ? $new[0] : $new;
            $formatted_array[$key] = is_array($new) ? $parcel->$result[$new[1]] : $parcel->$new;
        }
        if (!empty($additionals)) {
            $attach_results = AttachmentFormatFirebase($additionals[1], $additionals[2]);
            $formatted_array[$additionals[0]] = $attach_results;
        }
        return $formatted_array;
    }
}

// use in voucher observer
if (!function_exists('failedAttemptFirstTime')) {
    function failedAttemptFirstTime($voucher)
    {
        return ($voucher->delivery_status_id <= 4 && $voucher->getOriginal('outgoing_status') == 0)
            && ($voucher->delivery_counter >= 1 && $voucher->getOriginal('delivery_counter') == 1)
            && ($voucher->getOriginal('is_manual_return') == 0); //close delisheed
        // && ($voucher->getOriginal('delivery_status_id') == 10 || 8);
    }
}
// use in voucher observer
if (!function_exists('pendingReturnFromFailedAttempt')) {
    function pendingReturnFromFailedAttempt($voucher)
    {
        return ($voucher->delivery_counter >= 1 && $voucher->getOriginal('outgoing_status') == null);
    }
}
// if (!function_exists('deliveredVoucherFromFailAtttempt')) {
//     function deliveredVoucherFromFailAtttempt($voucher)
//     {
//         return ($voucher->delivery_status_id == 8 && $voucher->getOriginal('outgoing_status') == null)
//             && ($voucher->delivery_counter >= 1 && $voucher->getOriginal('store_status_id') == 8);
//     }
// }
// use in voucher observer
if (!function_exists('originalFailedAttemptVoucher')) {
    function originalFailedAttemptVoucher($voucher)
    {
        return ($voucher->getOriginal('delivery_status_id') <= 4 && $voucher->getOriginal('outgoing_status') == 0)
            && ($voucher->delivery_counter >= 1 && $voucher->getOriginal('delivery_counter') > 1); //close delisheed
    }
}
if (!function_exists('checkFailedAttempt')) {
    function checkFailedAttempt($voucher)
    {
        return ($voucher->delivery_status_id <= 4 && $voucher->getOriginal('outgoing_status') == 0)
            && ($voucher->delivery_counter >= 1); //close delisheed
        // && ($voucher->getOriginal('delivery_status_id') == 10 || 8);
    }
}

if (!function_exists('merchantAppCompleteVoucherList')) {
    function merchantAppCompleteVoucherList()
    {
        return [
            'customer:id,name,phone,other_phone,address',
            'receiver_city:id,name,name_mm',
            'receiver_zone:id,name,name_mm,is_deliver',
            'parcels.parcel_items:id,parcel_id,item_name,item_qty,item_price,product_id',
            'parcels.parcel_items.product:id,sku,item_name,item_price,lwh,weight',
            'parcels.parcel_items.product.attachment:id,image,created_at,resource_id'
        ];
    }
}
if (!function_exists('merchantAppIncompleteVoucherList')) {
    function merchantAppIncompleteVoucherList()
    {
        return [
            'parcels.parcel_items:id,parcel_id,item_name,item_qty,item_price,product_id',
            'parcels.parcel_items.product:id,sku,item_name,item_price,lwh,weight',
            'parcels.parcel_items.product.attachment:id,image,created_at,resource_id'
        ];
    }
}
