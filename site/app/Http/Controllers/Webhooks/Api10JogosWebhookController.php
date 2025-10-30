<?php

namespace App\Http\Controllers\Webhooks;

use App\Http\Controllers\Controller;
use App\Traits\Providers\Api10JogosTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class Api10JogosWebhookController extends Controller
{
    use Api10JogosTrait;

    /**
     * Callback para consulta de saldo do usuário
     * Endpoint: POST /api10jogos/gold_api/user_balance
     */
    public function userBalance(Request $request)
    {
        Log::info('Api10Jogos - User Balance Request', ['data' => $request->all()]);
        
        return self::Api10JogosGetBalance($request);
    }

    /**
     * Callback para processar transações de jogo
     * Endpoint: POST /api10jogos/gold_api/game_callback
     */
    public function gameCallback(Request $request)
    {
        Log::info('Api10Jogos - Game Callback Request', ['data' => $request->all()]);
        
        return self::Api10JogosProcessCallback($request);
    }

    /**
     * Webhook genérico (caso a API envie em um único endpoint)
     * Endpoint: POST /api10jogos/webhook
     */
    public function webhook(Request $request)
    {
        Log::info('Api10Jogos - Webhook Request', ['data' => $request->all()]);
        
        return self::Api10JogosWebhook($request);
    }
}
