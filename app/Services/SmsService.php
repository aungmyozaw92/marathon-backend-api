<?php
 
namespace App\Services;

use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use GuzzleHttp\Exception\RequestException;
use App\Services\Interfaces\SmsServiceInterface;



class SmsService implements SmsServiceInterface
{
    private $key = '';
    private $baseURL = '';
    private $verifyRequestURL = '';
    private $verifyURL = '';
    private $sendURL = '';
    private $shortURL = '';

    public function __construct()
    {   
        $this->key = config('services.sms.key');
        $this->baseURL = config('services.sms.base_url');
        $this->verifyRequestURL = config('services.sms.verify_url') . config('services.sms.verify_request');
        $this->verifyURL = config('services.sms.verify_url') . config('services.sms.verify');
        $this->sendURL = config('services.sms.base_url') . config('services.sms.send_api');
        $this->shortURL = config('services.sms.base_url') . config('services.sms.short_url');
    }

    public function sendSmsRequest($phone, $data, $type=null)
    {
        if ($type == 'transaction_confirm') {
            $message = $data['transaction_no']. ' Amount - ' .$data['amount'] . 'K is transfered to your '.$data['account_name'].' account';
        }else{
            $short_url = $this->getShortUrl($data);
            $message = 'လူကြီးမင်း၏ပါဆယ်ကိုမာရသွန်မှတာဝန်ယူပို့ဆောင်ပေးနေပါပြီ။'.$short_url;
        }

        try {
            $data = [
                'to' => $phone,
                'message' => $message,
                "sender" => "Marathon"
            ];
            $ch = curl_init($this->sendURL);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Authorization: Bearer ' . $this->key,
                'Content-Type: application/json'
            ]);
            $result = curl_exec($ch);
            // dd($result);
        } catch (RequestException $exc) {
            Log::error($exc->getMessage());
            throw new Exception('Unable to send message');
        }

        return 0;
    }

    //get short url with tracking 
    public function getShortUrl($data)
    {  
        $url = 'https://www.marathonmyanmar.com/tracking?voucher_invoice='.$data['invoice_no'].'%26phone_number='.$data['phone'];
        try { 
            // $params = 'access-token='.$this->key.'&link='.$url;
            $params = array(
                'access-token' => $this->key,
                'link' => $url,
            );
            $data = http_build_query($params);
            $ch = curl_init($this->shortURL."?".$data);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Authorization: Bearer ' . $this->key,
                'Content-Type: application/json'
            ]);

            $result = curl_exec($ch);
            $json = json_decode($result, true);
   
           return ($json['data']['shortUrl']);
        }
        catch(RequestException $exc)
        {
            Log::error($exc->getMessage());
            throw new Exception('Unable to get short url');
        }

        return 0;
    }

    public function verifyRequest($phone)
    {  
        try { 
            $params = array(
                'access-token' => $this->key,
                'number' => $phone,
                'brand_name' => 'Marathon'
            );
            $data = http_build_query($params);
            $ch = curl_init($this->verifyRequestURL."?".$data);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Authorization: Bearer ' . $this->key,
                'Content-Type: application/json'
            ]);

            $result = curl_exec($ch);
            $response = json_decode($result, true);

            if($response['status'] === true){
                return $response['request_id'];
            }

        }catch(RequestException $exc){
            Log::error($exc->getMessage());
            throw new Exception('Unable to send verification code');
        }
        return 0;
    }

    public function verify($request_id, $code)
    {
        
        try { 
            $params = array(
                'access-token' => $this->key,
                'request_id' => $request_id,
                'code' => $code
            );
            $data = http_build_query($params);
            $ch = curl_init($this->verifyURL."?".$data);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Authorization: Bearer ' . $this->key,
                'Content-Type: application/json'
            ]);

            $result = curl_exec($ch);
            $response = json_decode($result, true);

            if($response['status'] === true){
                return true;
            }  
            
            
        }catch(RequestException $exc){
            Log::error($exc->getMessage());
            throw new Exception('Unable to send verification code');
        }

        return false;
    }
    // public function verifyRequest($phone)
    // {     
    //     try {            
    //         $response = Http::get($this->verifyRequestURL, [
    //             'access-token' => $this->key,
    //             'number' => $phone,
    //             'brand_name' => config('app.name')
    //         ]);
    //         if($response->ok())
    //         {
    //             $respBody = $response->json();
    //             return $respBody["request_id"];
    //         }
    //     }
    //     catch(RequestException $exc)
    //     {
    //         Log::error($exc->getMessage());
    //         throw new Exception('Unable to send verification code');
    //     }

    //     return 0;
    // }

    // public function verify($request_id, $code)
    // {
    //     $response = Http::get($this->verifyURL, [
    //         'access-token' => $this->key,
    //         'request_id' => $request_id,
    //         'code' => $code
    //     ]);

    //     if($response->ok())
    //     {
    //         return $response['status'];
    //     }

    //     return false;
    // }

}
