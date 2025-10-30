<?php

use App\Http\Controllers\Webhooks\Api10JogosWebhookController;
use Illuminate\Support\Facades\Route;

/**
 * Rotas para integração com API 10 Jogos
 * 
 * A API Node.js fará callbacks para estes endpoints:
 * - /api10jogos/gold_api/user_balance - Para consultar saldo do usuário
 * - /api10jogos/gold_api/game_callback - Para processar transações de jogo
 */

Route::prefix('api10jogos')->group(function () {
    Route::post('gold_api/user_balance', [Api10JogosWebhookController::class, 'userBalance']);
    Route::post('gold_api/game_callback', [Api10JogosWebhookController::class, 'gameCallback']);
    Route::post('webhook', [Api10JogosWebhookController::class, 'webhook']);
});
