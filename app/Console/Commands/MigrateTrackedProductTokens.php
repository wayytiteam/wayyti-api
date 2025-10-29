<?php

namespace App\Console\Commands;

use App\Models\GoogleProduct;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MigrateTrackedProductTokens extends Command
{
    protected $signature = 'migrate:tracked-product-tokens {--batch=100}';
    protected $description = 'Fetch and update Oxylabs product tokens for GoogleProduct entries without tokens.';

    public function handle()
    {
        $batchSize = (int) $this->option('batch') ?: 100;
        $username = env('OXYLABS_USERNAME');
        $password = env('OXYLABS_PASSWORD');
        $url = "https://realtime.oxylabs.io/v1/queries";

        $this->info("Starting token migration (batch size: {$batchSize})");

        GoogleProduct::whereNull('product_token')
            ->whereNotNull('product_id')
            ->whereHas('tracked_products')
            ->chunkById($batchSize, function ($chunk) use ($url, $username, $password) {
                foreach ($chunk as $product) {
                    try {
                        $this->info("Fetching token for: {$product->title}");

                        $response = Http::withBasicAuth($username, $password)
                            ->timeout(40)
                            ->post($url, [
                                'source' => 'google_shopping_search',
                                'query' => $product->title,
                                'geo_location' => $product->country ?? 'US',
                                'domain' => 'com',
                                'parse' => true,
                            ]);

                        if ($response->failed()) {
                            Log::warning("Failed: {$product->title} ({$response->status()})");
                            continue;
                        }

                        $json = $response->json();

                        // Navigate to first organic result
                        $token = $json['results'][0]['content']['results']['organic'][0]['token'] ?? null;

                        if ($token) {
                            $product->product_token = $token;
                            $product->save();
                            $this->info("✅ Saved token for {$product->title}");
                        } else {
                            Log::warning("❌ No token found in response for {$product->title}");
                        }

                        // small pause to prevent spamming Oxylabs
                        usleep(100); // 200ms
                    } catch (\Throwable $e) {
                        Log::error("Exception for {$product->title}: " . $e->getMessage());
                    }
                }
            });

        $this->info("🎯 Migration complete. All tokens updated where available.");
    }
}
