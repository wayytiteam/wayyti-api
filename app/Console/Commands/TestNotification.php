<?php

namespace App\Console\Commands;

use App\Models\Notification;
use App\Models\User;
use Illuminate\Console\Command;
use Google\Client as GoogleClient;

class TestNotification extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:notification';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = $this->ask('email: ');
        $user = User::where('email', $email)->first();

        try {
            $project_id = config('services.fcm.project_id');
            $credentials_file_path = base_path('smartsale-private-key.json');
            $client = new GoogleClient();
            $client->setAuthConfig($credentials_file_path);
            $client->addScope('https://www.googleapis.com/auth/firebase.messaging');
            $client->fetchAccessTokenWithAssertion();
            $token = $client->getAccessToken();
            $access_token = $token['access_token'];

            $headers = [
                "Authorization: Bearer $access_token",
                'Content-type: application/json'
            ];

            $data = [
                "message" => [
                    "token" => $user->fcm_token,
                    "notification" => [
                        "title" => "Test Notification",
                        "body" => "Hope this works"
                    ],
                ]
            ];

            $payload = json_encode($data);

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, "https://fcm.googleapis.com/v1/projects/{$project_id}/messages:send");
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
            curl_setopt($ch, CURLOPT_VERBOSE, true);
            $response = curl_exec($ch);
            $err = curl_error($ch);
            curl_close($ch);
            if ($err) {
                print_r($err);
            } else {
                print_r($response);
            }
            // print_r(curl_exec($ch));
        } catch (\Exception $e) {
            echo($e->getMessage());
        }
    }
}
