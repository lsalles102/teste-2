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
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Trait Api10JogosTrait
 * 
 * Integração completa com API 10 Jogos (Node.js)
 * 
 * Este trait fornece métodos para:
 * - Lançar jogos via API
 * - Processar callbacks de saldo
 * - Processar callbacks de transações (apostas/ganhos)
 * - Gerenciar configurações do agente
 * - Validar autenticação de callbacks
 * 
 * @package App\Traits\Providers
 * @author Sistema
 * @version 2.0.0
 */
trait Api10JogosTrait
{
    use MissionTrait;

    /**
     * Credenciais e configurações da API
     */
    protected static $agentToken;
    protected static $secretKey;
    protected static $apiEndpoint;

    /**
     * Mapeamento completo de jogos suportados
     * Código do jogo => Nome amigável
     */
    protected static $supportedGames = [
        'fortune-tiger' => 'Fortune Tiger',
        'fortune-ox' => 'Fortune Ox',
        'fortune-dragon' => 'Fortune Dragon',
        'fortune-rabbit' => 'Fortune Rabbit',
        'fortune-mouse' => 'Fortune Mouse',
        'bikini-paradise' => 'Bikini Paradise',
        'jungle-delight' => 'Jungle Delight',
        'ganesha-gold' => 'Ganesha Gold',
        'double-fortune' => 'Double Fortune',
        'dragon-tiger-luck' => 'Dragon Tiger Luck',
    ];

    /**
     * Configurações de timeout e retry
     */
    protected static $httpTimeout = 30;
    protected static $maxRetries = 3;
    protected static $retryDelay = 1000; // milliseconds

    /* ============================================================================
     * MÉTODOS PRINCIPAIS
     * ============================================================================ */

    /**
     * Obter credenciais da API10Jogos do banco de dados
     * 
     * @return bool
     * @throws \Exception Se as credenciais não forem encontradas
     */
    public static function Api10JogosGetCredential(): bool
    {
        try {
            $setting = GamesKey::first();

            if (!$setting) {
                throw new \Exception('Configurações de games_keys não encontradas');
            }

            self::$agentToken   = $setting->getAttributes()['api10jogos_agent_token'] ?? null;
            self::$secretKey    = $setting->getAttributes()['api10jogos_secret_key'] ?? null;
            self::$apiEndpoint  = $setting->getAttributes()['api10jogos_url'] ?? null;

            // Validar se todas as credenciais estão presentes
            if (empty(self::$agentToken) || empty(self::$secretKey) || empty(self::$apiEndpoint)) {
                throw new \Exception('Credenciais API10Jogos incompletas. Verifique: agent_token, secret_key e api_url');
            }

            return true;

        } catch (\Exception $e) {
            Log::error('Api10Jogos GetCredential Error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return false;
        }
    }

    /**
     * Lançar jogo via API10Jogos
     * 
     * @param string $game_code - Código do jogo (fortune-tiger, fortune-ox, etc)
     * @param int|string $user_id - ID do usuário no sistema Laravel
     * @param string|null $demo - Se 'demo', lança modo demonstração
     * @return array|false - Retorna array com launch_url ou false em caso de erro
     */
    public static function Api10JogosGameLaunch($game_code, $user_id, $demo = null)
    {
        try {
            // Carregar credenciais
            if (!self::Api10JogosGetCredential()) {
                Log::error('Api10Jogos GameLaunch: Falha ao carregar credenciais');
                return false;
            }

            // Validar jogo suportado
            if (!self::Api10JogosIsGameSupported($game_code)) {
                Log::error('Api10Jogos GameLaunch: Jogo não suportado', ['game_code' => $game_code]);
                return false;
            }

            // Buscar usuário
            $user = User::find($user_id);
            if (!$user) {
                Log::error('Api10Jogos GameLaunch: Usuário não encontrado', ['user_id' => $user_id]);
                return false;
            }

            // Obter saldo do usuário
            $user_balance = self::Api10JogosGetUserBalance($user->id);

            // Preparar dados para a API
            $data = [
                "agentToken" => self::$agentToken,
                "secretKey" => self::$secretKey,
                "user_code" => $user->email,
                "game_type" => "slot",
                "provider_code" => "PGSOFT",
                "game_code" => $game_code,
                "user_balance" => floatval($user_balance)
            ];

            Log::info('Api10Jogos GameLaunch Request', [
                'user_id' => $user_id,
                'user_code' => $user->email,
                'game_code' => $game_code,
                'balance' => $user_balance
            ]);

            // Fazer requisição com retry logic
            $response = self::Api10JogosHttpRequest('/api/v1/game_launch', $data);

            if (!$response) {
                return false;
            }

            // Validar resposta
            if (isset($response['status']) && $response['status'] == 1 && isset($response['launch_url'])) {
                Log::info('Api10Jogos GameLaunch Success', [
                    'user_id' => $user_id,
                    'game_code' => $game_code,
                    'user_created' => $response['user_created'] ?? false
                ]);

                return [
                    'launch_url' => $response['launch_url'],
                    'user_code' => $response['user_code'] ?? $user->email,
                    'user_balance' => $response['user_balance'] ?? $user_balance,
                    'currency' => $response['currency'] ?? 'BRL',
                    'user_created' => $response['user_created'] ?? false
                ];
            }

            Log::error('Api10Jogos GameLaunch Invalid Response', ['response' => $response]);
            return false;

        } catch (\Exception $e) {
            Log::error('Api10Jogos GameLaunch Exception', [
                'error' => $e->getMessage(),
                'game_code' => $game_code,
                'user_id' => $user_id
            ]);
            return false;
        }
    }

    /* ============================================================================
     * WEBHOOKS - CALLBACKS DA API
     * ============================================================================ */

    /**
     * Webhook principal para processar callbacks da API
     * 
     * A API Node.js envia callbacks para dois endpoints:
     * 1. gold_api/user_balance - Para consultar saldo do usuário
     * 2. gold_api/game_callback - Para processar transações de jogo
     * 
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public static function Api10JogosWebhook($request)
    {
        $data = $request->all();
        
        Log::info('Api10Jogos Webhook Received', [
            'data' => $data,
            'ip' => $request->ip()
        ]);

        // Verificar tipo de ação
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
     * 
     * Endpoint: POST /api10jogos/gold_api/user_balance
     * 
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public static function Api10JogosGetBalance($request)
    {
        try {
            // Validar autenticação
            if (!self::Api10JogosValidateAuth($request)) {
                Log::warning('Api10Jogos GetBalance: Autenticação inválida', [
                    'ip' => $request->ip()
                ]);
                
                return response()->json([
                    'status' => 0,
                    'user_balance' => 0,
                    'msg' => "UNAUTHORIZED"
                ], 403);
            }

            $user_code = $request->input('user_code');

            if (empty($user_code)) {
                return response()->json([
                    'status' => 0,
                    'user_balance' => 0,
                    'msg' => "MISSING_USER_CODE"
                ], 400);
            }

            // Buscar usuário (por ID ou email)
            $user = User::where('id', $user_code)
                        ->orWhere('email', $user_code)
                        ->first();

            if (!$user) {
                Log::warning('Api10Jogos GetBalance: Usuário não encontrado', [
                    'user_code' => $user_code
                ]);

                return response()->json([
                    'status' => 0,
                    'user_balance' => 0,
                    'msg' => "USER_NOT_FOUND"
                ], 404);
            }

            // Buscar carteira ativa
            $wallet = Wallet::where('user_id', $user->id)
                            ->where('active', 1)
                            ->first();

            if (!$wallet) {
                Log::warning('Api10Jogos GetBalance: Carteira não encontrada', [
                    'user_id' => $user->id
                ]);

                return response()->json([
                    'status' => 0,
                    'user_balance' => 0,
                    'msg' => "WALLET_NOT_FOUND"
                ], 404);
            }

            // Calcular saldo total
            $totalBalance = floatval($wallet->balance) + 
                          floatval($wallet->balance_withdrawal) + 
                          floatval($wallet->balance_bonus);
            $totalBalance = round($totalBalance, 2);

            Log::info('Api10Jogos GetBalance Success', [
                'user_id' => $user->id,
                'user_code' => $user_code,
                'balance' => $totalBalance
            ]);

            return response()->json([
                'status' => 1,
                'user_balance' => $totalBalance
            ], 200);

        } catch (\Exception $e) {
            Log::error('Api10Jogos GetBalance Exception', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'status' => 0,
                'user_balance' => 0,
                'msg' => 'INTERNAL_ERROR'
            ], 500);
        }
    }

    /**
     * Processar callback de jogo (apostas e ganhos)
     * 
     * Endpoint: POST /api10jogos/gold_api/game_callback
     * 
     * Parâmetros esperados:
     * - user_code: ID ou email do usuário
     * - game_code: Código do jogo
     * - transaction_id: ID único da transação
     * - bet_amount: Valor da aposta
     * - win_amount: Valor ganho
     * - game_type: Tipo do jogo (slot, etc)
     * - agent_secret: Chave secreta para validação
     * 
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public static function Api10JogosProcessCallback($request)
    {
        try {
            // Validar autenticação
            if (!self::Api10JogosValidateAuth($request)) {
                Log::warning('Api10Jogos ProcessCallback: Autenticação inválida', [
                    'ip' => $request->ip()
                ]);

                return response()->json([
                    'status' => 0,
                    'msg' => 'UNAUTHORIZED'
                ], 403);
            }

            $data = $request->all();
            
            // Extrair parâmetros
            $user_code = $data['user_code'] ?? null;
            $game_code = $data['game_code'] ?? null;
            $transaction_id = $data['transaction_id'] ?? null;
            $bet_amount = floatval($data['bet_amount'] ?? 0);
            $win_amount = floatval($data['win_amount'] ?? 0);
            $game_type = $data['game_type'] ?? 'slot';

            // Validar parâmetros obrigatórios
            if (!$user_code || !$transaction_id) {
                Log::error('Api10Jogos ProcessCallback: Parâmetros inválidos', $data);

                return response()->json([
                    'status' => 0,
                    'msg' => 'INVALID_PARAMETERS'
                ], 400);
            }

            // Buscar usuário
            $user = User::where('id', $user_code)
                        ->orWhere('email', $user_code)
                        ->first();
            
            if (!$user) {
                Log::error('Api10Jogos ProcessCallback: Usuário não encontrado', [
                    'user_code' => $user_code
                ]);

                return response()->json([
                    'status' => 0,
                    'msg' => 'USER_NOT_FOUND'
                ], 404);
            }

            // Buscar carteira ativa
            $wallet = Wallet::where('user_id', $user->id)
                           ->where('active', 1)
                           ->first();

            if (!$wallet) {
                Log::error('Api10Jogos ProcessCallback: Carteira não encontrada', [
                    'user_id' => $user->id
                ]);

                return response()->json([
                    'status' => 0,
                    'msg' => 'WALLET_NOT_FOUND'
                ], 404);
            }

            // IDEMPOTÊNCIA: Verificar se a transação já foi processada
            $existingTransaction = Order::where('round_id', $transaction_id)
                                        ->where('user_id', $user->id)
                                        ->first();

            if ($existingTransaction) {
                Log::info('Api10Jogos ProcessCallback: Transação já processada', [
                    'transaction_id' => $transaction_id,
                    'user_id' => $user->id
                ]);

                // Retornar sucesso com saldo atual
                return response()->json([
                    'status' => 1,
                    'user_balance' => round(
                        floatval($wallet->balance) + 
                        floatval($wallet->balance_withdrawal) + 
                        floatval($wallet->balance_bonus), 
                        2
                    ),
                    'msg' => 'TRANSACTION_ALREADY_PROCESSED'
                ]);
            }

            Log::info('Api10Jogos ProcessCallback: Processando transação', [
                'user_id' => $user->id,
                'transaction_id' => $transaction_id,
                'bet_amount' => $bet_amount,
                'win_amount' => $win_amount,
                'game_code' => $game_code
            ]);

            // Verificar missões (se o jogo existe)
            $game = Game::where('game_code', $game_code)->first();
            if ($game) {
                self::CheckMissionExist($user->id, $game, 'api10jogos');
            }

            // Processar a transação
            $result = self::Api10JogosProcessTransaction(
                $wallet,
                $user->id,
                $transaction_id,
                $bet_amount,
                $win_amount,
                $game_code
            );

            if ($result) {
                $wallet = $wallet->fresh();
                $newBalance = round(
                    floatval($wallet->balance) + 
                    floatval($wallet->balance_withdrawal) + 
                    floatval($wallet->balance_bonus), 
                    2
                );

                Log::info('Api10Jogos ProcessCallback Success', [
                    'user_id' => $user->id,
                    'transaction_id' => $transaction_id,
                    'new_balance' => $newBalance
                ]);

                return response()->json([
                    'status' => 1,
                    'user_balance' => $newBalance
                ]);
            } else {
                Log::error('Api10Jogos ProcessCallback: Saldo insuficiente', [
                    'user_id' => $user->id,
                    'bet_amount' => $bet_amount,
                    'wallet_balance' => $wallet->balance,
                    'wallet_balance_bonus' => $wallet->balance_bonus,
                    'wallet_balance_withdrawal' => $wallet->balance_withdrawal
                ]);

                return response()->json([
                    'status' => 0,
                    'msg' => 'INSUFFICIENT_USER_FUNDS'
                ], 400);
            }

        } catch (\Exception $e) {
            Log::error('Api10Jogos ProcessCallback Exception', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->all()
            ]);

            return response()->json([
                'status' => 0,
                'msg' => 'INTERNAL_ERROR'
            ], 500);
        }
    }

    /* ============================================================================
     * MÉTODOS DE PROCESSAMENTO INTERNO
     * ============================================================================ */

    /**
     * Processar transação (aposta e ganho)
     * 
     * @param \App\Models\Wallet $wallet
     * @param int $userId
     * @param string $transactionId
     * @param float $betAmount
     * @param float $winAmount
     * @param string $gameCode
     * @return bool
     */
    private static function Api10JogosProcessTransaction($wallet, $userId, $transactionId, $betAmount, $winAmount, $gameCode)
    {
        try {
            $user = User::find($userId);
            $changeBonus = 'balance';

            // Calcular saldo total disponível
            $totalBalance = floatval($wallet->balance) + 
                          floatval($wallet->balance_bonus) + 
                          floatval($wallet->balance_withdrawal);

            // Verificar se há saldo suficiente
            if ($totalBalance < $betAmount) {
                Log::warning('Api10Jogos ProcessTransaction: Saldo insuficiente', [
                    'user_id' => $userId,
                    'bet_amount' => $betAmount,
                    'total_balance' => $totalBalance
                ]);
                return false;
            }

            // ATOMICIDADE: Envolver todas as mutations em uma transação de banco de dados
            return DB::transaction(function () use ($wallet, $userId, $user, $transactionId, $betAmount, $winAmount, $gameCode, &$changeBonus) {

                // Deduzir valor da aposta (ordem de prioridade: bonus > balance > withdrawal)
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
                    // Dedução proporcional de múltiplas carteiras
                    $valorRestante = $betAmount;
                    
                    if ($wallet->balance_bonus > 0) {
                        $deduzir = min($wallet->balance_bonus, $valorRestante);
                        $wallet->decrement('balance_bonus', $deduzir);
                        $valorRestante -= $deduzir;
                        $changeBonus = 'balance_bonus';
                    }
                    
                    if ($valorRestante > 0 && $wallet->balance > 0) {
                        $deduzir = min($wallet->balance, $valorRestante);
                        $wallet->decrement('balance', $deduzir);
                        $valorRestante -= $deduzir;
                        $changeBonus = 'balance';
                    }
                    
                    if ($valorRestante > 0 && $wallet->balance_withdrawal > 0) {
                        $deduzir = min($wallet->balance_withdrawal, $valorRestante);
                        $wallet->decrement('balance_withdrawal', $deduzir);
                        $valorRestante -= $deduzir;
                        $changeBonus = 'balance_withdrawal';
                    }
                    
                    if ($valorRestante > 0.01) {
                        return false; // Ainda não tem saldo suficiente
                    }
                }

                // Criar transação de aposta
                self::Api10JogosCreateTransaction(
                    $userId,
                    $transactionId . '_bet',
                    'bet',
                    $changeBonus,
                    $betAmount,
                    $gameCode,
                    $transactionId
                );

                // Processar ganho (se houver)
                if ($winAmount > 0) {
                    $wallet->increment('balance_withdrawal', $winAmount);
                    
                    self::Api10JogosCreateTransaction(
                        $userId,
                        $transactionId . '_win',
                        'win',
                        'balance_withdrawal',
                        $winAmount,
                        $gameCode,
                        $transactionId
                    );

                    // Registrar no GGR (Gross Gaming Revenue)
                    GGRGamesFiver::create([
                        'user_id'     => $userId,
                        'provider'    => 'api10jogos',
                        'game'        => $gameCode,
                        'balance_bet' => $betAmount,
                        'balance_win' => $winAmount,
                        'currency'    => $wallet->currency ?? 'BRL'
                    ]);

                    Helper::generateGameHistory($user->id, 'win', $winAmount, $betAmount, $changeBonus, $transactionId);

                } else {
                    // Registrar perda no GGR
                    GGRGamesFiver::create([
                        'user_id'     => $userId,
                        'provider'    => 'api10jogos',
                        'game'        => $gameCode,
                        'balance_bet' => $betAmount,
                        'balance_win' => 0,
                        'currency'    => $wallet->currency ?? 'BRL'
                    ]);

                    Helper::lossRollover($wallet, $betAmount);
                    Helper::generateGameHistory($user->id, 'loss', 0, $betAmount, $changeBonus, $transactionId);
                }

                return true;
            }); // Fim da transação DB

        } catch (\Exception $e) {
            Log::error('Api10Jogos ProcessTransaction Exception', [
                'error' => $e->getMessage(),
                'user_id' => $userId,
                'transaction_id' => $transactionId
            ]);
            return false;
        }
    }

    /**
     * Criar registro de transação no banco de dados
     * 
     * @param int $userId
     * @param string $transactionId
     * @param string $type (bet|win)
     * @param string $changeBonus (balance|balance_bonus|balance_withdrawal)
     * @param float $amount
     * @param string $gameCode
     * @param string $originalTransactionId
     * @return \App\Models\Order|false
     */
    private static function Api10JogosCreateTransaction($userId, $transactionId, $type, $changeBonus, $amount, $gameCode, $originalTransactionId)
    {
        try {
            $order = Order::create([
                'user_id'        => $userId,
                'session_id'     => time(),
                'transaction_id' => $transactionId,
                'type'           => $type,
                'type_money'     => $changeBonus,
                'amount'         => round(floatval($amount), 2),
                'providers'      => 'api10jogos',
                'game'           => $gameCode,
                'game_uuid'      => $gameCode,
                'round_id'       => $originalTransactionId,
            ]);

            return $order;

        } catch (\Exception $e) {
            Log::error('Api10Jogos CreateTransaction Exception', [
                'error' => $e->getMessage(),
                'user_id' => $userId,
                'transaction_id' => $transactionId
            ]);
            return false;
        }
    }

    /* ============================================================================
     * MÉTODOS DE GERENCIAMENTO DO AGENTE
     * ============================================================================ */

    /**
     * Obter informações do agente na API
     * 
     * @return array|false
     */
    public static function Api10JogosGetAgent()
    {
        try {
            if (!self::Api10JogosGetCredential()) {
                return false;
            }

            $data = [
                "agentToken" => self::$agentToken,
                "secretKey" => self::$secretKey
            ];

            $response = self::Api10JogosHttpRequest('/api/v1/getagent', $data);

            if ($response) {
                Log::info('Api10Jogos GetAgent Success', ['agent_data' => $response]);
                return $response;
            }

            return false;

        } catch (\Exception $e) {
            Log::error('Api10Jogos GetAgent Exception', ['error' => $e->getMessage()]);
            return false;
        }
    }

    /**
     * Atualizar configurações do agente (probabilidades, RTP, etc)
     * 
     * @param float $probganho
     * @param float $probbonus
     * @param float $probganhortp
     * @param float $probganhoinfluencer
     * @param float $probbonusinfluencer
     * @param float $probganhoaposta
     * @param float $probganhosaldo
     * @return array|false
     */
    public static function Api10JogosUpdateAgent($probganho, $probbonus, $probganhortp, $probganhoinfluencer, $probbonusinfluencer, $probganhoaposta, $probganhosaldo)
    {
        try {
            if (!self::Api10JogosGetCredential()) {
                return false;
            }

            $data = [
                "agentToken" => self::$agentToken,
                "secretKey" => self::$secretKey,
                "probganho" => floatval($probganho),
                "probbonus" => floatval($probbonus),
                "probganhortp" => floatval($probganhortp),
                "probganhoinfluencer" => floatval($probganhoinfluencer),
                "probbonusinfluencer" => floatval($probbonusinfluencer),
                "probganhoaposta" => floatval($probganhoaposta),
                "probganhosaldo" => floatval($probganhosaldo)
            ];

            $response = self::Api10JogosHttpRequest('/api/v1/attagent', $data);

            if ($response) {
                Log::info('Api10Jogos UpdateAgent Success', ['response' => $response]);
                return $response;
            }

            return false;

        } catch (\Exception $e) {
            Log::error('Api10Jogos UpdateAgent Exception', ['error' => $e->getMessage()]);
            return false;
        }
    }

    /* ============================================================================
     * MÉTODOS AUXILIARES
     * ============================================================================ */

    /**
     * Validar autenticação do callback
     * 
     * Verifica se o agent_secret fornecido é válido
     * 
     * @param \Illuminate\Http\Request $request
     * @return bool
     */
    private static function Api10JogosValidateAuth($request): bool
    {
        try {
            if (!self::Api10JogosGetCredential()) {
                return false;
            }
            
            // Aceitar secret via body ou header
            $agent_secret = $request->input('agent_secret') ?? $request->header('X-Agent-Secret');
            
            if (empty($agent_secret)) {
                Log::warning('Api10Jogos ValidateAuth: Missing agent_secret', [
                    'ip' => $request->ip(),
                    'url' => $request->fullUrl()
                ]);
                return false;
            }
            
            if ($agent_secret !== self::$secretKey) {
                Log::error('Api10Jogos ValidateAuth: Invalid agent_secret', [
                    'ip' => $request->ip(),
                    'provided_secret' => substr($agent_secret, 0, 10) . '...',
                    'url' => $request->fullUrl()
                ]);
                return false;
            }
            
            return true;

        } catch (\Exception $e) {
            Log::error('Api10Jogos ValidateAuth Exception', ['error' => $e->getMessage()]);
            return false;
        }
    }

    /**
     * Fazer requisição HTTP com retry logic
     * 
     * @param string $endpoint
     * @param array $data
     * @param int $attempt
     * @return array|false
     */
    private static function Api10JogosHttpRequest($endpoint, $data, $attempt = 1)
    {
        try {
            $url = rtrim(self::$apiEndpoint, '/') . $endpoint;

            $response = Http::timeout(self::$httpTimeout)
                           ->withOptions([
                               'curl' => [
                                   CURLOPT_IPRESOLVE => CURL_IPRESOLVE_V4,
                               ],
                           ])
                           ->post($url, $data);

            if ($response->successful()) {
                return $response->json();
            }

            // Se falhou e ainda tem tentativas, fazer retry
            if ($attempt < self::$maxRetries) {
                usleep(self::$retryDelay * 1000);
                return self::Api10JogosHttpRequest($endpoint, $data, $attempt + 1);
            }

            Log::error('Api10Jogos HttpRequest Failed', [
                'endpoint' => $endpoint,
                'status' => $response->status(),
                'body' => $response->body(),
                'attempts' => $attempt
            ]);

            return false;

        } catch (\Exception $e) {
            // Se falhou e ainda tem tentativas, fazer retry
            if ($attempt < self::$maxRetries) {
                usleep(self::$retryDelay * 1000);
                return self::Api10JogosHttpRequest($endpoint, $data, $attempt + 1);
            }

            Log::error('Api10Jogos HttpRequest Exception', [
                'error' => $e->getMessage(),
                'endpoint' => $endpoint,
                'attempts' => $attempt
            ]);

            return false;
        }
    }

    /**
     * Verificar se o jogo é suportado
     * 
     * @param string $game_code
     * @return bool
     */
    private static function Api10JogosIsGameSupported($game_code): bool
    {
        return array_key_exists($game_code, self::$supportedGames);
    }

    /**
     * Obter saldo total do usuário
     * 
     * @param int $user_id
     * @return float
     */
    private static function Api10JogosGetUserBalance($user_id): float
    {
        try {
            $wallet = Wallet::where('user_id', $user_id)
                           ->where('active', 1)
                           ->first();

            if (!$wallet) {
                return 0.00;
            }

            $totalBalance = floatval($wallet->balance) + 
                          floatval($wallet->balance_withdrawal) + 
                          floatval($wallet->balance_bonus);

            return round($totalBalance, 2);

        } catch (\Exception $e) {
            Log::error('Api10Jogos GetUserBalance Exception', [
                'error' => $e->getMessage(),
                'user_id' => $user_id
            ]);
            return 0.00;
        }
    }

    /**
     * Obter lista de jogos suportados
     * 
     * @return array
     */
    public static function Api10JogosGetSupportedGames(): array
    {
        return self::$supportedGames;
    }

    /**
     * Obter nome amigável do jogo
     * 
     * @param string $game_code
     * @return string
     */
    public static function Api10JogosGetGameName($game_code): string
    {
        return self::$supportedGames[$game_code] ?? $game_code;
    }
}
