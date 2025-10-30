<?php

namespace App\Traits\Providers;

use App\Helpers\Core as Helper;
use App\Models\Game;
use App\Models\GamesKey;
use App\Models\GGRGames;
use App\Models\GGRGamesFiver;
use App\Models\Order;
use App\Models\User;
use App\Models\Wallet;
use App\Traits\Missions\MissionTrait;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

trait Api10JogosTrait
{
    use MissionTrait;

    protected static $agentToken;
    protected static $secretKey;
    protected static $apiEndpoint;

    /**
     * Obter credenciais da API10Jogos
     */
    public static function Api10JogosGetCredential(): bool
    {
        $setting = GamesKey::first();

        self::$agentToken   = $setting->getAttributes()['api10jogos_agent_token'];
        self::$secretKey    = $setting->getAttributes()['api10jogos_secret_key'];
        self::$apiEndpoint  = $setting->getAttributes()['api10jogos_url'];

        return true;
    }

    /**
     * Lançar jogo via API10Jogos
     * @param string $game_code - Código do jogo (fortune-tiger, fortune-ox, etc)
     * @param string $user_code - ID ou email do usuário
     * @param float $user_balance - Saldo do usuário
     */
    public static function Api10JogosGameLaunch($game_code, $user_code, $user_balance)
    {
        self::Api10JogosGetCredential();

        $data = [
            "agentToken" => self::$agentToken,
            "secretKey" => self::$secretKey,
            "user_code" => $user_code,
            "game_type" => "slot",
            "provider_code" => "PGSOFT",
            "game_code" => $game_code,
            "user_balance" => floatval($user_balance)
        ];

        try {
            $response = Http::timeout(30)->post(self::$apiEndpoint . '/launch_game', $data);

            if ($response->successful()) {
                $result = $response->json();
                
                if (isset($result['status']) && $result['status'] == 1) {
                    return $result;
                }
                
                Log::error('Api10Jogos GameLaunch Error', ['response' => $result]);
                return false;
            }

            Log::error('Api10Jogos GameLaunch HTTP Error', [
                'status' => $response->status(),
                'body' => $response->body()
            ]);
            return false;

        } catch (\Exception $e) {
            Log::error('Api10Jogos GameLaunch Exception', ['error' => $e->getMessage()]);
            return false;
        }
    }

    /**
     * Webhook principal para processar callbacks da API
     */
    public static function Api10JogosWebhook($request)
    {
        // A API envia callbacks para dois endpoints:
        // 1. gold_api/user_balance - Para consultar saldo
        // 2. gold_api/game_callback - Para processar transações
        
        $data = $request->all();
        Log::info('Api10Jogos Webhook Received', ['data' => $data]);

        // Verificar se é uma solicitação de saldo ou callback de jogo
        if (isset($data['action']) && $data['action'] === 'user_balance') {
            return self::Api10JogosGetBalance($request);
        } elseif (isset($data['action']) && $data['action'] === 'game_callback') {
            return self::Api10JogosProcessCallback($request);
        }

        return response()->json([
            'status' => 0,
            'msg' => 'INVALID_ACTION'
        ], 400);
    }

    /**
     * Retornar saldo do usuário
     */
    public static function Api10JogosGetBalance($request)
    {
        $user_code = $request->input('user_code');
        $user = User::where('id', $user_code)->orWhere('email', $user_code)->first();

        if (!$user) {
            return response()->json([
                'status' => 0,
                'user_balance' => 0,
                'msg' => "USER_NOT_FOUND"
            ], 404);
        }

        $wallet = Wallet::where('user_id', $user->id)
                        ->where('active', 1)
                        ->first();

        if (!empty($wallet)) {
            $totalBalance = floatval($wallet->balance) + floatval($wallet->balance_withdrawal) + floatval($wallet->balance_bonus);
            $totalBalance = round($totalBalance, 2);

            if ($totalBalance > 0) {
                return response()->json([
                    'status' => 1,
                    'user_balance' => $totalBalance
                ], 200);
            }
        }

        return response()->json([
            'status' => 0,
            'user_balance' => 0,
            'msg' => "INSUFFICIENT_USER_FUNDS"
        ], 200);
    }

    /**
     * Processar callback de jogo (apostas e ganhos)
     */
    public static function Api10JogosProcessCallback($request)
    {
        $data = $request->all();
        
        $user_code = $data['user_code'] ?? null;
        $game_code = $data['game_code'] ?? null;
        $transaction_id = $data['transaction_id'] ?? null;
        $bet_amount = floatval($data['bet_amount'] ?? 0);
        $win_amount = floatval($data['win_amount'] ?? 0);
        $game_type = $data['game_type'] ?? 'slot';

        if (!$user_code || !$transaction_id) {
            return response()->json([
                'status' => 0,
                'msg' => 'INVALID_PARAMETERS'
            ], 400);
        }

        $user = User::where('id', $user_code)->orWhere('email', $user_code)->first();
        
        if (!$user) {
            return response()->json([
                'status' => 0,
                'msg' => 'USER_NOT_FOUND'
            ], 404);
        }

        $wallet = Wallet::where('user_id', $user->id)->where('active', 1)->first();

        if (empty($wallet)) {
            return response()->json([
                'status' => 0,
                'msg' => 'WALLET_NOT_FOUND'
            ], 404);
        }

        // Verificar se a transação já existe
        $existingTransaction = Order::where('transaction_id', $transaction_id)->first();
        if ($existingTransaction) {
            return response()->json([
                'status' => 1,
                'user_balance' => $wallet->total_balance,
                'msg' => 'TRANSACTION_ALREADY_PROCESSED'
            ]);
        }

        // Processar a transação
        $game = Game::where('game_code', $game_code)->first();
        
        if ($game) {
            self::CheckMissionExist($user->id, $game, 'api10jogos');
        }

        $result = self::Api10JogosProcessTransaction(
            $wallet,
            $user->id,
            $transaction_id,
            $bet_amount,
            $win_amount,
            $game_code
        );

        if ($result) {
            return response()->json([
                'status' => 1,
                'user_balance' => $wallet->fresh()->total_balance
            ]);
        } else {
            return response()->json([
                'status' => 0,
                'msg' => 'INSUFFICIENT_USER_FUNDS'
            ], 400);
        }
    }

    /**
     * Processar transação (aposta e ganho)
     */
    private static function Api10JogosProcessTransaction($wallet, $userId, $transactionId, $betAmount, $winAmount, $gameCode)
    {
        $user = User::find($userId);
        $changeBonus = 'balance';

        // Deduzir valor da aposta
        if ($wallet->balance_bonus >= $betAmount) {
            $wallet->decrement('balance_bonus', $betAmount);
            $changeBonus = 'balance_bonus';
        } elseif ($wallet->balance >= $betAmount) {
            $wallet->decrement('balance', $betAmount);
            $changeBonus = 'balance';
        } elseif ($wallet->balance_withdrawal >= $betAmount) {
            $wallet->decrement('balance_withdrawal', $betAmount);
            $changeBonus = 'balance_withdrawal';
        } else {
            return false; // Saldo insuficiente
        }

        // Criar transação de aposta
        self::Api10JogosCreateTransaction(
            $userId,
            $transactionId . '_bet',
            'bet',
            $changeBonus,
            $betAmount,
            $gameCode
        );

        // Se houver ganho, processar
        if ($winAmount > 0) {
            $wallet->increment('balance_withdrawal', $winAmount);
            
            self::Api10JogosCreateTransaction(
                $userId,
                $transactionId . '_win',
                'win',
                'balance_withdrawal',
                $winAmount,
                $gameCode
            );

            // Registrar no GGR
            GGRGamesFiver::create([
                'user_id'     => $userId,
                'provider'    => 'api10jogos',
                'game'        => $gameCode,
                'balance_bet' => $betAmount,
                'balance_win' => $winAmount,
                'currency'    => $wallet->currency
            ]);

            Helper::generateGameHistory($user->id, 'win', $winAmount, $betAmount, $changeBonus, $transactionId);
        } else {
            // Perda
            GGRGamesFiver::create([
                'user_id'     => $userId,
                'provider'    => 'api10jogos',
                'game'        => $gameCode,
                'balance_bet' => $betAmount,
                'balance_win' => 0,
                'currency'    => $wallet->currency
            ]);

            Helper::lossRollover($wallet, $betAmount);
            Helper::generateGameHistory($user->id, 'loss', 0, $betAmount, $changeBonus, $transactionId);
        }

        return true;
    }

    /**
     * Criar registro de transação
     */
    private static function Api10JogosCreateTransaction($userId, $transactionId, $type, $changeBonus, $amount, $gameCode)
    {
        $order = Order::create([
            'user_id'        => $userId,
            'session_id'     => time(),
            'transaction_id' => $transactionId,
            'type'           => $type,
            'type_money'     => $changeBonus,
            'amount'         => floatval($amount),
            'providers'      => 'api10jogos',
            'game'           => $gameCode,
            'game_uuid'      => $gameCode,
            'round_id'       => 1,
        ]);

        return $order ? $order : false;
    }

    /**
     * Obter informações do agente
     */
    public static function Api10JogosGetAgent()
    {
        self::Api10JogosGetCredential();

        $data = [
            "agentToken" => self::$agentToken,
            "secretKey" => self::$secretKey
        ];

        try {
            $response = Http::timeout(30)->post(self::$apiEndpoint . '/get_agent', $data);

            if ($response->successful()) {
                return $response->json();
            }

            return false;
        } catch (\Exception $e) {
            Log::error('Api10Jogos GetAgent Exception', ['error' => $e->getMessage()]);
            return false;
        }
    }

    /**
     * Atualizar configurações do agente (probabilidades, RTP, etc)
     */
    public static function Api10JogosUpdateAgent($probganho, $probbonus, $probganhortp, $probganhoinfluencer, $probbonusinfluencer, $probganhoaposta, $probganhosaldo)
    {
        self::Api10JogosGetCredential();

        $data = [
            "agentToken" => self::$agentToken,
            "secretKey" => self::$secretKey,
            "probganho" => $probganho,
            "probbonus" => $probbonus,
            "probganhortp" => $probganhortp,
            "probganhoinfluencer" => $probganhoinfluencer,
            "probbonusinfluencer" => $probbonusinfluencer,
            "probganhoaposta" => $probganhoaposta,
            "probganhosaldo" => $probganhosaldo
        ];

        try {
            $response = Http::timeout(30)->post(self::$apiEndpoint . '/update_agent', $data);

            if ($response->successful()) {
                return $response->json();
            }

            return false;
        } catch (\Exception $e) {
            Log::error('Api10Jogos UpdateAgent Exception', ['error' => $e->getMessage()]);
            return false;
        }
    }
}
