<?php

namespace App\Http\Controllers\Mobile\Api\v1\Delivery;

use App\Http\Controllers\Controller;

class NotificationController extends Controller
{
    public function sendMessage()
    {
        $fcmUrl = 'https://fcm.googleapis.com/fcm/send';
        $token='fhiyx_YhwDM:APA91bFdtjMN4vjQ_3XZblwGnPQkhf0Eit81WFnJO21mVSLSZxDxHq8GPtAtpBMLgp5ngOsAF9z1HsPtxWcjPRtdUvDMx7Z6zMMKAmjN1yTYz48w-jp8g9D6s6iwDJv523qZeHfqfbwJ';

        $notification = [
            'body' => 'this is test',
            'sound' => true,
        ];
        
        $extraNotificationData = ["message" => $notification,"moredata" =>'dd'];

        $fcmNotification = [
            //'registration_ids' => $tokenList, //multple token array
            'to'        => $token, //single token
            'notification' => $notification,
            'data' => $extraNotificationData
        ];

        $headers = [
            'Authorization: key=AAAAJRPG6zg:APA91bG8Mfcw8Q1YIfX93No8BcyK5F8z9Nw7VuuMxcaHj8uzbUxTniIs0EqOOASxRzP7wznvcpcDpLIzfCkbpjtkKJjtIVxO2TEQS8PY3lf6AC8FwxEDgMINcFAWvep8QJ3LVc7Y2uWk',
            'Content-Type: application/json'
        ];


        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL,$fcmUrl);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fcmNotification));
        $result = curl_exec($ch);
        curl_close($ch);


        dd($result);
    }
}

