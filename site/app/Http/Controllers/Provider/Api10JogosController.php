<?php

namespace App\Http\Controllers\Provider;

use App\Http\Controllers\Controller;
use App\Traits\Providers\Api10JogosTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

/**
 * Controller para integração com API 10 Jogos
 * 
 * Este controller gerencia o lançamento de jogos através da API Node.js
 * e fornece endpoints para listar jogos disponíveis
 * 
 * @package App\Http\Controllers\Provider
 */
class Api10JogosController extends Controller
{
    use Api10JogosTrait;

    /**
     * Listar todos os jogos suportados pela API 10 Jogos
     * 
     * GET /api/games/api10jogos/list
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function list()
    {
        try {
            $games = self::Api10JogosGetSupportedGames();

            $gamesList = [];
            foreach ($games as $code => $name) {
                $gamesList[] = [
                    'game_code' => $code,
                    'game_name' => $name,
                    'provider' => 'api10jogos',
                    'provider_name' => 'API 10 Jogos',
                    'type' => 'slot'
                ];
            }

            return response()->json([
                'status' => 'success',
                'total' => count($gamesList),
                'games' => $gamesList
            ]);

        } catch (\Exception $e) {
            Log::error('Api10JogosController List Exception', [
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Erro ao listar jogos'
            ], 500);
        }
    }

    /**
     * Lançar um jogo específico
     * 
     * POST /api/games/api10jogos/launch
     * 
     * Parâmetros esperados:
     * - game_code: Código do jogo (fortune-tiger, fortune-ox, etc)
     * - demo: (opcional) Se "demo", lança em modo demonstração
     * 
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function launch(Request $request)
    {
        try {
            // Validar autenticação
            $user = Auth::guard('api')->user();
            
            if (!$user) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Usuário não autenticado'
                ], 401);
            }

            // Validar parâmetros
            $request->validate([
                'game_code' => 'required|string'
            ]);

            $game_code = $request->input('game_code');
            $demo = $request->input('demo', null);

            Log::info('Api10JogosController Launch Request', [
                'user_id' => $user->id,
                'user_email' => $user->email,
                'game_code' => $game_code,
                'demo' => $demo
            ]);

            // Verificar se o jogo é suportado
            if (!array_key_exists($game_code, self::Api10JogosGetSupportedGames())) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Jogo não suportado',
                    'game_code' => $game_code
                ], 400);
            }

            // Lançar jogo via trait
            $result = self::Api10JogosGameLaunch($game_code, $user->id, $demo);

            if ($result && isset($result['launch_url'])) {
                return response()->json([
                    'status' => 'success',
                    'launch_url' => $result['launch_url'],
                    'game_code' => $game_code,
                    'game_name' => self::Api10JogosGetGameName($game_code),
                    'user_code' => $result['user_code'],
                    'user_balance' => $result['user_balance'],
                    'currency' => $result['currency'],
                    'provider' => 'api10jogos',
                    'user_created' => $result['user_created'] ?? false
                ]);
            }

            return response()->json([
                'status' => 'error',
                'message' => 'Erro ao lançar jogo. Tente novamente.'
            ], 500);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Parâmetros inválidos',
                'errors' => $e->errors()
            ], 422);

        } catch (\Exception $e) {
            Log::error('Api10JogosController Launch Exception', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'user_id' => Auth::guard('api')->id()
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Erro interno ao lançar jogo'
            ], 500);
        }
    }

    /**
     * Lançar jogo diretamente por game_code (para compatibilidade)
     * 
     * GET /api/games/api10jogos/{game_code}
     * 
     * @param string $game_code
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($game_code)
    {
        try {
            // Validar autenticação
            $user = Auth::guard('api')->user();
            
            if (!$user) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Usuário não autenticado'
                ], 401);
            }

            Log::info('Api10JogosController Show Request', [
                'user_id' => $user->id,
                'game_code' => $game_code
            ]);

            // Verificar se o jogo é suportado
            if (!array_key_exists($game_code, self::Api10JogosGetSupportedGames())) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Jogo não encontrado',
                    'game_code' => $game_code
                ], 404);
            }

            // Lançar jogo
            $result = self::Api10JogosGameLaunch($game_code, $user->id, null);

            if ($result && isset($result['launch_url'])) {
                return response()->json([
                    'status' => 'success',
                    'launch_url' => $result['launch_url'],
                    'game_code' => $game_code,
                    'game_name' => self::Api10JogosGetGameName($game_code),
                    'user_code' => $result['user_code'],
                    'user_balance' => $result['user_balance'],
                    'currency' => $result['currency'],
                    'provider' => 'api10jogos'
                ]);
            }

            return response()->json([
                'status' => 'error',
                'message' => 'Erro ao lançar jogo'
            ], 500);

        } catch (\Exception $e) {
            Log::error('Api10JogosController Show Exception', [
                'error' => $e->getMessage(),
                'game_code' => $game_code,
                'user_id' => Auth::guard('api')->id()
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Erro ao carregar jogo'
            ], 500);
        }
    }

    /**
     * Obter informações do agente na API
     * 
     * GET /api/games/api10jogos/agent/info
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function agentInfo()
    {
        try {
            // Apenas administradores podem ver informações do agente
            $user = Auth::guard('api')->user();
            
            if (!$user || !$user->hasRole('admin')) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Não autorizado'
                ], 403);
            }

            $agentInfo = self::Api10JogosGetAgent();

            if ($agentInfo) {
                return response()->json([
                    'status' => 'success',
                    'agent' => $agentInfo
                ]);
            }

            return response()->json([
                'status' => 'error',
                'message' => 'Erro ao obter informações do agente'
            ], 500);

        } catch (\Exception $e) {
            Log::error('Api10JogosController AgentInfo Exception', [
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Erro ao buscar informações'
            ], 500);
        }
    }

    /**
     * Atualizar configurações do agente
     * 
     * POST /api/games/api10jogos/agent/update
     * 
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function agentUpdate(Request $request)
    {
        try {
            // Apenas administradores podem atualizar configurações
            $user = Auth::guard('api')->user();
            
            if (!$user || !$user->hasRole('admin')) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Não autorizado'
                ], 403);
            }

            // Validar parâmetros
            $request->validate([
                'probganho' => 'required|numeric|min:0|max:100',
                'probbonus' => 'required|numeric|min:0|max:100',
                'probganhortp' => 'required|numeric|min:0|max:100',
                'probganhoinfluencer' => 'required|numeric|min:0|max:100',
                'probbonusinfluencer' => 'required|numeric|min:0|max:100',
                'probganhoaposta' => 'required|numeric|min:0|max:100',
                'probganhosaldo' => 'required|numeric|min:0|max:100'
            ]);

            $result = self::Api10JogosUpdateAgent(
                $request->input('probganho'),
                $request->input('probbonus'),
                $request->input('probganhortp'),
                $request->input('probganhoinfluencer'),
                $request->input('probbonusinfluencer'),
                $request->input('probganhoaposta'),
                $request->input('probganhosaldo')
            );

            if ($result) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'Configurações atualizadas com sucesso',
                    'result' => $result
                ]);
            }

            return response()->json([
                'status' => 'error',
                'message' => 'Erro ao atualizar configurações'
            ], 500);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Parâmetros inválidos',
                'errors' => $e->errors()
            ], 422);

        } catch (\Exception $e) {
            Log::error('Api10JogosController AgentUpdate Exception', [
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Erro ao atualizar configurações'
            ], 500);
        }
    }
}
