<?php

namespace App\Services;

use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;
use Kreait\Firebase\Messaging\RawMessageFromArray;

class FirebaseService
{
    protected $realtime_database;
    protected $messaging;
    public function __construct()
    {
        // $factory = (new Factory)->withServiceAccount((__DIR__ . '/firebase.json'));
        $factory = (new Factory)->withServiceAccount((base_path('firebase.json')))->withDatabaseUri(\Config::get('services.firebase_services.realtime_database_url'));
        $this->realtime_database = $factory->createDatabase();
        $this->messaging = $factory->createMessaging();
    }
    /**
     * curent using functions
     */
    public function sendNotification(array $data)
    {
		// return $this->saveNoti($data);
        $data['title'] = 'MarathonMyanmar';
        $message = new RawMessageFromArray([
            'android' => [
                // https://firebase.google.com/docs/reference/fcm/rest/v1/projects.messages#androidconfig
                'ttl' => '3600s',
                'priority' => 'normal',
                'data' => array_only($data,['title','body','type','document','invoice']),
            ],
            'apns' => [
                // https://firebase.google.com/docs/reference/fcm/rest/v1/projects.messages#apnsconfig
                'headers' => [
                    'apns-priority' => '10',
                ],
                'payload' => [
                    'aps' => [
                        'alert' => [
                            'title' => '$GOOG up 1.43% on the day',
                            'body' => '$GOOG gained 11.80 points to close at 835.67, up 1.43% on the day.',
                        ],
                        'badge' => 42,
                    ],
                ],
            ],
            'webpush' => [
                // https://firebase.google.com/docs/reference/fcm/rest/v1/projects.messages#webpushconfig
                'notification' => [
                    'title' => '$GOOG up 1.43% on the day',
                    'body' => '$GOOG gained 11.80 points to close at 835.67, up 1.43% on the day.',
                    'icon' => 'https://my-server/icon.png',
                ],
            ],
            'fcm_options' => [
                // https://firebase.google.com/docs/reference/fcm/rest/v1/projects.messages#fcmoptions
                'analytics_label' => 'some-analytics-label'
            ]
		]);
		// $device_tokens = $this->validateFCMTokens($data['device_tokens']);
		// if(empty($device_tokens))return false;
		try{
			$device_tokens = $this->validateFCMTokens($data['device_tokens']);
			$this->messaging->sendMulticast($message, $device_tokens);
			$data['sent_by'] = auth()->user() !=null? auth()->user()->name:'Customer';
			$data['sent_at'] = date('Y-m-d H:i:s');
			$this->saveNoti($data);
		}catch(\Throwable $e) {
			\Log::error($e->getMessage());
		}
	}
	public function sendAnnouncement(array $data)
	{
		$data['title'] = 'MarathonMyanmar';
		$message = new RawMessageFromArray([
			'android' => [
				// https://firebase.google.com/docs/reference/fcm/rest/v1/projects.messages#androidconfig
				'ttl' => '3600s',
				'priority' => 'normal',
				'data' => array_only($data, ['type', 'link']),
				'notification' => array_only($data, ['title', 'body'])
			],
			'apns' => [
				// https://firebase.google.com/docs/reference/fcm/rest/v1/projects.messages#apnsconfig
				'headers' => [
					'apns-priority' => '10',
				],
				'payload' => [
					'aps' => [
						'alert' => [
							'title' => '$GOOG up 1.43% on the day',
							'body' => '$GOOG gained 11.80 points to close at 835.67, up 1.43% on the day.',
						],
						'badge' => 42,
					],
				],
			],
			'webpush' => [
				// https://firebase.google.com/docs/reference/fcm/rest/v1/projects.messages#webpushconfig
				'notification' => [
					'title' => '$GOOG up 1.43% on the day',
					'body' => '$GOOG gained 11.80 points to close at 835.67, up 1.43% on the day.',
					'icon' => 'https://my-server/icon.png',
				],
			],
			'fcm_options' => [
				// https://firebase.google.com/docs/reference/fcm/rest/v1/projects.messages#fcmoptions
				'analytics_label' => 'some-analytics-label'
			]
		]);
		try {
			$device_tokens = $this->validateFCMTokens($data['device_tokens']);
			$this->messaging->sendMulticast($message, $device_tokens);
			$data['sent_by'] = auth()->user()->name;
			$data['sent_at'] = date('Y-m-d H:i:s');
			$this->saveNoti($data);
		} catch (\Throwable $e) {
			\Log::error($e->getMessage());
		}
	}
	public function validateFCMTokens($validateTokens)
	{
		$validatedTokens = [];
		foreach($validateTokens as $validated) {
			try {
				$this->messaging->getAppInstance($validated);
				array_push($validatedTokens,$validated);
			} catch (\Throwable $e) {
				\Log::error($e->getMessage());
			}
			// $this->messaging->getAppInstance($validated);
			// $validatedTokens = array_push($validatedTokens,$validated);
		}
		return $validatedTokens;
	}
	public function saveNoti($data) {
		try{
			$this->realtime_database->getReference('Notifications/' . preg_replace('/[^A-Za-z0-9]/', 'x', $data['receiver']) . '/' . $data['invoice'])
				->set((array_only($data, ['receiver', 'invoice', 'body', 'type', 'sent_by', 'sent_at'])));
		}catch (\Throwable $e){
			\Log::error($e->getMessage());
		}
		return;
		// return $this->realtime_database->getReference('Notifications/'. preg_replace('/[^A-Za-z0-9]/', 'x', $data['receiver']).'/')
		// 	->push(array_only($data,['receiver','invoice','body','type','sent_by','sent_at']));
		// return $this->realtime_database->getReference('Notifications/' . preg_replace('/[^A-Za-z0-9]/', 'x', $data['receiver']) . '/' . $data['invoice'])
		// 	->set((array_only($data, ['receiver', 'invoice', 'body', 'type', 'sent_by', 'sent_at'])));
	}
	public function cleanNotification($data) {
		$ref = $this->realtime_database->getReference('Notifications/' . preg_replace('/[^A-Za-z0-9]/', 'x', $data['receiver']) . '/' . $data['invoice']);
		\Log::info($ref);
		return $ref->remove();
	}
	public function sendInternalMessage($data) {
		$data['sender'] = auth()->user() != null ? auth()->user()->name : 'Unknown';
		$data['sent_at'] = date('Y-m-d H:i:s');
		try{
			$this->realtime_database->getReference('Internal-Messages/'.$data['receiver_department'].'/')
				->push(array_only($data,['invoice','body','sender','sent_at']));
		}catch (\Throwable $e){
			\Log::error($e->getMessage());
		}
		return;
	}
    /*
    * functions  using firestore  document_id
    */
}