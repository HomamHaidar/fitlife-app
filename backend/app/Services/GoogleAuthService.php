<?php

namespace App\Services;

use Google_Client;
use Illuminate\Support\Facades\Log;

class GoogleAuthService
{
    protected Google_Client $client;

    public function __construct()
    {

        $this->client = new Google_Client(['client_id' => config('services.google.client_id')]);
    }

    public function verifyToken(string $token): ?array
    {
        try {
            $payload = $this->client->verifyIdToken($token);

            if (!$payload) {
                return null;
            }

            return [
                'google_id' => $payload['sub'],
                'email'     => $payload['email'],
                'name'      => $payload['name'],
            ];

        } catch (\Exception $e) {

            Log::error('Google Login Error: ' . $e->getMessage());
            return null;
        }
    }
}
