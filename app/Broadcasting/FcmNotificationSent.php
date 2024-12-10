<?php

namespace App\Broadcasting;

use App\Models\User;
use Google\Auth\ApplicationDefaultCredentials;
use Google\Client as GoogleClient;
use Illuminate\Notifications\Notification;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Client as GuzzleHttpClient;

class FcmNotificationSent
{
    /**
     * Create a new channel instance.
     */
    protected $client;
    protected $project_id;

    public function __construct()
    {
        $this->project_id = env('FIREBASE_PROJECT_ID');
        $service_key_file = base_path('smartsale-private-key.json');
        if (!file_exists($service_key_file)) {
            throw new \Exception("Service account file not found");
        }
        $scopes = ['https://www.googleapis.com/auth/firebase.messaging'];
        $middleware = ApplicationDefaultCredentials::getMiddleware($scopes);
        $stack = HandlerStack::create();
        $stack->push($middleware);

        $this->client = new GuzzleHttpClient([
            'handler' => $stack,
            'base_uri' => 'https://fcm.googleapis.com',
            'auth' => 'google_auth',
            'debug' => fopen(storage_path('logs/guzzle_debug.log/'), 'a'),
        ]);
    }

    /**
     * Authenticate the user's access to the channel.
     */
    public function send($notifiable, Notification $notification)
    {
        // $message_payload = $notification->locale($notifiable);

        // $response = $this->client->post("/v1/projects/{$this->project_id}/message:send", [
        //     'json' => $message_payload,
        // ]);

        return '';
    }
}
