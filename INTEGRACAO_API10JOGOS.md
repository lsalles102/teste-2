# Integração API 10 Jogos - Documentação Completa

## Visão Geral

Este documento descreve a integração completa entre o site Laravel e a API de jogos Node.js (API 10 Jogos).

## Arquitetura da Integração

```
┌──────────────────┐         ┌──────────────────┐
│                  │         │                  │
│   Laravel Site   │◄───────►│   API Node.js    │
│   (port 5000)    │         │   (port 3000)    │
│                  │         │                  │
└──────────────────┘         └──────────────────┘
         │                            │
         │                            │
         ▼                            ▼
┌──────────────────┐         ┌──────────────────┐
│  PostgreSQL DB   │         │   MySQL/Redis    │
│  (Laravel)       │         │   (API Interna)  │
└──────────────────┘         └──────────────────┘
```

## Componentes Principais

### 1. Trait: `Api10JogosTrait`

**Localização:** `site/app/Traits/Providers/Api10JogosTrait.php`

**Responsabilidades:**
- Gerenciar autenticação com a API Node.js
- Lançar jogos via API
- Processar callbacks de saldo
- Processar callbacks de transações (apostas/ganhos)
- Gerenciar configurações do agente
- Validar autenticação de callbacks

**Métodos Públicos Principais:**

```php
// Lançar jogo
Api10JogosGameLaunch($game_code, $user_id, $demo = null)

// Processar webhook de saldo
Api10JogosGetBalance($request)

// Processar webhook de transação
Api10JogosProcessCallback($request)

// Obter informações do agente
Api10JogosGetAgent()

// Atualizar configurações do agente
Api10JogosUpdateAgent($probganho, $probbonus, ...)

// Obter jogos suportados
Api10JogosGetSupportedGames()
```

### 2. Controller: `Api10JogosController`

**Localização:** `site/app/Http/Controllers/Provider/Api10JogosController.php`

**Endpoints:**

#### Listar Jogos
```http
GET /api/games/api10jogos/list
Authorization: Bearer {token}

Response:
{
  "status": "success",
  "total": 10,
  "games": [
    {
      "game_code": "fortune-tiger",
      "game_name": "Fortune Tiger",
      "provider": "api10jogos",
      "provider_name": "API 10 Jogos",
      "type": "slot"
    },
    ...
  ]
}
```

#### Lançar Jogo (POST)
```http
POST /api/games/api10jogos/launch
Authorization: Bearer {token}
Content-Type: application/json

Body:
{
  "game_code": "fortune-tiger",
  "demo": null
}

Response:
{
  "status": "success",
  "launch_url": "https://m.pg-nmgaa.com/126/index.html?...",
  "game_code": "fortune-tiger",
  "game_name": "Fortune Tiger",
  "user_code": "user@example.com",
  "user_balance": 100.50,
  "currency": "BRL",
  "provider": "api10jogos",
  "user_created": false
}
```

#### Lançar Jogo (GET)
```http
GET /api/games/api10jogos/fortune-tiger
Authorization: Bearer {token}

Response: (mesmo formato do POST)
```

#### Informações do Agente (Admin)
```http
GET /api/games/api10jogos/agent/info
Authorization: Bearer {admin_token}

Response:
{
  "status": "success",
  "agent": {
    "id": 1,
    "probganho": 50,
    "probbonus": 30,
    ...
  }
}
```

#### Atualizar Agente (Admin)
```http
POST /api/games/api10jogos/agent/update
Authorization: Bearer {admin_token}
Content-Type: application/json

Body:
{
  "probganho": 50,
  "probbonus": 30,
  "probganhortp": 95,
  "probganhoinfluencer": 40,
  "probbonusinfluencer": 20,
  "probganhoaposta": 60,
  "probganhosaldo": 70
}

Response:
{
  "status": "success",
  "message": "Configurações atualizadas com sucesso",
  "result": { ... }
}
```

### 3. Webhook Controller: `Api10JogosWebhookController`

**Localização:** `site/app/Http/Controllers/Webhooks/Api10JogosWebhookController.php`

**Endpoints de Callback (sem autenticação JWT):**

#### Consultar Saldo
```http
POST /api10jogos/gold_api/user_balance
Content-Type: application/json

Body:
{
  "user_code": "user@example.com",
  "agent_secret": "YOUR_SECRET_KEY"
}

Response:
{
  "status": 1,
  "user_balance": 150.75
}
```

#### Processar Transação
```http
POST /api10jogos/gold_api/game_callback
Content-Type: application/json

Body:
{
  "user_code": "user@example.com",
  "game_code": "fortune-tiger",
  "transaction_id": "unique_txn_id_123",
  "bet_amount": 10.00,
  "win_amount": 25.00,
  "game_type": "slot",
  "agent_secret": "YOUR_SECRET_KEY"
}

Response:
{
  "status": 1,
  "user_balance": 165.75
}
```

## Jogos Suportados

A integração suporta os seguintes jogos PG Soft:

| Código do Jogo      | Nome do Jogo       | ID na API |
|---------------------|--------------------|-----------| 
| fortune-tiger       | Fortune Tiger      | 126       |
| fortune-ox          | Fortune Ox         | 98        |
| fortune-dragon      | Fortune Dragon     | 1695365   |
| fortune-rabbit      | Fortune Rabbit     | 1543462   |
| fortune-mouse       | Fortune Mouse      | 68        |
| bikini-paradise     | Bikini Paradise    | 69        |
| jungle-delight      | Jungle Delight     | 40        |
| ganesha-gold        | Ganesha Gold       | 42        |
| double-fortune      | Double Fortune     | 48        |
| dragon-tiger-luck   | Dragon Tiger Luck  | 63        |

## Fluxo de Integração

### 1. Lançamento de Jogo

```
Usuario -> Laravel -> API Node.js -> Retorna URL do jogo
                          ↓
                    Cria/Atualiza user_code
                          ↓
                    Retorna launch_url
```

**Passos:**
1. Usuário autenticado faz requisição para lançar jogo
2. Laravel chama API Node.js via endpoint `/api/v1/game_launch`
3. API Node.js:
   - Valida credenciais (agentToken + secretKey)
   - Cria ou atualiza usuário interno
   - Retorna URL do jogo com parâmetros
4. Laravel retorna URL para o frontend

### 2. Consulta de Saldo

```
API Node.js -> Laravel Webhook -> Consulta DB -> Retorna Saldo
```

**Passos:**
1. API Node.js faz callback para `/api10jogos/gold_api/user_balance`
2. Laravel valida `agent_secret`
3. Busca usuário por `user_code` (ID ou email)
4. Calcula saldo total (balance + balance_withdrawal + balance_bonus)
5. Retorna saldo formatado

### 3. Processamento de Transação

```
API Node.js -> Laravel Webhook -> Processa Aposta -> Atualiza Saldo
                                         ↓
                                   Registra GGR
                                         ↓
                                  Verifica Missões
```

**Passos:**
1. API Node.js envia callback para `/api10jogos/gold_api/game_callback`
2. Laravel valida `agent_secret`
3. Verifica idempotência (transaction_id já processado?)
4. Deduz aposta do saldo do usuário
5. Se houver ganho, adiciona ao `balance_withdrawal`
6. Registra transação na tabela `orders`
7. Registra estatísticas no GGR (Gross Gaming Revenue)
8. Verifica e atualiza missões do usuário
9. Retorna novo saldo

## Segurança

### Validação de Callbacks

Todos os callbacks da API Node.js são validados através do `agent_secret`:

```php
private static function Api10JogosValidateAuth($request): bool
{
    $agent_secret = $request->input('agent_secret') ?? 
                    $request->header('X-Agent-Secret');
    
    return $agent_secret === self::$secretKey;
}
```

### Idempotência

O sistema garante que transações não sejam processadas duas vezes:

```php
$existingTransaction = Order::where('round_id', $transaction_id)
                            ->where('user_id', $user->id)
                            ->first();

if ($existingTransaction) {
    return response()->json([
        'status' => 1,
        'user_balance' => $wallet->total_balance,
        'msg' => 'TRANSACTION_ALREADY_PROCESSED'
    ]);
}
```

## Configuração

### 1. Banco de Dados

Adicione as credenciais na tabela `games_keys`:

```sql
INSERT INTO games_keys (id, api10jogos_agent_token, api10jogos_secret_key, api10jogos_url) 
VALUES (
    1,
    'SEU_AGENT_TOKEN',
    'SEU_SECRET_KEY',
    'http://localhost:3000'
);
```

### 2. API Node.js

Configure o callback URL no banco de dados da API Node.js para apontar para:
- **Saldo:** `https://seu-site.com/api10jogos/gold_api/user_balance`
- **Transação:** `https://seu-site.com/api10jogos/gold_api/game_callback`

### 3. Variáveis de Ambiente

A API Node.js usa as seguintes variáveis (arquivo `.env` na pasta `api/`):

```env
PORT=3000
GAME_DOMAIN=m.pg-nmgaa.com
```

## Tratamento de Erros

### Retry Logic

O trait implementa retry automático para chamadas HTTP:

```php
private static function Api10JogosHttpRequest($endpoint, $data, $attempt = 1)
{
    // ... faz requisição ...
    
    // Se falhou e ainda tem tentativas
    if ($attempt < self::$maxRetries) {
        usleep(self::$retryDelay * 1000);
        return self::Api10JogosHttpRequest($endpoint, $data, $attempt + 1);
    }
}
```

### Logs

Todos os erros e eventos importantes são logados:

```php
Log::info('Api10Jogos GameLaunch Success', [...]);
Log::error('Api10Jogos GameLaunch Exception', [...]);
Log::warning('Api10Jogos ValidateAuth: Missing agent_secret', [...]);
```

## Testes

### Teste de Lançamento de Jogo

```bash
curl -X POST https://seu-site.com/api/games/api10jogos/launch \
  -H "Authorization: Bearer SEU_TOKEN_JWT" \
  -H "Content-Type: application/json" \
  -d '{
    "game_code": "fortune-tiger"
  }'
```

### Teste de Callback de Saldo

```bash
curl -X POST http://localhost:5000/api10jogos/gold_api/user_balance \
  -H "Content-Type: application/json" \
  -d '{
    "user_code": "user@example.com",
    "agent_secret": "SEU_SECRET_KEY"
  }'
```

### Teste de Callback de Transação

```bash
curl -X POST http://localhost:5000/api10jogos/gold_api/game_callback \
  -H "Content-Type: application/json" \
  -d '{
    "user_code": "user@example.com",
    "game_code": "fortune-tiger",
    "transaction_id": "test_123",
    "bet_amount": 10.00,
    "win_amount": 25.00,
    "agent_secret": "SEU_SECRET_KEY"
  }'
```

## Monitoramento

### Métricas Importantes

1. **Taxa de sucesso de lançamento**: Verificar logs de `Api10JogosGameLaunch`
2. **Tempo de resposta**: Monitorar tempo de resposta da API Node.js
3. **Erros de autenticação**: Logs de `Api10JogosValidateAuth`
4. **Transações duplicadas**: Logs de `TRANSACTION_ALREADY_PROCESSED`
5. **Saldo insuficiente**: Logs de `INSUFFICIENT_USER_FUNDS`

### Dashboard Sugerido

- Total de jogos lançados (hoje/mês)
- Total de apostas processadas
- GGR (Gross Gaming Revenue)
- Taxa de ganho dos jogadores
- Jogos mais populares

## Melhorias Futuras

1. **Cache de credenciais**: Usar Cache::remember para credenciais da API
2. **Rate Limiting**: Implementar rate limiting nos endpoints de callback
3. **Webhook Signature**: Adicionar assinatura HMAC nos webhooks
4. **Queue para callbacks**: Processar callbacks assíncronos via filas
5. **Métricas em tempo real**: Dashboard com estatísticas em tempo real
6. **Multi-moeda**: Suporte a múltiplas moedas
7. **Modo sandbox**: Ambiente de testes isolado

## Suporte

Para problemas ou dúvidas sobre a integração:

1. Verificar logs em `storage/logs/laravel.log`
2. Verificar logs da API Node.js
3. Validar configurações no banco `games_keys`
4. Verificar conectividade entre Laravel e API Node.js

## Changelog

### Versão 2.0.0 (Atual)
- ✅ Reescrita completa do trait com melhorias
- ✅ Adicionado controller específico para API 10 Jogos
- ✅ Implementado retry logic automático
- ✅ Melhor tratamento de erros e validações
- ✅ Documentação completa em português
- ✅ Suporte a todos os 10 jogos PG Soft
- ✅ Idempotência de transações
- ✅ Logs estruturados
- ✅ Métodos auxiliares para consultas

### Versão 1.0.0 (Anterior)
- ✅ Implementação básica do trait
- ✅ Webhook controller
- ✅ Integração básica com API Node.js
