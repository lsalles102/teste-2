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

## Integração API 10 Jogos

### Como Funciona
A integração foi implementada através do **trait `Api10JogosTrait`** que permite ao Laravel comunicar-se com a API de jogos.

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

### Endpoints da API

#### API 10 Jogos (Node.js)
```
POST /launch_game
- Lança um jogo e retorna URL

POST /get_agent
- Obtém informações do agente

POST /update_agent
- Atualiza configurações (RTP, probabilidades)
```

#### Laravel (Callbacks)
```
POST /api10jogos/gold_api/user_balance
- Retorna saldo do usuário

POST /api10jogos/gold_api/game_callback
- Processa transações (apostas/ganhos)
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

## Arquivos Principais da Integração

### Trait Principal
- `site/app/Traits/Providers/Api10JogosTrait.php` - Toda lógica de integração

### Controller
- `site/app/Http/Controllers/Webhooks/Api10JogosWebhookController.php` - Processa callbacks

### Rotas
- `site/routes/groups/provider/api10jogos.php` - Rotas dos callbacks

### Migration
- `site/database/migrations/2024_10_30_000001_add_api10jogos_to_games_keys_table.php`

### Model
- `site/app/Models/GamesKey.php` - Atualizado com campos api10jogos

## Banco de Dados

### Configuração Atual
- **Tipo**: SQLite
- **Arquivo**: `site/database/database.sqlite`

### Migrar para PostgreSQL/MySQL
1. Criar banco de dados
2. Atualizar `.env`:
```env
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=seu_banco
DB_USERNAME=seu_usuario
DB_PASSWORD=sua_senha
```
3. Rodar migrations: `php artisan migrate --force`

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
