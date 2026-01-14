<?php

namespace App\Models;

use Carbon\Carbon;
use Google\Client;
use Google\Service\AndroidPublisher;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class Subscription extends Model
{
    use HasUuids;

    protected $keyType = 'string';

    protected $fillable = [
        'user_id',
        'type',
        'on_trial_mode',
        'has_subscribed',
        'product_id',
        'purchase_token',
        'sub_id',
    ];

    protected $appends = ['server_time'];

    public function getServerTimeAttribute()
    {
        return Carbon::parse(now())->toDateTimeString();
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    protected $casts = [
        'has_subscribed' => 'boolean',
        'on_trial_mode' => 'boolean',
    ];

    public static function verify_subscription($package_name, $purchase_token, $product_id)
    {
        $client = new Client;
        $bucket_file = 'google-play-service-key.json';
        $local_file_path = storage_path('google-play-service-key.json');
        $file_content = Storage::disk('s3')->get($bucket_file);
        file_put_contents($local_file_path, $file_content);
        $client->setAuthConfig($local_file_path);
        // $client->addScope(AndroidPublisher::ANDROIDPUBLISHER);
        $client->addScope('https://www.googleapis.com/auth/androidpublisher');
        $service = new AndroidPublisher($client);
        try {
            $subscription = $service->purchases_subscriptions->get($package_name, $purchase_token, $product_id);

            return $subscription;
        } catch (\Exception $e) {
            return $e;
        }
    }
}
