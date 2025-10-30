<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class PgApiService
{
    public static function launchGame(string $userCode, string $gameCode, float $userBalance): array
    {
        $base = rtrim(config('services.pgapi.base'), '/');
        $agentToken = config('services.pgapi.agent_token');
        $secretKey = config('services.pgapi.secret_key');

        if (empty($base) || empty($agentToken) || empty($secretKey)) {
            return [
                'status' => 'error',
                'message' => 'PG API credentials not configured',
            ];
        }

        try {
            $response = Http::timeout(10)->withHeaders([
                'Content-Type' => 'application/json',
            ])->post($base . '/api/v1/game_launch', [
                'agentToken' => $agentToken,
                'secretKey' => $secretKey,
                'user_code' => $userCode,
                'game_type' => 'slot',
                'provider_code' => 'PGSOFT',
                'game_code' => $gameCode,
                'user_balance' => $userBalance,
            ]);

            if (!$response->ok()) {
                return [
                    'status' => 'error',
                    'message' => 'PG API unreachable',
                    'http_status' => $response->status(),
                ];
            }

            $json = $response->json();
            if (isset($json['status']) && (int)$json['status'] === 1 && !empty($json['launch_url'])) {
                return [
                    'status' => 'success',
                    'launch_url' => $json['launch_url'],
                    'raw' => $json,
                ];
            }

            return [
                'status' => 'error',
                'message' => $json['message'] ?? 'Unknown error launching game',
                'raw' => $json,
            ];
        } catch (\Throwable $e) {
            return [
                'status' => 'error',
                'message' => $e->getMessage(),
            ];
        }
    }
}


