<?php

namespace App\Http\Controllers\Webhooks;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Wallet;

class PgWebhookController extends Controller
{
    public function userBalance(Request $request)
    {
        $userCode = $request->input('user_code');
        if (empty($userCode)) {
            return response()->json(['msg' => 'INVALID_USER']);
        }

        $user = User::where('email', $userCode)->first();
        if (!$user && str_starts_with($userCode, 'user_')) {
            $id = (int) str_replace('user_', '', $userCode);
            if ($id > 0) {
                $user = User::find($id);
            }
        }
        if (!$user) {
            return response()->json(['msg' => 'INVALID_USER']);
        }

        $wallet = Wallet::where('user_id', $user->id)->first();
        $balance = $wallet ? (float) $wallet->total_balance : 0.0;

        if ($balance <= 0) {
            return response()->json(['msg' => 'INSUFFICIENT_USER_FUNDS']);
        }

        return response()->json([
            'msg' => 'OK',
            'balance' => $balance,
        ]);
    }

    public function gameCallback(Request $request)
    {
        $payload = $request->all();
        $userCode = $payload['user_code'] ?? null;
        $slot = $payload['slot'] ?? [];
        $bet = (float) ($slot['bet'] ?? 0);
        $win = (float) ($slot['win'] ?? 0);

        if (!$userCode) {
            return response()->json(['status' => 'error', 'message' => 'user_code missing'], 400);
        }

        $user = User::where('email', $userCode)->first();
        if (!$user && str_starts_with($userCode, 'user_')) {
            $id = (int) str_replace('user_', '', $userCode);
            if ($id > 0) {
                $user = User::find($id);
            }
        }
        if (!$user) {
            return response()->json(['status' => 'error', 'message' => 'user not found'], 404);
        }

        $wallet = Wallet::firstOrCreate(['user_id' => $user->id]);

        $delta = $win - $bet;
        $wallet->balance = max(0, (float) ($wallet->balance ?? 0) + $delta);
        $wallet->total_bet = (float) ($wallet->total_bet ?? 0) + $bet;
        if ($win > 0) {
            $wallet->total_won = (float) ($wallet->total_won ?? 0) + $win;
            $wallet->last_won = $win;
        } else {
            $wallet->total_lose = (float) ($wallet->total_lose ?? 0) + $bet;
            $wallet->last_lose = $bet;
        }
        $wallet->save();

        return response()->json([
            'status' => 'success',
            'user_code' => $userCode,
            'new_balance' => $wallet->balance,
        ]);
    }

    public function moneyCallback(Request $request)
    {
        // Callback financeiro opcional – aceitar e retornar sucesso
        return response()->json(['status' => 'ok']);
    }
}


