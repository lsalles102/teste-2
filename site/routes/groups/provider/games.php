<?php

use App\Http\Controllers\Api\Games\GameController;
use App\Http\Controllers\Webhooks\PgWebhookController;
use Illuminate\Support\Facades\Route;


// PG API callbacks (compatível com sua API Node)
Route::post('gold_api/game_callback', [PgWebhookController::class, 'gameCallback']);
Route::post('gold_api/money_callback', [PgWebhookController::class, 'moneyCallback']);
Route::post('gold_api/user_balance', [PgWebhookController::class, 'userBalance']);