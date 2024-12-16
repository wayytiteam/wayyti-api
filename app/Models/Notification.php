<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;
use Google\Client as GoogleClient;

class Notification extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'user_id',
        'message',
        'description',
        'monthly_draw_winner_id',
        'tracked_product_id',
        'badge_id',
        'read',
        'type',
        'country'
    ];

    protected $appends = ['created_at_human'];

    protected $casts = [
        'read' => 'boolean'
    ];

    public function monthly_draw_winner(): BelongsTo
    {
        return $this->belongsTo(MonthlyDrawWinner::class);
    }

    public function badge(): BelongsTo
    {
        return $this->belongsTo(Badge::class);
    }

    public function tracked_product(): BelongsTo
    {
        return $this->belongsTo(TrackedProduct::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public static function send_notification($title, $message, $fcm_token) {
        $project_id = config('services.fcm.project_id');
        $bucket_file = 'smartsale-private-key.json';
        $local_file_path = storage_path('smartsale-private-key.json');
        $file_content = Storage::disk('s3')->get($bucket_file);
        file_put_contents($local_file_path, $file_content);
        $client = new GoogleClient();
        $client->setAuthConfig($local_file_path);
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
                "token" => $fcm_token,
                "notification" => [
                    "title" => $title,
                    "body" => $message
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
        if($response) {
            return $response;
        } else {
            return $err;
        }
        curl_close($ch);
    }

    public function getCreatedAtHumanAttribute()
    {
        return $this->created_at->diffForHumans();
    }
}
