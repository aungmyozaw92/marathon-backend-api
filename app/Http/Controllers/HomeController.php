<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\City;
use App\Models\Zone;
use App\Models\BusStation;
use App\Models\Gate;
use App\Models\GlobalScale;
use App\Models\PaymentType;
use App\Models\TrackingStatus;
use App\Http\Resources\BusStation\BusStationCollection;
use App\Models\Bank;
use App\Models\DeliSheet;
use App\Models\FailureStatus;
use App\Models\Merchant;
use App\Models\Pickup;
use App\Models\Voucher;
use Illuminate\Support\Facades\Storage;
use App\Services\FirebaseService;
class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
	private $firebaseService;
	public function __construct(FirebaseService $firebaseService)
	{
		$this->middleware('auth');
		$this->firebaseService = $firebaseService;
	}
    public function index()
    {
        return view('home');
    }
	public function sendNoti() {
		$payload = [
			'home_page' => "Jojo",
			'receiver' => 'Jojo',
			'device_tokens' => ['id'=>1,'token'=> 'chI8VgjTskVepdqieNemfM:APA91bH6WepEnvqMK0ynmU4XHfZEBbwgE4vuMhCBH6fZdJEd4CWB51QrahBQoEnzHqst1XxjM1EHvs_qCjrk9VLrspNA93pVxWnN4aWEpA_l5j3mn10H--9FRSTxYXJ2v12i8J0XG9iH'],
			'type' => "Can't_Delivered",
			'body' => 'သင်ပို့ဆောင်လိုက်သော ပါဆယ်များမှ ပို့မရသော ပါဆယ် ရှိပါသည်ffs။',
			'document' => null,
			'invoice' => "D001"
		];
		$this->firebaseService->sendNotification($payload);
	}

}
