<?php

use App\Http\Controllers\Provider\Api10JogosController;
use Illuminate\Support\Facades\Route;

/**
 * Rotas para jogos via API 10 Jogos
 * 
 * Prefixo: /api/games/api10jogos
 * Middleware: auth:api (requer autenticação JWT/API)
 * 
 * Endpoints disponíveis:
 * - GET /api/games/api10jogos/list - Listar todos os jogos disponíveis
 * - POST /api/games/api10jogos/launch - Lançar um jogo específico
 * - GET /api/games/api10jogos/{game_code} - Lançar jogo por código
 * - GET /api/games/api10jogos/agent/info - Informações do agente (admin apenas)
 * - POST /api/games/api10jogos/agent/update - Atualizar configurações do agente (admin apenas)
 */

Route::prefix('api10jogos')->middleware(['auth:api'])->group(function () {
    
    // Listar jogos disponíveis
    Route::get('list', [Api10JogosController::class, 'list'])
        ->name('api10jogos.list');
    
    // Lançar jogo via POST
    Route::post('launch', [Api10JogosController::class, 'launch'])
        ->name('api10jogos.launch');
    
    // Gerenciamento do agente (admin apenas)
    Route::prefix('agent')->group(function () {
        Route::get('info', [Api10JogosController::class, 'agentInfo'])
            ->name('api10jogos.agent.info');
        
        Route::post('update', [Api10JogosController::class, 'agentUpdate'])
            ->name('api10jogos.agent.update');
    });
    
    // Lançar jogo por código (compatibilidade)
    Route::get('{game_code}', [Api10JogosController::class, 'show'])
        ->name('api10jogos.show')
        ->where('game_code', '.*');
});
