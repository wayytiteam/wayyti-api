<?php

namespace App\Services;

use Firebase\JWT\JWT;
use Illuminate\Support\Facades\File;

class AppStoreJwtService
{
    protected string $private_key;

    protected string $key_id;

    protected string $team_id;

    protected string $bundle_id;

    protected string $issuer_id;

    public function __construct()
    {
        $this->key_id = config('services.apple.key_id');
        $this->team_id = config('services.apple.team_id');
        $this->bundle_id = config('services.apple.bundle_id');
        $this->issuer_id = config('services.apple.issuer_id');
        $private_key_path = config('services.apple.private_key');

        if (! File::exists($private_key_path)) {
            throw new \RuntimeException('Apple private key (.p8) file not found');
        }

        $this->private_key = File::get($private_key_path);
    }

    /**
     * Generate App Store Server API JWT
     */
    public function generateToken(): string
    {
        $now = time();

        $payload = [
            'iss' => $this->issuer_id,
            'iat' => $now,
            'exp' => $now + (20 * 60), // max 20 minutes
            'aud' => 'appstoreconnect-v1',
            'bid' => $this->bundle_id,
        ];

        $headers = [
            'alg' => 'ES256',
            'kid' => $this->key_id,
            'typ' => 'JWT',
        ];

        return JWT::encode(
            $payload,
            $this->private_key,
            'ES256',
            $this->key_id,
            $headers
        );
    }

    /**
     * Decode signedTransactionInfo payload (no verification)
     */
    public function decodeToken(string $jwt): object
    {
        $parts = explode('.', $jwt);

        if (count($parts) !== 3) {
            throw new \InvalidArgumentException('Invalid JWT format');
        }

        return json_decode(
            base64_decode(strtr($parts[1], '-_', '+/'))
        );
    }
}
