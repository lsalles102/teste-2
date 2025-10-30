# Projeto: Sistema de Cassino Online com API 10 Jogos

## Visão Geral
Este projeto é um sistema completo de cassino online integrado com uma API de jogos PG Soft (Fortune Tiger, Fortune Ox, Fortune Rabbit, etc). Consiste em duas partes principais:

1. **Site Laravel** - Frontend/Backend em Laravel 10 + Vue.js 3
2. **API 10 Jogos** - API Node.js/TypeScript que provê os jogos

## Arquitetura do Projeto

### Estrutura de Pastas
```
/
├── api/          # API Node.js/TypeScript (Provedor de Jogos)
│   ├── src/      # Código-fonte TypeScript
│   ├── dist/     # Código compilado JavaScript
│   └── public/   # Assets dos jogos
└── site/         # Laravel + Vue.js (Site Principal)
    ├── app/      # Código Laravel
    ├── resources/# Frontend Vue.js
    └── routes/   # Rotas da aplicação
```

### Tecnologias Utilizadas

#### Site Laravel
- **Backend**: Laravel 10 (PHP 8.2)
- **Frontend**: Vue.js 3 + Vite
- **Database**: SQLite (pode ser migrado para PostgreSQL/MySQL)
- **Autenticação**: JWT (Tymon/JWT-Auth)
- **UI**: Tailwind CSS + Flowbite

#### API 10 Jogos
- **Runtime**: Node.js 20
- **Linguagem**: TypeScript
- **Framework**: Express.js
- **Database**: MySQL2
- **WebSockets**: Socket.io
- **SSL**: HTTPS com certificados auto-assinados

## Integração API 10 Jogos (Versão 2.0.0 - Completa)

### Como Funciona
A integração foi implementada através de uma arquitetura profissional e completa:
- **Trait `Api10JogosTrait`** - Lógica central de integração com validações, retry logic e transações atômicas
- **Controller `Api10JogosController`** - Endpoints RESTful para lançar jogos
- **Webhook Controller** - Processa callbacks da API (saldo e transações)
- **Documentação completa** - INTEGRACAO_API10JOGOS.md com todos os detalhes

#### Fluxo de Integração
1. **Usuário faz login** no site Laravel
2. **Laravel consulta saldo** do usuário
3. **Usuário clica em um jogo** (ex: Fortune Tiger)
4. **Laravel chama API** `/launch_game` com:
   - `agentToken`: Token de autenticação
   - `secretKey`: Chave secreta
   - `user_code`: ID do usuário
   - `game_code`: Código do jogo (fortune-tiger, fortune-ox, etc)
   - `user_balance`: Saldo atual
5. **API retorna URL do jogo** que é exibida em iframe
6. **Durante o jogo**, a API faz callbacks para o Laravel:
   - `/api10jogos/gold_api/user_balance` - Consultar saldo
   - `/api10jogos/gold_api/game_callback` - Processar apostas/ganhos

### Configuração da Integração

#### 1. Configurar Credenciais no Admin do Laravel
Acesse o painel admin do Laravel (Filament) e configure:
- **api10jogos_agent_token**: Token do agente
- **api10jogos_secret_key**: Chave secreta
- **api10jogos_url**: URL da API (ex: `https://seu-dominio.com`)

#### 2. Configurar Callback URL na API
A API precisa saber a URL do Laravel para fazer callbacks:
```sql
UPDATE agents SET callbackurl = 'https://seu-site-laravel.com/' WHERE id = 1;
```

#### 3. Jogos Disponíveis
- fortune-tiger (126)
- fortune-ox (98)
- fortune-dragon (1695365)
- fortune-rabbit (1543462)
- fortune-mouse (68)
- bikini-paradise (69)
- jungle-delight (40)
- ganesha-gold (42)
- double-fortune (48)
- dragon-tiger-luck (63)

### Endpoints da Integração

#### Endpoints Laravel (Autenticados - Requerem JWT)
```http
GET  /api/games/api10jogos/list           - Listar todos os jogos disponíveis
POST /api/games/api10jogos/launch         - Lançar um jogo específico
GET  /api/games/api10jogos/{game_code}    - Lançar jogo por código
GET  /api/games/api10jogos/agent/info     - Info do agente (admin)
POST /api/games/api10jogos/agent/update   - Atualizar config (admin)
```

#### Webhooks Laravel (Callbacks da API Node.js - Sem JWT, validação via agent_secret)
```http
POST /api10jogos/gold_api/user_balance    - Consultar saldo do usuário
POST /api10jogos/gold_api/game_callback   - Processar transações (apostas/ganhos)
POST /api10jogos/webhook                  - Webhook genérico
```

#### API Node.js (Endpoints Internos)
```http
POST /api/v1/game_launch    - Lança um jogo e retorna URL
POST /api/v1/getagent       - Obtém informações do agente
POST /api/v1/attagent       - Atualiza configurações (RTP, probabilidades)
```

## Workflows Configurados

### 1. Laravel Site (Porta 5000)
- **Comando**: `cd site && php artisan serve --host=0.0.0.0 --port=5000`
- **Tipo**: Webview (Frontend visível ao usuário)
- **Porta**: 5000
- **Status**: ✅ Running

### 2. API 10 Jogos (Porta 3000)
- **Comando**: `cd api && PORT=3000 node dist/index.js`
- **Tipo**: Console (Backend API)
- **Portas**: 3000 (HTTP) e 443 (HTTPS)
- **Status**: ✅ Running

## Desenvolvimento

### Instalar Dependências

#### Laravel
```bash
cd site
composer install
npm install
```

#### API
```bash
cd api
npm install
```

### Migrations
```bash
cd site
php artisan migrate --force
```

A migration `2024_10_30_000001_add_api10jogos_to_games_keys_table` adiciona os campos necessários para a integração.

### Compilar Assets Frontend
```bash
cd site
npm run build  # Produção
npm run dev    # Desenvolvimento
```

### Compilar TypeScript da API
```bash
cd api
npm run build
```

## Arquivos Principais da Integração (Versão 2.0.0)

### Arquivos Criados/Modificados

#### Trait (Reescrito Completamente)
- `site/app/Traits/Providers/Api10JogosTrait.php` 
  - ✅ Validação completa de credenciais
  - ✅ Retry logic automático (3 tentativas)
  - ✅ Transações atômicas de banco de dados
  - ✅ Idempotência de transações
  - ✅ Logs estruturados
  - ✅ Suporte a todos os 10 jogos PG Soft
  - ✅ Métodos auxiliares completos

#### Controller (Novo)
- `site/app/Http/Controllers/Provider/Api10JogosController.php`
  - ✅ Endpoints RESTful autenticados
  - ✅ Validação de entrada
  - ✅ Controle de acesso (admin-only para agent)
  - ✅ Respostas estruturadas JSON

#### Webhook Controller
- `site/app/Http/Controllers/Webhooks/Api10JogosWebhookController.php`
  - ✅ Processa callbacks da API Node.js
  - ✅ Validação de agent_secret

#### Rotas
- `site/routes/groups/api/games/api10jogos.php` - Rotas autenticadas (novo)
- `site/routes/groups/provider/api10jogos.php` - Rotas de webhooks (existente)
- `site/routes/api.php` - Inclusão das rotas de jogos (modificado)

#### Documentação Completa
- `INTEGRACAO_API10JOGOS.md` 
  - ✅ Arquitetura detalhada
  - ✅ Exemplos de uso de todos os endpoints
  - ✅ Guia de configuração
  - ✅ Testes com curl
  - ✅ Troubleshooting

#### Model
- `site/app/Models/GamesKey.php` - Com campos api10jogos (já existia)

## Banco de Dados

### Configuração Atual
- **Tipo**: PostgreSQL (Replit Neon) ou SQLite
- **Arquivo SQLite**: `site/database/database.sqlite`
- **PostgreSQL**: Disponível via DATABASE_URL

### ⚠️ Importante: Migrations Limpas
O projeto original contém migrations com erros estruturais. Para evitar problemas:

**Use o caminho limpo de migrations criado em `database/migrations/api10jogos/`**

```bash
# Resetar banco e rodar apenas migrations essenciais
cd site
php artisan migrate:fresh --path=database/migrations/api10jogos --force

# Popular com dados de teste
php artisan db:seed --class=Api10JogosSeeder --force
```

Este caminho contém apenas as tabelas essenciais para a integração API 10 Jogos funcionar:
- users, wallets, orders, games, games_keys, settings, currencies, sessions, etc.

### Configurar .env para SQLite
```env
DB_CONNECTION=sqlite
# O Laravel automaticamente usa database/database.sqlite
```

### Configurar .env para PostgreSQL Replit
```env
DB_CONNECTION=pgsql
DATABASE_URL="${DATABASE_URL}"
# As variáveis PGHOST, PGPORT, etc são fornecidas automaticamente pelo Replit
```

### Tabelas Principais
- `users` - Usuários do sistema
- `wallets` - Carteiras dos usuários
- `games_keys` - Credenciais das APIs de jogos
- `orders` - Transações (apostas/ganhos)
- `ggr_games_fivers` - Histórico GGR (Gross Gaming Revenue)

## Segurança

### JWT Authentication
- Token gerado: ✅
- Usado para autenticação API

### SSL/HTTPS
- API usa HTTPS (porta 443)
- Certificados auto-assinados gerados em `api/`

### Secrets
- Nunca commitar credenciais
- Usar `.env` para configurações sensíveis

## Troubleshooting

### Erro: "no such table: settings"
Execute todas as migrations:
```bash
cd site
php artisan migrate:fresh --force
```

### API não inicia
1. Verificar se certificados SSL existem em `api/`:
```bash
cd api
ls -la server.key server.crt
```
2. Se não existirem, gerar:
```bash
openssl req -x509 -newkey rsa:4096 -keyout server.key -out server.crt -days 365 -nodes -subj "/C=BR/ST=State/L=City/O=Organization/CN=localhost"
```

### Laravel não encontra .env
```bash
cd site
cp .env.example .env
php artisan key:generate
php artisan jwt:secret
```

## Próximos Passos

1. **Configurar Banco de Dados de Produção**
   - Criar PostgreSQL ou MySQL
   - Rodar todas as migrations

2. **Configurar Credenciais da API**
   - Acessar admin do Laravel
   - Preencher tokens e URLs

3. **Testar Integração Completa**
   - Lançar um jogo
   - Fazer uma aposta
   - Verificar callbacks no log

4. **Configurar Deployment**
   - Usar Deploy Config para publicar

## Suporte

Criado em: 30/10/2025
Linguagem: Português (BR)
Framework: Laravel 10 + Vue.js 3 + Node.js/TypeScript
