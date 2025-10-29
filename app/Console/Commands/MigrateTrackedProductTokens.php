<?php

namespace App\Console\Commands;

use App\Models\GoogleProduct;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class MigrateTrackedProductTokens extends Command
{
    protected $signature = 'migrate:tracked-product-tokens {--concurrency=20} {--batch=100}';
    protected $description = 'Migrate Oxylabs product tokens with concurrent cURL, cross-platform progress, and retry for failed products.';

    // Oxylabs endpoint and timeouts
    protected string $oxylabsUrl = 'https://realtime.oxylabs.io/v1/queries';
    protected int $httpTimeout = 60; // seconds

    // where to save final failed IDs
    protected string $failedSavePath = 'oxylabs_failed_products.json';

    public function handle()
    {
        $concurrency = (int) $this->option('concurrency') ?: 20;
        $batchSize = (int) $this->option('batch') ?: 100;

        $username = env('OXYLABS_USERNAME');
        $password = env('OXYLABS_PASSWORD');

        if (empty($username) || empty($password)) {
            $this->error('OXYLABS_USERNAME or OXYLABS_PASSWORD not set in .env');
            return 1;
        }

        $authHeader = 'Basic ' . base64_encode("{$username}:{$password}");

        $this->info("🚀 Starting concurrent migration: concurrency={$concurrency}, batch={$batchSize}");

        $total = GoogleProduct::whereNull('product_token')
            ->whereNotNull('product_id')
            ->whereHas('tracked_products')
            ->count();

        if ($total === 0) {
            $this->info("No products need migration.");
            return 0;
        }

        $processed = 0;
        $failedProducts = [];


        GoogleProduct::whereNull('product_token')
            ->whereNotNull('product_id')
            ->whereHas('tracked_products')
            ->chunkById($batchSize, function ($chunk) use (&$processed, &$failedProducts, $concurrency, $authHeader, $total) {

                $multiHandle = curl_multi_init();
                $handles = [];

                foreach ($chunk as $product) {
                    $payload = json_encode([
                        'source' => 'google_shopping_search',
                        'query' => $product->title,
                        'geo_location' => $product->country ?? 'Australia',
                        'domain' => 'com',
                        'parse' => true,
                        'pages' => 1,
                    ]);

                    $ch = curl_init($this->oxylabsUrl);
                    curl_setopt_array($ch, [
                        CURLOPT_RETURNTRANSFER => true,
                        CURLOPT_POST => true,
                        CURLOPT_POSTFIELDS => $payload,
                        CURLOPT_HTTPHEADER => [
                            'Content-Type: application/json',
                            'Authorization: ' . $authHeader,
                        ],
                        CURLOPT_TIMEOUT => $this->httpTimeout,
                    ]);

                    $handles[(string) $product->id] = ['ch' => $ch, 'product' => $product];
                    curl_multi_add_handle($multiHandle, $ch);


                    if (count($handles) >= $concurrency) {
                        $this->execMulti($multiHandle, $handles, $processed, $total, $failedProducts);
                    }
                }


                if (!empty($handles)) {
                    $this->execMulti($multiHandle, $handles, $processed, $total, $failedProducts);
                }

                curl_multi_close($multiHandle);
            });


        $MAX_RETRIES = 1;
        if (!empty($failedProducts)) {
            $this->info("\n🔁 Retrying failed products ({count} items) — up to {$MAX_RETRIES} attempts");

            $retryMap = array_fill_keys($failedProducts, 0);

            for ($attempt = 1; $attempt <= $MAX_RETRIES && count($retryMap) > 0; $attempt++) {
                $this->info("Retry attempt {$attempt} — items remaining: " . count($retryMap));


                $ids = array_keys($retryMap);
                $chunksOfIds = array_chunk($ids, $batchSize);
                $newRetryMap = [];

                foreach ($chunksOfIds as $idsChunk) {
                    $multiHandle = curl_multi_init();
                    $handles = [];

                    // load products by ids
                    $products = GoogleProduct::whereIn('id', $idsChunk)->get()->keyBy('id');

                    foreach ($idsChunk as $id) {
                        $gp = $products[$id] ?? null;
                        if (!$gp) {
                            // product may have been deleted, skip
                            continue;
                        }

                        $payload = json_encode([
                            'source' => 'google_shopping_search',
                            'query' => $gp->title,
                            'geo_location' => $gp->country ?? 'Australia',
                            'domain' => 'com',
                            'pages'=> 1,
                            'parse' => true,
                        ]);

                        $ch = curl_init($this->oxylabsUrl);
                        curl_setopt_array($ch, [
                            CURLOPT_RETURNTRANSFER => true,
                            CURLOPT_POST => true,
                            CURLOPT_POSTFIELDS => $payload,
                            CURLOPT_HTTPHEADER => [
                                'Content-Type: application/json',
                                'Authorization: ' . $authHeader,
                            ],
                            CURLOPT_TIMEOUT => $this->httpTimeout,
                        ]);

                        $handles[(string) $gp->id] = ['ch' => $ch, 'product' => $gp];
                        curl_multi_add_handle($multiHandle, $ch);

                        if (count($handles) >= $concurrency) {
                            $this->execMulti($multiHandle, $handles, $processed, $total, $newRetryMap);
                        }
                    }

                    if (!empty($handles)) {
                        $this->execMulti($multiHandle, $handles, $processed, $total, $newRetryMap);
                    }
                    curl_multi_close($multiHandle);
                }

                $retryMap = array_fill_keys($newRetryMap, 0);
                $this->info("Attempt {$attempt} complete — remaining: " . count($retryMap));
            }


            $finalFailed = array_keys($retryMap);
            if (!empty($finalFailed)) {

                // Storage::put($this->failedSavePath, json_encode($finalFailed));
                $this->error("⚠️ Some products still failed after retries. Saved list to storage/{$this->failedSavePath}");
                Log::warning('Oxylabs migration - final failed products', ['ids' => $finalFailed]);
            } else {
                $this->info("✅ All previously failed products were recovered on retry.");

                // if (Storage::exists($this->failedSavePath)) {
                //     Storage::delete($this->failedSavePath);
                // }
            }
        }

        $this->info("\n🎯 Migration finished. Processed: {$processed} / {$total}");
        return 0;
    }

    /**
     * Execute the multi handle, collect results, and update $processed and $failedProducts (by reference).
     *
     * @param resource $multiHandle
     * @param array &$handles
     * @param int &$processed
     * @param int $total
     * @param array &$failedProducts (will collect failed IDs)
     * @return void
     */
    private function execMulti($multiHandle, array &$handles, int &$processed, int $total, array &$failedProducts)
    {

        do {
            $status = curl_multi_exec($multiHandle, $running);

            curl_multi_select($multiHandle, 1.0);
        } while ($running > 0 && $status === CURLM_OK);

        foreach ($handles as $id => $data) {
            $product = $data['product'];
            $ch = $data['ch'];

            $response = curl_multi_getcontent($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_multi_remove_handle($multiHandle, $ch);
            curl_close($ch);

            $processed++;

            if ($httpCode === 200 && $response) {
                $json = json_decode($response, true);
                $token = $json['results'][0]['content']['results']['organic'][0]['token'] ?? null;

                if ($token) {
                    try {
                        $product->update(['product_token' => $token]);
                        $this->line("<fg=green>[OK]</> {$processed}/{$total} | Saved token for {$product->title}");
                    } catch (\Throwable $e) {
                        Log::error("DB save failed for {$product->id}: " . $e->getMessage());
                        $failedProducts[] = (string) $product->id;
                        $this->line("<fg=red>[ERR]</> {$processed}/{$total} | DB save failed for {$product->title}");
                    }
                } else {
                    Log::warning("No token found for {$product->id} (HTTP 200)");
                    $failedProducts[] = (string) $product->id;
                    $this->line("<fg=red>[ERR]</> {$processed}/{$total} | No token found for {$product->title}");
                }
            } else {
                Log::error("HTTP {$httpCode} for {$product->id}: " . substr((string) $response, 0, 2000));
                $failedProducts[] = (string) $product->id;
                $this->line("<fg=red>[ERR]</> {$processed}/{$total} | HTTP {$httpCode} for {$product->title}");
            }

            $this->printProgress("{$processed}/{$total}");
        }


        $handles = [];
    }

    /**
     * Print a single-line blue progress indicator (cross-platform).
     */
    private function printProgress(string $text)
    {

        $cols = 120;
        $line = mb_substr($text, 0, $cols - 1);

        echo "\r\033[34m{$line}\033[0m\033[K";
        flush();
    }
}
